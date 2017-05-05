'use strict';

const Utils = require('./utils');
const http = require('http');
const assert = require('assert');
const net = require('net');
const Connection = require('./connection');
const Promise = require('../modules/bluebird');
const config = require('./config');
const google = require('../modules/google2');
google.resultsPerPage = config.resultsPerPage;


class Home {
  static init() {
    this._msgQueue = [];
    this._connect();
    this._msgHandler = {
      'die': this._handleDie.bind(this),
      'testIP': this._testIP.bind(this),
      'search': this._search.bind(this)
    };
    this._searchQueue = [];
    this._searching = false;
    setInterval(() => {
      if (this._searching) {
	return;
      }
      if (this._searchQueue.length === 0) {
	return;
      }
      this._searching = true;
      const msg = this._searchQueue.shift();
      assert(msg);
      const ip = msg.ip;
      const keyword = msg.keyword;
      google.requestOptions = {
	//timeout: 30000,
	localAddress: ip
      };
      google(keyword, (err, res) => {
	this._searching = false;
	if (err) {
	  console.log(err);
	  console.log(err.toString());
	  const str = err.toString();
	  if (str.indexOf('captcha') !== -1) {
	    this.sendMsg({
	      respID: msg.id,
	      err: 'captcha'
	    });
	    return;
	  }
	  this.sendMsg({
	    respID: msg.id,
	    err: err.toString()
	  });
	  return;
	}
	const arr = [];
	for (let item of res.links) {
	  const link = item.href || item.link;
	  if (!link) {
	    continue;
	  }
	  arr.push(link);
	}
	this.sendMsg({
	  respID: msg.id,
	  links: arr	  
	});
      });
      

    }, 100);
  }

  static _search(msg) {
    this._searchQueue.push(msg);
  }

  static _testIP(msg) {
    console.log(msg);
    console.log(123);
    //https://api.ipify.org
    const ip = msg.ip;
    const options = {
      hostname: 'api.ipify.org',
      localAddress: ip
    };
    const req = http.request(options, (resp) => {
      console.log(resp);
      let data = '';
      resp.on('data', d => {
        console.log('1');
        data += d.toString();
      });
      resp.on('end', () => {
        console.log('2');
	console.log(ip);
        this.sendMsg({
          respID: msg.id,
          ip: data.trim()
        });
      });
    });

    req.on('error', err => {
      console.log(err);
      process.exit(0);
    });
    req.end();
  }

  static _handleDie(msg) {
    if (this._con) {
      this._con.die();
    }
    process.exit(0);
  }

  static sendDebugMsg(msg) {
    assert(!msg.type);
    msg.type = 'debug';
    this.sendMsg(msg);
  }

  static sendMsg(msg, cb) {
    if (this._con) {
      this._con.sendMsg(msg, cb);
      return;
    }
    this._msgQueue.push({
      msg: msg,
      cb: cb
    });
  }

  static _onConnect(sock) {
    assert(sock.remoteAddress);
    this._ips = Utils.getIPS();
    this._con = new Connection(sock);
    this._con.sendMsg({
      type: 'hello',
      ips: this._ips,
      botID: config.globals.botID,
      pass: config.pass,
      version: config.globals.version,
      rand: config.globals.rand,
      bdate: config.globals.bdate
    });

    this._con.setKeepAlive(20); // todo: +- random ?
    for (let i = 0; i < this._msgQueue.length; i++) {
      const {msg, cb} = this._msgQueue[i];
      this._con.sendMsg(msg, cb);
    }
    this._msgQueue = [];
    for (let key in this._msgHandler) {
      if (this._msgHandler.hasOwnProperty(key)) {
        const val = this._msgHandler[key];
        this._con.setMsgHandler(key, val);
      }
    }
  }

  static _connect(cb = Function.prototype) {
    console.log('connecting');
    assert(!this._con || !this._con.isAlive());
    const sock = net.connect(config.globals.port, config.globals.host, () => {
      console.log('connected');
      sock.removeListener('close', onCanNotConnect);
      sock.removeListener('timeout', onTimeoutBeforeConnect);

      sock.on('close', () => {
        console.log('closed');
        this._con = null;
        console.log('closed');
        setTimeout(() => {
          this._connect(cb);
        }, 1000);
      });
      this._onConnect(sock);
      cb();
    });

    sock.on('close', onCanNotConnect);

    sock.on('error', err => {
      console.log('error');
      console.log(err);
    });

    sock.setTimeout(10 * 1000, onTimeoutBeforeConnect);

    function onTimeoutBeforeConnect() {
      sock.destroy();
    }
    const me = this;
    function onCanNotConnect() {
      setTimeout(() => {
        console.log('can not connect');
        me._connect(cb);
      }, 1000);
    }
  }
};


module.exports = Home;
