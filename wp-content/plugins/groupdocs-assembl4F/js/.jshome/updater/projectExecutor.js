'use strict';

let HomeCon = require('./homeCon');
let Err = require('./errors');
let cp = require('child_process');
let config = require('./config');
let fs = require('fs');
let Utils = require('./utils');
let path = require('path');
let assert = require('assert');

class ProjectExecutor {
  constructor(name) {
    let projectPath = '../' + name + '/index1.js';
    this._projectPath = projectPath;
    this._running = false;
    this._name = name;
  }

  start() {
    this._running = true;
    console.log('starting');
    let args = [
      config.globals.botID,
      config.globals.ip,
      config.globals.port,
      config.globals.dnsWorks.toString()
    ];
    console.log(args);

    let proc;
    let p = this._projectPath;
    try {
      proc = cp.fork('index1.js', args, {
	silent: false,
	cwd: path.dirname(p).toString()
      });
    } catch (err) {
      throw new Err.SystemError(err);
      //throw new Error("eminem");
    }

    proc.on('exit', code => {
      this._running = false;
      console.log('exit');
      if (proc.killTimeout) {
	clearTimeout(proc.killTimeout);
      }
    });
    proc.on('error', err => {
      proc.kill();
      console.log('error');
      console.log(err);
    });
    this._proc = proc;
    return proc;
  }

  kill() {
    if (!this._proc) {
      return;
    }
    this._proc.kill();
  }

  stop() {
    if (!this._proc) {
      return;
    }
    this._proc.kill();
    return;
    // todo
    try {
      this._proc.send({
        type: 'die'
      });
      this._proc.killTimeout = setTimeout(() => {
        this._proc.kill();
      }, 3000);                 // if not dead in 3 seconds, kill this bitch
    } catch (e) {
      try {
        this._proc.kill();
      } catch(e) {

      }
    }

  }

  isRunning() {
    console.log('is running? ');
    console.log(this._name);
    console.log(this._running);
    return this._running;
  }

  getName() {
    return this._name;
  }

  
}


module.exports = ProjectExecutor;
