'use strict';

const http = require('http');

function run() {
  const req = http.get('http://www.google.com/index.html', function(res) {
    process.exit(10);
    console.log('got response: %s', res.statusCode);
    // consume response body
    res.resume();
  }).on('error', function(e) {
    console.log('got error: %s', e.message);
    process.exit(11);
  });
  req.setTimeout(10 * 1000, () => {
    process.exit(11);
  });
}

run();

