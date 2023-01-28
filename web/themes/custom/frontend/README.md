## Eau de Web Theme theme
#### Features
* Drupal 10 compatible
* Can be used as is (subtheme is required for template overrides)

#### Configuration
Head to `Appearance` and clicking Frontend `settings`.

#### Requirements
[Node JS](https://nodejs.org/en/) - v 16.0+

#### Installation

Install development dependencies by running

`npm install`

#### CSS compilation

To compile once, use `rpm run build:css`
To run the watcher, use `npm run watch:css`

#### Adding general CSS that is applied to every page (e.g. header, footer, fonts)

- Type your SCSS in an existing component in dev/pcss
- OR create a new component in dev/pcss and use it by adding it in frontend.libraries.yml under global-styling.

#### Adding CSS specific to sections of the site

- Create a new library in frontend.libraries.yml OR dev/components/*/libraries.yml
- Create your SCSS or JS files in the same folder
- Include this library wherever it is needed using Twig files: {{ attach_library('frontend/LIBRARY_NAME') }}

#### Critical CSS

If you have [Critical CSS](https://www.drupal.org/project/critical_css) module enabled, you can generate a file with the critical CSS for a specific page (usually the frontpage).

To do this, run:

`npm run theme:build-critical-css 1 http://site.local`

It will automatically generate the critical CSS for the Node 1 in css/critical/1.css. Additionally, font imports from css/fonts.css will be added to this file.
