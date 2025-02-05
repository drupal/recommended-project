# PHP Collections library

![Tests](https://github.com/phootwork/phootwork/workflows/Tests/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phootwork/phootwork/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phootwork/phootwork/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phootwork/phootwork/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phootwork/phootwork/?branch=master)
[![License](https://img.shields.io/github/license/phootwork/collection.svg?style=flat-square)](https://packagist.org/packages/phootwork/collection)
[![Latest Stable Version](https://img.shields.io/packagist/v/phootwork/collection.svg?style=flat-square)](https://packagist.org/packages/phootwork/collection)
[![Total Downloads](https://img.shields.io/packagist/dt/phootwork/collection.svg?style=flat-square&colorB=007ec6)](https://packagist.org/packages/phootwork/collection)

PHP Collections library which contains ArrayList, Set, Map, Queue & Stack.

## Goals

- Provide collections for php
- Inspired by java `java.util.Collection`
- Functional sugar (map, filter, reduce, ...)

## Installation

Installation via composer:

```
composer require phootwork/collection
```

## Documentation

[https://phootwork.github.io/collection](https://phootwork.github.io/collection)

## Running tests

This package is a part of the Phootwork library. In order to run the test suite, you have to download the full library.

```
git clone https://github.com/phootwork/phootwork
```
Then install the dependencies via composer:

```
composer install
```
Now, run the *collection* test suite:

```
vendor/bin/phpunit --testsuite collection
```
If you want to run the whole library tests, simply run:

```
vendor/bin/phpunit
```

## Contact

Report issues at the github [Issue Tracker](https://github.com/phootwork/phootwork/issues).

## Changelog

Refer to [Releases](https://github.com/phootwork/phootwork/releases)
