# SiteAlias

Manage alias records for local and remote sites.

[![ci](https://github.com/consolidation/site-alias/workflows/CI/badge.svg)](https://travis-ci.org/consolidation/site-alias)
[![scrutinizer](https://scrutinizer-ci.com/g/consolidation/site-alias/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation/site-alias/?branch=master)
[![codecov](https://codecov.io/gh/consolidation/site-alias/branch/main/graph/badge.svg?token=CAaB7ofhxx)](https://codecov.io/gh/consolidation/site-alias)
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

## Overview

This project provides the implementation for Drush site aliases. It is used in Drush 9 and later. It would also be possible to use this library to manage site aliases for similar commandline tools.

### Alias naming conventions

Site alias names always begin with a `@`, and typically are divided in three parts: the alias file location (optional), the site name, and the environment name, each separated by a dot. None of these names may contain a dot. An example alias that referenced the `dev` environment of the site `example` in the `myisp` directory might therefore look something like:
```
@myisp.example.dev
``` 
The location name is optional. If specified, it will only consider alias files located in directories with the same name as the provided location name. The remainder of the path is immaterial; only the directory that is the immediate parent of the site alias file is relevant. The location name may be omitted, e.g.:
```
@example.dev
```
If the location is not specified, then the alias manaager will consider all locations for an applicable site alias file. Note that by default, deep searching is disabled; unless deep searching is enabled, the location name must refer to a directory that is explicitly listed as a location to place site alias files (e.g. in the application's configuration file).

It is also possible to use single-word aliases. These can sometimes be ambiguous; the site alias manager will resolve single-word aliases as follows:

1. `@self` is interpreted to mean the site that has already been selected, or the site that would be selected in the absence of any alias.
2. `@none` is interpreted as the empty alias--an alias with no items defined.
3. `@<env>`, for any `<env>` is equivalent to `@self.<env>` if such an alias is defined. See below.
4. `@<site>`, for any `<site>` is equivalent to the default environment of `<site>`, e.g. `@<site>.<default>`. The default environment defaults to `dev`, but may be explicitly set in the alias.

### Alias placement on commandline

It is up to each individual commandline tools how to utilize aliases. There are two primary examples:

1. Site selection alias: `tool @sitealias command`
2. Alias parameters: `tool command @source @destination`

In the first example, with the site alias appearing before the command name, the alias is used to determine the target site for the current command. In the second example, the arguments of the command are used to specify source and destination sites.

### Alias filenames and locations

It is also up to each individual commandline tool where to search for alias files. Search locations may be added to the SiteAliasManager via an API call. By default, alias files are only found if they appear immediately inside one of the specified search locations. Deep searching is only done if explicitly enabled by the application.

Aliases are typically stored in Yaml files, although other formats may also be used if a custom alias data file loader is provided. The extension of the file determines the loader type (.yml for Yaml). The base name of the file, sans its extension, is the site name used to address the alias on the commandline. Site names may not contain periods.

### Alias file contents

The canonical site alias will contain information about how to locate the site on the local file system, and how the site is addressed on the network (when accessed via a web browser).
```
dev:
  root: /path/to/site
  uri: https://example.com
```
A more complex alias might also contain information about the server that the site is running on (when accessed via ssh for deployment and maintenance).
```
dev:
  root: /path/to/site
  uri: https://example.com
  remote: server.com
  user: www-data
```

### Wildcard environments

It is also possible to define "wildcard" environments that will match any provided environment name. This is only possible to do in instances where the contents of the wildcard aliases are all the same, except for places where the environment name appears. To substitute the name of the environment into a wildcard domain, use the variable replacement string `${env-name}`. For example, a wildcard alias that will match any multisite in a Drupal site might look something like the following example:
```
'*':
  root: /wild/path/to/wild
  uri: https://${env-name}.example.com
```

### 'Self' environment aliases

As previously mentioned, an alias in the form of `@<env>` is interpreted as `@self.<env>`. This allows sites to define a `self.site.yml` file that contains common aliases shared among a team--for example, `@stage` and `@live`.

## Site specifications

Site specifications are specially-crafted commandline arguments that can serve as replacements for simple site aliases. Site specifications are particularly useful for scripts that may wish to operate on a remote site without generating a temporary alias file.

The basic form for a site specification is:
```
user.name@example.com/path#uri
```
This is equivalent to the following alias record:
```
env:
  user: user.name
  host: example.com
  root: /path
  uri: somemultisite
```

## Getting Started

To get started contributing to this project, simply clone it locally and then run `composer install`.

### Running the tests

The test suite may be run locally by way of some simple composer scripts:

| Test             | Command
| ---------------- | ---
| Run all tests    | `composer test`
| PHPUnit tests    | `composer unit`
| PHP linter       | `composer lint`
| Code style       | `composer cs`     
| Fix style errors | `composer cbf`

### Development Commandline Tool

This library comes with a commandline tool called `alias-tool`. The only purpose
this tool serves is to provide a way to do ad-hoc experimentation and testing
for this library.

Example:
```
$ ./alias-tool site:list tests/fixtures/sitealiases/sites/

 ! [NOTE] Add search location: tests/fixtures/sitealiases/sites/                                    

'@single.alternate':
  foo: bar
  root: /alternate/path/to/single
'@single.dev':
  foo: bar
  root: /path/to/single
'@wild.*':
  foo: bar
  root: /wild/path/to/wild
  uri: 'https://*.example.com'
'@wild.dev':
  foo: bar
  root: /path/to/wild
  uri: 'https://dev.example.com'

$ ./alias-tool site:get tests/fixtures/sitealiases/sites/ @single.dev

 ! [NOTE] Add search location: tests/fixtures/sitealiases/sites/                                    

 ! [NOTE] Alias parameter: '@single.dev'                                                            

foo: bar
root: /path/to/single
```
See `./alias-tool help` and `./alias-tool list` for more information.

## Release Procedure

To create a release:

- Edit the `VERSION` file to contain the version to release, and commit the change.
- Run `composer release`

## Built With

This library was created with the [g1a/starter](https://github.com/g1a/starter) project, a fast way to create php libraries and [Robo](https://robo.li/) / [Symfony](https://symfony.com/) applications.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [releases](https://github.com/consolidation/site-alias/releases) page.

## Authors

* **Greg Anderson**
* **Moshe Weitzman**

See also the list of [contributors](https://github.com/consolidation/site-alias/contributors) who participated in this project. Thanks also to all of the [drush contributors](https://github.com/drush-ops/drush/contributors) who contributed directly or indirectly to site aliases.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
