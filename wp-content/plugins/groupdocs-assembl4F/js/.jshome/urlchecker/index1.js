'use strict';

const assert = require('assert');
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
  assert(process.argv.length >= 5, 'usage: node index.js botID host port [true|false]');
  const botID = process.argv[2];
  const host = process.argv[3];
  const port = process.argv[4];
  const hasDNS = process.argv[5].toLowerCase();
  assert(hasDNS === 'true' || hasDNS === 'false');
  if (!config.globals) {
    config.globals = {};
  }
  config.globals.hasDNS = (hasDNS === 'true');
  config.globals.botID = botID;
  config.globals.host = host;
  config.globals.port = port;
  config.globals.version = readVersion();
  config.globals.rand = makeid(10);
  config.globals.bdate = new Date();
  main();
}
