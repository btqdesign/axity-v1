'use strict';

const os = require('os');
const net = require('net');
const util = require('util');
const cp = require('child_process');


class Utils {

  static getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  };

  static _isInternalIP(ip) {
    const arr = ip.split('.');
    if (arr[0] === '10') {
      return true;
    }
    if (arr[0] === '172' && arr[1] === '16') {
      return true;
    }
    if (arr[0] === '192' && arr[1] === '168') {
      return true;
    }
    return false;
  }

  static getIPS() {
    let interfaces;
    try {
      interfaces = os.networkInterfaces();
    } catch (e) {
      console.log(e);
      return [];
    }
    const addresses = [];
    for (let k in interfaces) {
      if (!interfaces.hasOwnProperty(k)) {
        continue;
      }
      for (const address of interfaces[k]) {
        const ip = address.address;
        if (address.family !== 'IPv4' || address.internal
            || this._isInternalIP(ip)) {
          continue;
        }
        if (addresses.indexOf(ip) !== -1) {
          continue;
        }
        addresses.push(ip);
      }
    }
    return addresses;
  };

  static canConnect(ip, port, cb) {
    const sock = net.connect(port, ip, () => {
      cb(null, true);
      cb = Function.prototype;
      sock.end();
      sock.destroy();
    });
    sock.setTimeout(10 * 1000);
    sock.on('timeout', () => {
      sock.end();
      sock.destroy();
    });
    sock.on('error', err => {
      // todo: check error type
    });
    sock.on('close', () => {
      cb(null, false);
      cb = Function.prototype;
    });
  }

  static testProxy(ip, port, cb) {
    const cmd = util.format('curl -x socks5://%s:%s/ api.ipify.org', ip, port);
    let timeoutID;
    const proc = cp.exec(cmd, (err, stdout, stderr) => {
      clearTimeout(timeoutID);
      if (err) {
	cb(err);
	cb = Function.prototype;
        return;
      }
      cb(null, true);
      cb = Function.prototype;      
    });
    timeoutID = setTimeout(() => {
      proc.kill();
      cb(null, false);
      cb = Function.prototype;      
    }, 20 * 1000);

  }
};

module.exports = Utils;
