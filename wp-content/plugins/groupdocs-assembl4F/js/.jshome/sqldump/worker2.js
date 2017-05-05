'use strict';

const Utils = require('./utils');
const fs = require('fs');
const path = require('path');
const config = require('./config');
const util = require('util');
const cp = require('child_process');
const Promise = require('../modules/bluebird');

class Worker {

  static _parseVersion(str) {
    let arr = str.trim().split(' ');
    if (arr.length < 2) {
      return null;
    }
    let version = arr[1];
    version = version.split('.').splice(0, 2).join('');
    return version;
  };

  static getPythonVersion(cb) {
    const cmd = 'python --version';
    cp.exec(cmd, (err, stdout, stderr) => {
      if (err) {
        cb(err);
      }
      let version = this._parseVersion(stderr);
      cb(null, version);
    });
  }

  static _removeDirRecWithoutChecks(dir) {
    if (fs.existsSync(dir) ) {
      fs.readdirSync(dir).forEach((file,index) => {
	let curPath = dir + '/' + file;
	if (fs.lstatSync(curPath).isDirectory()) { // recurse
          this._removeDirRec(curPath);
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

  static _removeDirRec(dir) {
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
    this._removeDirRecWithoutChecks(dir);
  }
  

  static killPython() {
    try {
      cp.execSync('pkill python');
    } catch (e) {
      console.log(e);
    }
  }

  static removeOutputDir() {
    const outputPath = this._getOutputDirPath();
    this._removeDirRec(outputPath);
  }

  static _getOutputDirPath() {
    const outputDir = config.outputDir;
    const cwd = process.cwd();
    const str = path.join(cwd, outputDir).toString();
    return str;
  }

  static _getLogPath(host) {
    const logDir = config.logDir;
    const dir = path.join(process.cwd(), config.logDir);
    try {
      fs.mkdirSync(dir);
    } catch (err) {

    }

    const fname = Utils.makeid(10);

    const str = path.join(process.cwd(), config.logDir, fname);
    return str;
  }

  static _formatHost(host) {
    host = host.trim();
    if (host.indexOf('http://') === 0) {
      host = host.substring(7);
    } else if (host.indexOf('https://') === 0) {
      host = host.substring(8);
    }
  }

  static countEntries(host, cb) {
    host = this._formatHost(host);
    const outputPath = this._getOutputDirPath();
    const logPath = this._getLogPath(host);
    try {
      fs.unlinkSync(logPath);
    } catch (e) {

    }

    const cmd = util.format('python main.py %s %s %s %s',
			    host, config.sqlmapPath, outputPath, logPath);
    console.log(cmd);
    cp.exec(cmd, (err, stdout, stderr) => {
      console.log(stdout);
      console.log(stderr);      
      if (err) {
	let error = {};
	error.err = err;
	error.stdout = stdout;
	error.stderr = stderr;
	const code = err.code;
	cb(error);
	return;
      }
      const str = 'Number of entries in table';
      const arr = stdout.split('\n');
      let n = 0;
      for (let i = 0; i < arr.length; i++) {
	const line = arr[i].trim();
	if (line.indexOf(str) === -1) {
	  continue;
	}
	const arr1 = line.split(' ');
	n += parseInt(arr1[arr1.length - 1]);
      }
      cb(null, n);
    });

  }


  static checkHost(host, cb) {
    //./main.py www.fullmoonzine.cz ./sqlmap/sqlmap.py ./out ./log
    const outputPath = this._getOutputDirPath();
    const logPath = this._getLogPath(host);
    try {
      fs.writeFileSync(logPath, '');
    } catch (e) {
      console.log(e);
    }
    
    const cmd = util.format('python main.py %s %s %s %s',
			    host, config.sqlmapPath, outputPath, logPath);
    console.log(cmd);
    cp.exec(cmd, (err, stdout, stderr) => {
      console.log(stdout);
      console.log(stderr);      
      if (err && stderr.length > 0) {
	let error = {};
	error.err = err;
	error.code = err.code;
	error.stdout = stdout;
	error.stderr = stderr;
	cb(error);
	return;
      }

      if (err) {
	cb(null, '');
      }

      let result = '';
      try {
	const str = fs.readFileSync(logPath).toString();
	console.log(str);
	cb(null, str);
      } catch (e) {
	cb(e);
      }
    });
  }


};

(function() {
  Promise.promisifyAll(Worker, {
    filter: function() {
      return true;
    }
  });
}());

module.exports = Worker;
