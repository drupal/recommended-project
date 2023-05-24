/**
 * @file
 * Watch changes to *.pcss.css files and compile them to CSS during development.
 */

'use strict';

const fs = require('fs');
const path = require('path');
const chokidar = require('chokidar');

const changeOrAdded = require('./changeOrAdded');
const log = require('./log');

// Match only on .pcss.css files.
const fileMatch = [
  './dev/**/*.pcss.css',
  './templates/**/*.pcss.css',
  './components/**/*.pcss.css',
  './components/**/*.js',
  './dev/**/*.js',
];
// Ignore everything in node_modules
const watcher = chokidar.watch(fileMatch, {
  ignoreInitial: true,
  ignored: [
    './node_modules/**',
    './components/**/dist/**',
  ]
});

const unlinkHandler = (err) => {
  if (err) {
    log(err);
  }
};

// Watch for filesystem changes.
watcher
  .on('add', changeOrAdded)
  .on('change', changeOrAdded)
  .on('unlink', (filePath) => {
    if (filePath.endsWith('.css')) {
      if (filePath.startsWith('dev/pcss')) {
        var fileName = filePath.slice(8, -9);
      } else if (filePath.startsWith('components')) {
        var fileName = filePath.slice(0, -9);
      } else if (filePath.startsWith('templates')) {
        var fileName = filePath.slice(9, -9);
      } else {
        var fileName = filePath.slice(0, -9);
      }

      var cssFilePath;
      if (filePath.startsWith('components')) {
        cssFilePath =  path.dirname(fileName) + '/dist/' + path.basename(fileName) + '.css';
      }
      else {
        cssFilePath = 'css' + fileName + '.css';
      }

      fs.stat(cssFilePath, () => {
        fs.unlink(cssFilePath, unlinkHandler);
      });
    }
    else if (filePath.endsWith('.js')) {
      if (filePath.startsWith('dev/js')) {
        var fileName = filePath.slice(7);
      } else if (filePath.startsWith('components')) {
        var fileName = filePath.slice(0);
      } else if (filePath.startsWith('templates')) {
        var fileName = filePath.slice(10);
      } else {
        var fileName = filePath;
      }
      var jsFilePath;
      if (filePath.startsWith('components')) {
        jsFilePath =  path.dirname(fileName) + '/dist/' + path.basename(fileName);
      }
      else {
        jsFilePath = 'js' + fileName;
      }
      fs.stat(jsFilePath, () => {
        fs.unlink(jsFilePath, unlinkHandler);
      });
    }
  })
  .on('ready', () => log(`Watching files in current theme for changes.`));
