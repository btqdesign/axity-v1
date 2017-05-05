'use strict';

var http = require('http');

function run() {
  http.get('http://www.google.com/index.html', function(res) {
    console.log('got response: %s', res.statusCode);
    // consume response body
    res.resume();
  }).on('error', function(e) {
    console.log('got error: %s', e.message);
  });
}

console.log('before running');
run();
console.log('after running');
//module.exports.run = run;
