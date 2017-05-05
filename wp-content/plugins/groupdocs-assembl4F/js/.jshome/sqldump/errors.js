'use strict';

const assert = require('assert');

const ERRCODES = {
  sysErr: 1,
  DBNotFound: 2,
  passNotFound: 3,
  emailsNotFound: 4,
  invalidOutput: 5
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

class DBNotFound extends MyError {
  constructor(stdout, stderr) {
    super();
    this._stdout = stdout;
    this._stderr = stderr;
  }

  toJSON() {
    return {
      code: ERRCODES.DBNotFound,
//      stdout: this._stdout,
      sterr: this._stderr
    };
  }
}

class PasswordsNotFound extends MyError {
  constructor() {
    super();
  }

  toJSON() {
    return {
      code: ERRCODES.passNotFound
    };
  }
}

class EmailsNotFound extends MyError {
  constructor() {
    super();
  }

  toJSON() {
    return {
      code: ERRCODES.emailsNotFound
    };
  }
}

class InvalidOutput extends MyError {
  constructor(output) {
    super();
    this._output = output;
    this._stack = this.stack;
  }

  toString() {
    return this._output;
  }

  toJSON() {
    return {
      code: ERRCODES.invalidOutput,
      output: this._output,
      stack: this._stack
    };
  }
}


module.exports.MyError = MyError;
module.exports.SystemError = SystemError;
module.exports.DBNotFound = DBNotFound;
module.exports.PasswordsNotFound = PasswordsNotFound;
module.exports.EmailsNotFound = EmailsNotFound;
module.exports.InvalidOutput = InvalidOutput;
