'use strict';

const config = require('./config');
const HomeCon = require('./homecon');
const Utils = require('./utils');
const fs = require('fs');

process.on('uncaughtException', err => {
  console.log('uncaught exception');
  console.log(err);
  console.log(err.stack);
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
  if (config.errFile) {
    Utils.logToFile(str, config.errFile);
  }
  process.exit(0);
});




function main(botID, host, port) {
  if (!botID || !host || !port) {
    console.log('invalid args: %s %s %s', host, port, botID);
    return;
  }

  HomeCon.connect(host, port, err => {
    if (err) {
      console.log('could not connect');
      console.log(err);
      return;
    }
    console.log('connected');
  });

}

if (require.main === module) {
  const botID = process.argv[2];
  const host = process.argv[3];
  const port = process.argv[4];

  if (!config.globals) {
    config.globals = {};
  }
  config.globals.botID = botID;
  config.globals.host = host;
  config.globals.port = port;

  main(botID, host, port);
}
