'use strict';

const assert = require('assert');

const ERRCODES = {
  sysErr: 1
};

class MyError extends Error {
  constructor() {
    super();
  }

}

class SystemError extends MyError {
  constructor(err) {
    assert(err);
    assert(err instanceof Error);
    super();
    this._err = err;
  }

  toJSON() {
    return {
      code: ERRCODES.sysErr,
      stack: this._err.stack,
      errObj: JSON.stringify(this._err)
    };
  }

  toJSONstr() {
    return JSON.stringify(this.toJSON());
  }
}

module.exports.MyError = MyError;
module.exports.SystemError = SystemError;
