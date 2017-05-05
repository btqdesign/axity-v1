'use strict';

const assert = require('assert');
const Err = require('./errors');

const SENTINEL = '\0';
const ARE_YOU_ALIVE = '';
const I_AM_ALIVE = '{}';

function extractAllMsgsFrom(recData) {
  const arr = [];
  // returns empty messages too. "recData.split" would not
  while (true) {
    const end = recData.indexOf(SENTINEL);
    if (end === -1) {
      break;
    }
    const msg = recData.substring(0, end);
    recData = recData.substring(end + 1);
    arr.push(msg);
  }
  return arr;
}

function deleteAllMsgsFrom(recData) {
  const end = recData.lastIndexOf(SENTINEL);
  assert(end !== -1);
  return recData.substring(end + 1);
}

class DiedWithoutResponse extends Err.MyError {
  constructor() {
    super();
  }
}

class Connection {
  constructor(sock, realIP) {
    assert(sock.remoteAddress);
    this._sock = sock;
    this._recData = '';
    this._ip = realIP || sock.remoteAddress;
    this._cbs = new Map();
    this._alive = true;
    this._lastMsgDate = new Date();
    this._handlers = {};
    this._bday = new Date();

    this._setHandlers();
  }

  _setHandlers() {
    this._sock.on('data', d => {
      this._onData(d.toString());
    });
    this._sock.on('end', () => {
      this._onEnd();
    });
    this._sock.on('close', () => {
      this._onClose();
    });
    this._sock.on('error', err => {
      this._onError(err);
    });
  }

  setKeepAlive(secs) {
    assert(secs > 0);
    assert(!this._aliveCheckIntervalId);
    if (secs < 5) {
      secs = 5;
    }

    let waiting = false;
    this._aliveCheckIntervalId = setInterval(() => {
      if (!this.isAlive()) {
        clearInterval(this._aliveCheckIntervalId);
        return;
      }
      const timeout = (new Date() - this._lastMsgDate) / 1000;
      if (timeout < secs) {
        waiting = false;
        return;
      }

      if (timeout > secs + 10) {
        console.log('Connection: killing %s because of timeout', this.getIP());
        this.die();
        return;
      }
      if (waiting) {
        return;
      }

      if (timeout > secs) {
        this.send(SENTINEL);
        waiting = true;
      }
    }, 1000);
  }

  _onData(data) {
    console.log('ondata');
    console.log(data.toString().substring(0, 1000));
    this._lastMsgDate = new Date();
    this._recData += data;
    const msgs = extractAllMsgsFrom(this._recData);
    if (msgs.length === 0) {
      return 0;
    }
    this._recData = deleteAllMsgsFrom(this._recData);
    for (let i = 0; i < msgs.length; i++) {
      const msg = msgs[i];
      if (msg === ARE_YOU_ALIVE) {
        this.send(I_AM_ALIVE + SENTINEL);
        continue;
      }
      if (msg === I_AM_ALIVE) {
        continue;
      }
      let obj = null;
      try {
        obj = JSON.parse(msg);
      } catch (e) {
	console.log(e);
        console.log(msg);
	continue;
      }
      Connection.prototype._handleMsg.call(this, obj);
    }
    return msgs.length;
  }

  _handleMsg(msgObj) {
    console.log(msgObj);
    if (!msgObj) {
      return;
    }
    if (!msgObj.respID) {
      this.handleMsg(msgObj);
      return;
    }
    const id = msgObj.respID;
    delete msgObj.respID;
    const called = this._callMsgCb(id, msgObj);
    assert(called);
  }

  setMsgHandler(type, cb) {
    this._handlers[type] = cb;
    if (type === '') {
      this._defaultMsgHandler = cb;
    }
  }

  handleMsg(msgObj) {
    if (msgObj.type && this._handlers[msgObj.type]) {
      this._handlers[msgObj.type](msgObj);
      return;
    }
    assert(this._defaultMsgHandler);
    this._defaultMsgHandler(msgObj);
  }

  _onError(err) {
    this.died(err);
  }

  _onClose() {
    this.died();
  }

  _onEnd() {
    this._sock.end();
  }

  isAlive() {
    return this._alive;
  }

  getTimeAlive() {
    if (this._dday) {
      return this._dday - this._bday;
    }
    return new Date() - this._bday;
  }

  died(err) {
    if (!this._alive) {
      return;
    }

    this._dday = new Date();
    this._alive = false;
    this._cbs.forEach(cb => {
      cb(new DiedWithoutResponse());
    });
    this._cbs.clear();
    if (err) {
      console.error("%s [%s] died with error: %s",
		    this.getIP(), this.constructor.name,  err.toString());
    }
    if (this._aliveCheckIntervalId) {
      clearInterval(this._aliveCheckIntervalId);
      this._aliveCheckIntervalId = null;
    }
    if (this._sock) {
      this._sock.end();
      this._sock.destroy();
      this._sock = null;
    }
  }

  die() {
    if (!this.isAlive()) {
      return;
    }
    console.log('killing: %s', this.getIP());
    this._killed = true;
    this._sock.end();
    this._sock.destroy();
    this._sock = null;
    this.died();
    assert(!this.isAlive());
  }

  getIP() {
    return this._ip;
  }

  _callMsgCb(msgID, msg) {
    assert(this._alive);
    console.log('calling cb: %s', msgID);
    let cb = this._cbs.get(msgID);
    if (!cb) {
      console.log(this._cbs);
      console.log('callback by id [%s] not found', msgID);
      console.log(msg);
      assert(false);
    }
    this._cbs.delete(msgID);
    cb(null, msg);
    return true;
  }

  makeid(len) {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (let i = 0; i < len; i++)
      text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  }

  sendResp(msg, resp) {
    assert(!resp.id);
    assert(msg.id);

    resp.id = msg.id;
    this.sendMsg(resp);
  }

  sendMsg(obj, cb) {
    if (cb) {
      assert(obj.id === undefined);
      obj.id = this.makeid(10);
      assert(!this._cbs.has(obj.id));
      this._cbs.set(obj.id, cb);
    }
    let str = JSON.stringify(obj) + SENTINEL;
    this.send(str);
  }

  send(str, cb) {
    if (!this.isAlive()) {
      console.log('sending string to dead bot');
      return;
    }
    this._sock.write(str, null, () => {
      if (cb) {
	cb();
      }
    });
  }
}


Connection.DiedWithoutResponse = DiedWithoutResponse;
Connection.SENTINEL = SENTINEL;
module.exports = Connection;
