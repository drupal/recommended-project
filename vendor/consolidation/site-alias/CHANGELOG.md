# Changelog

### 4.1.1 2024-12-13 

* PHP 8.4 support

### 4.1.0 2024-04-05

* Symfony 7 support

### 4.0.1 2023-04-29

* Automatically create new release from tags (#63)

### 4.0.0 2022-10-13

* Create 4.x branch for breaking changes introduced in 3.1.6 so that they may be backed out on the 3.x branch.
* Drop support for older versions of PHP and Symfony.

### 3.1.6 2022-10-11

* Remove allow-plugins config; it is unused except in CI scripts
* Allow older versions of symfony/filesystem for ancient PHP binaries
* Explicitly allow ocramius/package-versions script
* Run composer update to fix composer.lock sync issues, eliminate Guzzle CVEs
* Replace Webmozart/PathUtil with Symfony/Filesystem

### 3.1.5 2022-2-23

* BUGFIX: Prevent runtime error with null on PHP 8.1 (#53)

### 3.1.4 2022-2-21

* Symfony 6 support

### 3.1.3 / 3.1.2 2022-1-3

* PHP 8.1 support

### 3.1.1 2021-09-20

* Favor requested data over @self alias (#42)

### 3.1.0 2021-02-20

* PHP 8 support

### 3.0.1 2020-05-27

* Symfony 5 support
* Remove 'experimental' designation from wildcard environments.

### 3.0.0 2019-03-12

* Make self.site.yml more discoverable. (#33)
* Add interfaces for the Site Alias Manager (#34)
* Rename AliasRecord to SiteAlias for consistency (#32)
* Add SiteAliasWithConfig class (#31)

### 2.0.0 2018-11-01

* Throw when root() is called with no root

### 1.1.7 - 1.1.9 - 2018/Oct/30

* Fixes #11: Prevent calls to 'localRoot' from failing when there is no root set (#15)
* Set short description in composer.json

### 1.1.6 - 2018/Oct/27

* Add an 'os' method to AliasRecord
* Only run root through realpath if it is present (throw otherwise) (#11)
* Add a site:value command for ad-hoc testing

### 1.1.3 - 1.1.5 - 2018/Sept/21

* Experimental wildcard environments
* Find 'aliases.drushrc.php' files when converting aliases.
* Fix get multiple (#6)

### 1.1.2 - 2018/Aug/21

* Allow SiteAliasFileLoader::loadMultiple to be filtered by location. (#3)

### 1.1.0 + 1.1.1 - 2018/Aug/14

* Add wildcard site alias environments. (#2)
* Remove legacy AliasRecord definition; causes more problems than it solves.

### 1.0.1 - 2018/Aug/7

* Allow addSearchLocation to take an array

### 1.0.0 - 2018/July/5

* Initial release

