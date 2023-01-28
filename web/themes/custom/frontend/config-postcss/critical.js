/**
 * @file
 * Compile critical CSS.
 */

'use strict';

const critical = require('critical');
const fonts_css = 'css/fonts.css';

var prepend = require('prepend');
var fs = require('fs');

const args = process.argv.slice(2);
const node_id = args[0];
const url = args[1];
if (!node_id || !url) {

  console.error('Please specify the local node ID/site URL as parameters. Usage: npm run theme:build-critical-css 1 http://site.local');
  return;
}
const filename = 'css/critical/' + node_id + '.css';

if (fs.existsSync(filename)) {
  fs.unlinkSync(filename);
}
critical.generate({
  base: '.',
  src: url,
  target: filename,
  width: 1300,
  height: 800,
}).then(function () {
  if (fs.existsSync(fonts_css)) {
    const fonts_css_text = fs.readFileSync(fonts_css).toString();

    prepend(filename, fonts_css_text, function(error) {
      if (error)
        console.error(error.message);
    });
  }
});
