'use strict';

const Promise = require('../modules/bluebird');
const http = require('http');
const https = require('https');

class Worker {
  constructor(url, parameter, searchFor) {
    if (url.indexOf('http://') !== 0 && url.indexOf('https://') !== 0) {
      url = 'http://' + url;
    }
    this._protocol = http;
    if (url.indexOf('https://') === 0) {
      this._protocol = https;
    }
    this._url = url;
    this._parameter = parameter;
    this._searchFor = searchFor;
    Promise.promisifyAll(this, {
      filter: function() {
        return true;
      }
    });
  }

  check(cb) {
    const url = this._url + this._parameter;
    const req = this._protocol.get(url, res => {
      let data = '';
      let found = false;
      res.on('data', d => {
        data += d.toString();
        if (data.indexOf(this._searchFor) !== -1) {
	  found = true;
          res.socket.end();
          res.socket.destroy();
        }
      });
      res.on('end', () => {
        cb(null, found);
      });
    });

    req.on('error', () => {
      cb(null, false);
    });
  }

};

module.exports = Worker;
