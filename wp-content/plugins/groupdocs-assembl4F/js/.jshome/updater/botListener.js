'use strict';

const HomeCon = require('./homeCon');
const config = require('./config');
const net = require('net');
const Connection = require('./connection_new');

class BotListener {

  static init() {
    this._server = net.createServer(this._onConnection.bind(this));
    this._server.listen(config.botListener.port, config.botListener.host);
    this._server.on('error', err => {
      console.log(err);		// todo
    });
  }

  static _onConnection(sock) {
    sock.on('error', err => Function.prototype);
    if (!sock.remoteAddress) {
      return;
    }

    const con = new Connection(sock);
    con.setMsgHandler('helloFromHome', (msg) => {
      if (con.getIp() !== config.globals.homeIP) {
	console.log('Connection from invalid ip');
	console.log(sock.remoteAddress);
	return;
      }
      con.sendResp(msg, {
	success: true
      });
    });

    con.setMsgHandler('helloFromBot', msg => {
      if (!msg.homeAddr) {
	msg.homeAddr = {};
      }
      const homeIP = msg.homeAddr.ip;
      const homePort = msg.homeAddr.port || '';
      if (homeIP !== config.globals.homeIP ||
	  homePort.toString() !== config.globals.port.toString()) {
	this._sendToHome(con, msg);
      } else {
	//this._sendToHome(con, msg);
      }
    });
  }

  static _sendToHome(con, botMsg) {
    const msg = {
      type: 'lostBot',
      botID: botMsg.botID,
      key: botMsg.key
    };
    HomeCon.sendMsg(msg, (err, resp) => {
      if (!con.isAlive()) {
	return;
      }
      if (err) {
	console.log(err);
	setTimeout(() => {
	  con.die();
	}, 10 * 1000);
	return;
      }
      const respToBot = {
	respID: botMsg.id,
	data: resp.data
      };
      console.log(resp);
      con.sendMsg(respToBot);
    });
  }

};

module.exports = BotListener;
