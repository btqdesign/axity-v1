'use strict';

const Worker = require('./worker');
const cp = require('child_process');
const util = require('util');
const net = require('net');
const config = require('./config');
const fs = require('fs');
const Err = require('./errors');
const assert = require('assert');
const Connection = require('./connection');

let con = null;

class HomeCon extends Connection {
  constructor(sock) {
    super(sock);
    this._onConnect();
    this._msgHandler = {
      'die': this._handleDie.bind(this),
      'checkHost': this._checkHost.bind(this),
      'checkHostC': this._checkHostC.bind(this),
      'countEntries': this._countEntries.bind(this),
      'getPythonVersion': this._getPythonVersion.bind(this),
      'checkSQLMap': this._checkSQLMap.bind(this),
      'checkDarkSql': this._checkDarkSql.bind(this)
      
    };
  }


  _checkSQLMap(msg) {
    Worker.checkSQLMapAsync()
      .then(success => {
	this._sendResp(msg, {
	  success: success
	});
      });
  }

  _countEntries(msg) {
    const host = msg.host;
    Worker.countEntriesAsync(host)
      .then(resp => {
	console.log('%s entries', resp);
	this._sendResp(msg, {
	  n: resp
	});
      })
      .catch(errObj => {
	const err = errObj.err;
	const stdout = errObj.stdout;
	const stderr = errObj.stderr;

	this._sendResp(msg, {
	  err: err,
	  stdout: stdout,
	  stderr: stderr
	});
      });
  }

  _checkDarkSql(msg) {
    const url = msg.url;
    Worker.checkDarkSqlAsync(url)
      .then(url => {
	console.log(url);
	this._sendResp(msg, {
	  url
	});
      })
      .catch(err => {
	this._sendResp(msg, {
	  err: err.toString()
	});
      });
  }

  _checkHostC(msg) {
    const host = msg.host;
    const technique = msg.technique;
    const args = msg.args;
    const postData = msg.postData;

    console.log(host);
    console.log(technique);
    console.log(args);

    Worker.checkHostCAsync(host, postData, technique, args)
      .then(resp => {
	console.log(resp);
	this._sendResp(msg, {
	  resp: resp
	});
      })
      .catch(err => {
	this._sendResp(msg, {
	  err: err.toJSON()
	});
      });

  }

  _checkHost(msg) {
    const host = msg.host;
    const technique = msg.technique;
    const args = msg.args;
    const postData = msg.postData;

    console.log(host);
    console.log(technique);
    console.log(args);
    Worker.checkHostAsync(host, postData, technique, args)
      .then(resp => {
	console.log(resp);
	this._sendResp(msg, {
	  resp: resp
	});
      })
      .catch(err => {
	this._sendResp(msg, {
	  err: err.toJSON()
	});
      });

  }

  _sendResp(msg, resp) {
    assert(!resp.id);
    assert(!resp.type);
    assert(msg.id);
    assert(msg.type);

    resp.id = msg.id;
    resp.type = msg.type;

    this.sendMsg(resp);
  }

  _getPythonVersion(msg) {
    Worker.getPythonVersionAsync()
      .then(resp => {
	this._sendResp(msg, {
	  version: resp
	});
      })
      .catch(err => {
	console.log(err);
	this._sendResp(msg, {
	  err: err
	});
      });
  }

  _handleDie(msg) {
    if (con) {
      con.die();
    }
    process.exit(0);
  }

  _getVersion() {
    const content = fs.readFileSync('version.txt');
    const version = content.toString().trim();
    return version;
  }

  _onConnect() {
    console.log('removing output dir');
    Worker.killPython();
    Worker.removeOutputDir();
    console.log('done');    
    this.sendMsg({
      type: 'hello',
      botID: config.globals.botID,
      pass: config.pass
    });
  }

  sendDebugMsg(msg) {
    msg.type = 'DEBUG';
    this.sendMsg(msg);
  }

  die() {
    if (!this.isAlive()) {
      return;
    }
    this.sendMsg({
      type: 'dying'
    });
    super.die();
  }

  handleMsg(msg) {
    const handler = this._msgHandler[msg.type];
    if (!handler) {
      console.log('Invalid msg');
      console.log(msg);
      this.sendDebugMsg({
	str: 'invalid msg',
	type: msg.type,
	msg: JSON.stringify(msg)
      });
      return;
    }
    handler(msg);
  }
}

function _connect(cb) {
  const host = config.globals.host;
  const port = config.globals.port;

  if (con && con.isAlive()) {
    console.log('already connected');
    cb('already connected');
    return;
  }

  const sock = net.connect(port, host, () => {
    cb();
    sock.removeListener('close', onCanNotConnect);
    sock.removeListener('timeout', onTimeoutBeforeConnect);

    console.log('connected');
    con = new HomeCon(sock);
    con.setKeepAlive(10 * 60);
    sock.on('close', () => {
      Worker.killPython();
      Worker.removeOutputDir();
      con.onClose();
      setTimeout(() => {
	_connect(function() { });
      }, 1000);
    });
    sock.on('end', () => {
      if (con) {
	con.onEnd();
      }
    });
    sock.on('error', err => {
      if (con) {
	con.onError(err);
      }
    });

    sock.on('data', data => {
      if (con) {
	con.onData(data.toString());
      }
    });
  });

  sock.on('close', onCanNotConnect);
  sock.on('error', err => {
    console.log('error');
    console.log(err);
  });

  sock.setTimeout(10 * 1000, onTimeoutBeforeConnect);

  function onCanNotConnect() {
    setTimeout(() => {
      _connect(function(){});
    }, 1000);
  }

  function onTimeoutBeforeConnect() {
    sock.destroy();
  }

}

function connect(host, port, cb) {
  if (!host || !port) {
    cb(util.format('Invalid address: %s:%s', host, port));
    console.log('Invalid address: %s:%s', host, port);
    return;
  }
  if (con) {
    con.die();
    con = null;
  }
  _connect(cb);
}

function isConnected() {
  return con && con.isAlive();
}

function sendDebugMsg(msg) {
  if (!con || !con.isAlive()) {
    return;
  }
  con.sendDebugMsg(msg);
}

function sendMsg(msg, cb) {
  if (!con || !con.isAlive()) {
    cb();
    return;
  }
  con.sendMsg(msg, cb);
}

module.exports.connect = connect;
module.exports.sendDebugMsg = sendDebugMsg;
module.exports.isConnected = isConnected;
module.exports.sendMsg = sendMsg;
