'use strict';
let ProjectExecutor = require('./projectExecutor');


class ProjectManager {
  static killAll() {
    for (let i = 0; i < this._arr.length; i++) {
      let pe = this._arr[i];
      pe.kill();
    }
    this._arr = [];
  }

  static _getPe(name) {
    for (let i = 0; i < this._arr.length; i++) {
      let pe = this._arr[i];
      if (pe.getName() === name) {
	return pe;
      }
    }
    return null;
  }

  static start(name) {
    let pe = this._getPe(name);
    if (pe) {
      if (!pe.isRunning()) {
	pe.start();
      }
      return;
    }

    pe = new ProjectExecutor(name);
    pe.start();
    this._arr.push(pe);
  }


  static isRunning(name) {
    console.log('checking if %s is running', name);
    let pe = this._getPe(name);
    if (pe) {
      if (!pe.isRunning()) {
	console.log('not running. yes pe');
	return false;
      }
      console.log('running');
      return true;
    }
    console.log('not runing. no pe');
    for (let i = 0; i< this._arr.length; i++) {
      console.log(this._arr[i].getName());
    }
    return false;
  }

  static stop(name) {
    for (let i = 0; i < this._arr.length; i++) {
      let se = this._arr[i];
      if (se.getName() === name) {
        if (se.isRunning()) {
          se.stop();
        }
        this._arr.splice(i, 1);
        return;
      }
    }
  }

}

ProjectManager._arr = [];

module.exports = ProjectManager;
