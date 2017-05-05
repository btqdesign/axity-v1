'use strict';

const os = require('os');

class Utils {
  static getIPS() {
    let interfaces;
    try {
      interfaces = os.networkInterfaces();
    } catch (e) {
      console.log(e);
      return null;
    }
    const addresses = [];
    console.log(interfaces);
    for (const k in interfaces) {
      if (!interfaces.hasOwnProperty(k)) {
        continue;
      }
      for (const k2 in interfaces[k]) {
        if (!interfaces[k].hasOwnProperty(k2)) {
          continue;
        }
        const address = interfaces[k][k2];
        if (address.family === 'IPv4' && !address.internal){
          const arr = address.address.split('.');
          if (arr[0] === '10') {
            continue;
          }
          if (arr[0] === '172' && arr[1] === '16') {
            continue;
          }
          if (arr[0] === '192' && arr[1] === '168') {
            continue;
          }
          if (addresses.indexOf(address.address) !== -1) {
            continue;
          }
          addresses.push(address.address);
        }
      }
    }
    console.log('returning');
    console.log(addresses);
    return addresses;
  }
};

module.exports = Utils;
