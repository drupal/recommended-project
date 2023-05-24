const fse = require('fs-extra');
const log = require('./log');
const compile = require('./compile');
const minify = require('./minify-js');
const path = require('path');

module.exports = (filePath) => {
  log(`'${filePath}' is being processed.`);

  // Transform the file.
  if (filePath.endsWith('.css')) {
    compile(filePath, function write(code) {
      if (!filePath.startsWith('./')) {
        filePath = './' + filePath;
      }

      // Ex file ./folder/folder/file-name.scss.css
      // Slice rome first symbols and last 9
      if (filePath.startsWith('./dev/pcss')) {
        var fileName = filePath.slice(10, -9);
      } else if (filePath.startsWith('./templates')) {
        var fileName = filePath.slice(11, -9);
      } else if (filePath.startsWith('./components')) {
        var fileName = filePath.slice(2, -9);
      } else {
        var fileName = filePath.slice(1, -9);
      }

      // Add base folder
      var cssFilePath;
      if (filePath.startsWith('./components')) {
        cssFilePath =  path.dirname(fileName) + '/dist/' + path.basename(fileName) + '.css';
      }
      else {
        cssFilePath = 'css' + fileName + '.css';
      }

      fse.outputFile(cssFilePath, code)
        .then(() => {
          log(`'${filePath}' is finished.`);
        });
    });
  }
  else if (filePath.endsWith('.js')) {
    minify(filePath, function write(code) {
      if (!filePath.startsWith('./')) {
        filePath = './' + filePath;
      }

      if (filePath.startsWith('./dev/js')) {
        var fileName = filePath.slice(8);
      } else if (filePath.startsWith('./templates')) {
        var fileName = filePath.slice(11);
      } else if (filePath.startsWith('./components')) {
        var fileName = filePath.slice(2);
      } else {
        var fileName = filePath.slice(1);
      }

      // Add base folder
      var jsFilePath;
      if (filePath.startsWith('./components')) {
        jsFilePath =  path.dirname(fileName) + '/dist/' + path.basename(fileName);
      }
      else {
        jsFilePath = 'js' + fileName;
      }


      fse.outputFile(jsFilePath, code)
        .then(() => {
          log(`'${filePath}' is finished.`);
        });
    });
  }
  else if (filePath.endsWith('.svg') || filePath.endsWith('.png') || filePath.endsWith('.jpg') || filePath.endsWith('.jpeg') || filePath.endsWith('.gif')) {
    compress_images(filePath).then(() => {
      log(`'${filePath}' is finished.`);
    });
  }
};
