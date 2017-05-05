'use strict';

const assert = require('assert');
const util = require('util');
const nodemailer = require('../modules/nodemailer');
const Promise = require('../modules/bluebird');

class Sender {

  static check(smtp, user, pass, cb) {
    console.log('checking');
    const transporter = nodemailer.createTransport({
      host: smtp,
      ignoreTLS: true,
      secure: false,
      port: 25,
      auth: {
	user,
	pass
      }
    });

    let host = user.split('@')[1];
    assert(host);
    user = 'test@' + host;
    assert(host);



    const mailOptions = {
//      from: util.format('"test" <%s>', user),
      from: '"test" <test@test.com>',
      to: 'test@hotmail.com',
      subject: 'test',
      text: 'test',
      html: 'teste'
    };

/*
    // verify connection configuration
    transporter.verify((error, success) => {
      console.log(error);
      console.log(success);
      if (error) {
	cb(error);
	return;
      }
      cb(null, success);
    });

    return;
*/
    transporter.sendMail(mailOptions, (err, info) => {
      console.log(err);
      console.log(info);
      if (err) {
	cb(err);
	return;
      }
      cb(null, true);
    });
  }

};

Promise.promisifyAll(Sender);
module.exports = Sender;
