/**
 * @file
 */

'use strict';

const glob = require('glob');
const argv = require('minimist')(process.argv.slice(2));
const changeOrAdded = require('./changeOrAdded');
const check = require('./check');
// const log = require('./log');

// Match only on .pcss.css files.
const fileMatch = './**/*.pcss.css';
// Match js files.
const fileMatchJs = './dev/**/*.js';
// Ignore everything in node_modules
const globOptions = {
  ignore: './node_modules/**'
};
const processFiles = (error, filePaths) => {
  if (error) {
    process.exitCode = 1;
  }
  // Process all the found files.
  let callback = changeOrAdded;
  if (argv.check) {
    callback = check;
  }
  filePaths.forEach(callback);
};

if (argv.file) {
  processFiles(null, [].concat(argv.file));
}
else {
  glob(fileMatch, globOptions, processFiles);
  glob(fileMatchJs, globOptions, processFiles);
}
process.exitCode = 0;
