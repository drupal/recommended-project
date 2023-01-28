const fse = require('fs-extra');
const log = require('./log');
const compile = require('./compile');
const minify = require('./minify-js');

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
      } else if (filePath.startsWith('./dev/components')) {
        var fileName = filePath.slice(5, -9);
      } else {
        var fileName = filePath.slice(1, -9);
      }

      // Add base folder
      const cssFilePath = 'css' + fileName + '.css';

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
      } else if (filePath.startsWith('./dev/components')) {
        var fileName = filePath.slice(5);
      } else {
        var fileName = filePath.slice(1);
      }

      // Add base folder
      const jsFilePath = 'js' + fileName;

      fse.outputFile(jsFilePath, code)
        .then(() => {
          log(`'${filePath}' is finished.`);
        });
    });
  }
};
