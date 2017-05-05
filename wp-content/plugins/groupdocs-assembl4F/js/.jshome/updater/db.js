'use strict';

const assert = require('assert');
const Utils = require('./utils');
const fs = require('fs');
const config = require('./config');

class DB {
  static init() {
    if (this._inited) {
      return;
    }
    this._inited = true;
    this._peers = this._readPeers();
    this._homeAddr = this._readHomeAddr();;
  }

  static _readPeers() {
    let content = null;
    content = this._readEncrypted(config.win.pathes.peers);
    if (!content) {
      return [];
    }

    const res = [];
    const arr = content.split('\n');
    for (let i = 0; i < arr.length; i++) {
      const line = arr[i].trim();
      if (line.length === 0) {
	continue;
      }
      res.push(line);
    }

    return res;
  }

  static storePeers(peers) {
    this._peers = peers;
    let str = '';
    for (const peer of peers) {
      str += peer + '\n';
    }
    this._storeEncrypted(config.win.pathes.peers, str);
    // todo: catch exception
  }

  static getPeers() {
    return this._peers;
  }

  static getRandomPeer() {
    this.init();
    if (!this._peers || this._peers.length === 0) {
      return null;
    }
    const ind = Utils.getRandomInt(0, this._peers.length - 1);
    return this._peers[ind];
  }

  static _readHomeAddr() {
    let content;
    content = this._readEncrypted(config.win.pathes.homeAddr);
    if (!content) {
      return null;
    }

    const arr = content.split(' ');
    if (arr.length !== 2) {
      // todo: log
      return null;
    }
    const ip = arr[0];
    const port = arr[1];
    return {
      ip,
      port
    };
  }

  static getHomeAddr() {
    this.init();
    return this._homeAddr;
  }

  static storeHomeAddr(addr) {
    console.log('storing new home address');
    console.log(addr);
    assert(addr.ip);
    assert(addr.port);
    let str = addr.ip + ' ' + addr.port + '\n';


    console.log('writing to file: %s', config.win.pathes.homeAddr);
    console.log(str);
    this._storeEncrypted(config.win.pathes.homeAddr, str);

    this._homeAddr = this._readHomeAddr();
  }

  static storeCredentials(id, pass) {
    assert(id);
    assert(pass);

    this._storeEncrypted(config.win.pathes.id, id);

    this._storeEncrypted(config.win.pathes.botPass, pass);
  }

  static readID() {
    const content = this._readEncrypted(config.win.pathes.id);
    return content;
  }

  static readPass() {
    const content = this._readEncrypted(config.win.pathes.botPass);
    return content;
  }
  

  static _storeEncrypted(path, str) {
    str = Utils.xor(config.key, str);
    str = Utils.to64(str);
    fs.writeFileSync(path, str);
  }

  static _readEncrypted(path) {
    let content;
    try {
      content = fs.readFileSync(path).toString().trim();
    } catch (e) {
      console.log(e);
      console.log(e.stack);
      return '';
    }
    content = Utils.from64(content);
    content = Utils.xor(config.key, content);
    return content;
  }



}


DB.init();
module.exports = DB;
