'use strict';

const Home = require('./home');
const config = require('./config');
const fs = require('fs');
const util = require('util');

process.on('uncaughtException', err => {
  console.log('uncaught exception');
  console.log(err);
  console.log(err.stack);
  let str = new Error().stack.toString();
  str = util.format('%s\n%s\n%s\n%s\n', new Error().stack.toString(),
                    err.toString(),
                    err.stack.toString(), '---------------------------------\n');
  console.log(str);
  // todo: send debug msg
  // log to err
  process.exit(0);
});

function readVersion() {
  return fs.readFileSync('version.txt').toString().trim();
}

function makeid(len) {
  let text = "";
  let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  for (let i = 0; i < len; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}

function main() {
  const botID = config.globals.botID;
  const host = config.globals.host;
  const port = config.globals.port;

  if (!botID || !host || !port) {
    console.log('Invalid args: %s %s %s', host, port, botID);
    return;
  }
  Home.init();
}


if (require.main === module) {
  console.log(123);
  const botID = process.argv[2];
  const host = process.argv[3];
  const port = process.argv[4];

  if (!config.globals) {
    config.globals = {};
  }
  config.globals.botID = botID;
  config.globals.host = host;
  config.globals.port = port;
  config.globals.version = readVersion();
  config.globals.rand = makeid(10);
  config.globals.bdate = new Date();
  main();
}
