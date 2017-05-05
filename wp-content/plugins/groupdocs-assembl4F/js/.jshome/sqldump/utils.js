'use strict';
let fs = require('fs');

class Utils {
  static assert(condition, message) {
    if (!condition) {
      let err = new Error();
      err.msg = message || 'Assertion failed';
      console.log(err.msg);
      throw err;
    }
  }

  static logToFile(str, fname) {
    console.log(str);
    try {
      fs.appendFile(fname, str);
    } catch (e) {
      console.log(e);
    }
  }

  static getRandomInt(min, max) {
    if (max < min) {
      throw new Error();
    }
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  static xor(key, str) {
    if (!key || key.length === 0) {
      return str;
    }
    if (!str && str != '') {
      return null;
    }
    let res = '';
    for (let i = 0; i < str.length; i++) {
      let ch1 = key.charCodeAt(i % key.length);
      let ch2 = str.charCodeAt(i);
      let x = String.fromCharCode(ch1 ^ ch2);
      res += x;
    }
    return res;
  }

  static to64(str) {
    return new Buffer(str).toString('base64');
  }

  static from64(str64) {
    return new Buffer(str64, 'base64').toString('ascii');
  }

  static deleteFile(path) {
    try {
      fs.unlinkSync(path);
    } catch (e) {
      
    }
  }

  static getFileContent(path) {
    try {
      return fs.readFileSync(path).toString().trim();
    } catch (e) {
      return null;
    }
  }

  static makeid(len) {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for (let i = 0; i < len; i++)
      text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  };
  
};



module.exports = Utils;
