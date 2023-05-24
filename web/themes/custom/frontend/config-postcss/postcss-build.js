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
const fileMatch = './{dev,templates}/**/*.pcss.css';
// Match js files.
const fileMatchJs = './{dev,templates}/**/*.js';
// Ignore everything in node_modules
const cssGlobOptions = {
  ignore: './node_modules/**'
};
const jsGlobOptions = {
  ignore: './**/dist/*.js'
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
  glob(fileMatch, cssGlobOptions, processFiles);
  glob(fileMatchJs, jsGlobOptions, processFiles);
}
process.exitCode = 0;
