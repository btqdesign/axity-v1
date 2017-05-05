'use strict';
let Utils = require('./utils');

let SENTINEL = '\0';

let extractAllMsgsFrom = function(recData) {
  let arr = [];
  while (true) {
    let split = recData.indexOf(SENTINEL);
    if (split === -1) {
      break;      
    }
    let msg = recData.substring(0, split);
    recData = recData.substring(split + 1);
    arr.push(msg);
  }
  return arr;
};

let deleteAllMsgsFrom = function(recData) {
  let split = recData.lastIndexOf(SENTINEL);
  Utils.assert(split !== -1, "Error");

  return recData.substring(split + 1);
};

// empty msg means - are you alive?
// '{}' means yes I'm alive
// on empty msg, should respond with '{}'

class Connection {
  constructor(sock) {
    Utils.assert(sock.remoteAddress);
    this._sock = sock;
    this._recData = '';
    this._ip = sock.remoteAddress;
    this._cbs = [];
    this._alive = true;
    this._lastMsgDate = new Date();
    this._keepAliveSet = false;
  }

  // todo: test this shit
  setKeepAlive(secs) {
    Utils.assert(secs > 0);
    this._aliveCheckInterval = secs;

    if (this._aliveCheckIntervalId) {
      this.clearInterval(this._aliveCheckIntervalId);
      this._aliveCheckIntervalId = null;
    }

    let waiting = false;
    this._aliveCheckIntervalId = setInterval(() => {
      if (!this.isAlive()) {
	clearInterval(this._aliveCheckIntervalId);
	this._aliveCheckIntervalId = null;
	return;
      }

      let now = new Date();
      let timeout = (now - this._lastMsgDate) / 1000;
      if (timeout <= secs) {
	waiting = false;
      }
      if (!waiting && (timeout > secs) && (timeout < secs + 30)) {
	this.send(SENTINEL);
	waiting = true;
      };
      if (timeout > secs + 30) {
	console.log('killing %s because of timeout', this.getIp());
	this.die();
      }
    }, 1000);
  }

  onData(data) {
    console.log('ondata: %s', this.getIp());
    console.log(data.toString().substring(0, 2000));
    this._lastMsgDate = new Date();
    this._recData += data;
    let msgs = extractAllMsgsFrom(this._recData);
    if (msgs.length === 0) {
      // not fully received yet;
      // TODO: handle possible DOS attack when msg doesn't contain sentinel
      return 0;
    }
    this._recData = deleteAllMsgsFrom(this._recData);
    for (let i = 0; i < msgs.length; i++) {
      let msg = msgs[i];

      let msgObj = null;
      if (msg === '') {
	// peer checks if we are alive. should send {}
	this.send('{}' + SENTINEL);
	continue;
      } else if (msg === '{}') {
	// responded - I'm alive. nothing to do here
	continue;
      } else {
	try {
	  msgObj = JSON.parse(msg);
	} catch (e) {
	  console.error(msg);
//	  throw new Error('Invalid msg received: ' + msg);
	}
      }

      Connection.prototype._handleMsg.call(this, msgObj);
    }
    
    return msgs.length;
  }

  _handleMsg(msgObj) {
    if (!msgObj) {
//      this.handleMsg(null);
      return;
    }
    this.handleMsg(msgObj);
  }

  handleMsg(msgObj) {
    
  }


  onError(err) {
    this.died(err);
  }

  onClose() {
    this.died();
  }

  onEnd() {
    this._sock.end();
  }

  isAlive() {
    return this._alive;
  }

  died(err) {
    if (!this._alive) {
      return;
    }

    this._alive = false;
    if (err) {
      console.error("%s [%s] died with error: %s",
		    this.getIp(), this.constructor.name,  err.toString());
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
    console.log('killing: %s', this.getIp());
    this._sock.end();
    this._sock.destroy();
    this._sock = null;
    this.died();
  }

  getIp() {
    return this._ip;
  }

  _callMsgCb(msgId, msg) {
//      arr.splice(i, 1);
    Utils.assert(this._alive, "message from dead connection?");
    let cb = null;
    for (let i = 0; i < this._cbs.length; i++) {
      if (this._cbs[i].id === msgId) {
	cb = this._cbs[i].cb;
	this._cbs.splice(i, 1);
	break;
      }
    }
    Utils.assert(cb);
    cb(msg);
  }


  sendMsg(obj, cb) {
    Utils.assert(!cb);
    console.log('sending msg');
    console.log(obj);
    let str = JSON.stringify(obj) + SENTINEL;
    if (obj.type === 'lostBot') {
      console.log('sending to %s............', this.getIp());
      console.log(str);
    }
    this.send(str);
  }

  send(str) {
    if (!this.isAlive()) {
      console.error('Sending string to dead bot');
      console.error(str);
      return;
    }
    this._sock.write(str);
  }

}

Connection.SENTINEL = SENTINEL;
module.exports = Connection;
