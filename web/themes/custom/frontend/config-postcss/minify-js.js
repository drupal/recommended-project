var uglify = require('uglify-js');
const fs = require('fs');
const log = require('./log');

module.exports = (filePath, callback) => {
  // Transform the file.
  fs.readFile(filePath, 'utf-8', (err, js) => {
    var res = uglify.minify(js, {
      compress: {
        unused: false,
      },
      mangle: false,
    });

    if (res.error) {
      log(res.error);
    }
    else {
      callback(res.code);
    }
  });
};
