'use strict';
const http = require('http');
const assert = require('assert');
const util = require('util');
const net = require('net');
const EventEmitter = require('events');

function makeAssertions(options) {
  assert(typeof options === typeof {});
  assert(!options.protocol);
  assert(!options.host);
  assert(!options.family);
  assert(!options.localAddress);
  assert(!options.socketPath);
  assert(!options.auth);
  assert(!options.agent);
  assert(!options.createConnection);
  assert(!options.timeout);
  assert(options.path);
  assert(options.method === 'GET' || options.method === 'POST');
  assert(options.port);
  assert(options.hostname);
}


function onData(d) {

}

class IncomingMessage extends EventEmitter {
  constructor(sock) {
    super();
    console.log('----------------------------');
    this._sock = sock;
    this._sock.on('data', d => {
      this._handleData(d);
    });
    this._sock.on('error', err => {
      console.log('incomingMessage socket error');
//      console.log(err);
      this.emit('error', err);
    });
    this._ended = false;
    this._sock.on('close', () => {
      if (this._ended) {
        return;
      }
      this._ended = true;
      this.emit('end');
    });
    this._sock.on('end', () => {
      if (this._ended) {
        return;
      }
      this._ended = true;      
      this.emit('end');
    });
    this._data = '';
    this._headersReceived = false;
  }

  setEncoding(enc) {
    this._sock.setEncoding(enc);
  }

  _parseHeaders(headers) {
    const arr = headers.split('\r\n');
    let line = arr.shift();
    const arr1 = line.split(' ');
    this.statusCode = parseInt(arr1[1]);
    this.statusMessage = arr1[2];
    this.rawHeaders = [];
    for (line of arr) {
      const index = line.indexOf(':');
      if (index === -1) {
        continue;
      }
      const key = line.substring(0, index);
      const val = line.substring(index + 1);
      this.rawHeaders.push(key);
      this.rawHeaders.push(val);
    }
    this.headers = {};
    for (let i = 0; i < this.rawHeaders.length; i += 2) {
      const key = this.rawHeaders[i].toLowerCase();
      const val = this.rawHeaders[i + 1];
      this.headers[key] = val;
    }
  }

  _handleData(d) {
    if (this._headersReceived) {
      this.emit('data', d);
      return;
    }
    this._data += d.toString();
    const index = this._data.indexOf('\r\n\r\n');
    if (index === -1) {
      return;
    }
    const headers = this._data.substring(0, index);
    this._parseHeaders(headers);
    const data = this._data.substring(index + 4);
    this._headersReceived = true;
    this.emit('headersReceived');
    setImmediate(() => {
      this._handleData(data);
    });
  }
}

function request(options, callback = Function.prototype) {
  makeAssertions(options);
  const hostname = options.hostname;
  const port = options.port;
  const path = options.path;
  const headers = options.headers;
  const method = options.method;

  this._emitter = new EventEmitter();

  console.log('connecting: %s:%s', hostname, port);

  this._socket = net.connect(port, hostname, () => {
    return;
    //assert(this._socket.remoteAddress);
    console.log('connected');
    let str = util.format('%s %s HTTP/1.1\r\n', method, path);;
    str += util.format('Host: %s\r\n', hostname);
    str += 'Connection: close\r\n';
    for (const key in headers) {
      if (!headers.hasOwnProperty(key)) {
        continue;
      }
      const val = headers[key];
      str += util.format('%s: %s\n', key, val);
    }
    str += '\r\n';
    console.log(str);
    console.log('123');
    this._socket.write(str);
    console.log('45');
    const resp = new IncomingMessage(this._socket);
    resp.on('headersReceived', () => {
      callback(resp);
    });
    this._emitter.emit('connect');    
  });

  this._socket.on('error', err => {
    console.log(err);
//    this._emitter.emit('error', err);
  });

  return {
    on: function() {

    },
    write: function() {

    },
    end: function() {

    }
  };
  

  const tryWrite = (data, encoding, callback) => {
    this._socket.write(data, encoding, callback);
    return;
    try {
    } catch (e) {
      this._emitter.emit('error', e);
    }
  };

  const tryEnd = (data, encoding) => {
    this._socket.end(data, encoding);
    return;
    try {
    } catch (e) {
      this._emitter.emit('error', e);
    }
  };
  

  return {
    on: (event, cb) => {
      if (event === 'error') {
	this._emitter.once('error', cb);
      }
    },
    write: (data, encoding, callback) => {
      if (!this._socket.connecting) {
	tryWrite(data, encoding, callback);
//        this._socket.write(data, encoding, callback);
        return;
      }
      this._emitter.on('connect', () => {
//      this._socket.on('connect', () => {
	tryWrite(data, encoding, callback);
//        this._socket.write(data, encoding, callback);
      });
    },
    end: (data, encoding) => {
      if (!this._socket.connecting) {
	tryEnd(data, encoding);
//        this._socket.end(data, encoding, callback);
        return;
      }
      this._emitter.on('connect', () => {
	tryEnd(data, encoding);	
//        this._socket.end(data, encoding);
      });
    }
  };
}










function get(options, callback) {

}

http.request = function(options, callback) {
  request.call({}, options, callback);
//  request.bind({});
};
http.get = get;
module.exports = http;
