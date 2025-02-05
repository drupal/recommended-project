# Docblock

[![License](https://img.shields.io/github/license/phpowermove/docblock.svg?style=flat-square)](https://packagist.org/packages/phpowermove/docblock)
[![Latest Stable Version](https://img.shields.io/packagist/v/phpowermove/docblock.svg?style=flat-square)](https://packagist.org/packages/phpowermove/docblock)
[![Total Downloads](https://img.shields.io/packagist/dt/phpowermove/docblock.svg?style=flat-square&colorB=007ec6)](https://packagist.org/packages/phpowermove/docblock)
![Tests](https://github.com/phpowermove/docblock/workflows/Docblock%20Test%20Suite/badge.svg)
![Coverage report](https://github.com/phpowermove/docblock/workflows/Coverage/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpowermove/docblock/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpowermove/docblock/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phpowermove/docblock/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpowermove/docblock/?branch=master)

PHP Docblock parser and generator. An API to read and write Docblocks.

> __WARNING__: starting from version 4.0 the library has moved to [phpowermove organization](https://github.com/phpowermove) and the namespace is `phpowermove\docblock`.

## Installation

Install via Composer:

```
composer require phpowermove/docblock
```

## Usage

### 1. Generate a Docblock instance

a) Simple:

```php
use phpowermove\docblock\Docblock;

$docblock = new Docblock();
```

b) Create from string:

```php
use phpowermove\docblock\Docblock;

$docblock = new Docblock('/**
 * Short Description.
 *
 * Long Description.
 *
 * @author gossi
 */');
```

c) Create from reflection:

```php
use phpowermove\docblock\Docblock;

$docblock = new Docblock(new \ReflectionClass('MyClass'));
```

### 2. Manipulate tags

Get the tags:

```php
$tags = $docblock->getTags();
```

Get tags by name:

```php
$tags = $docblock->getTags('author');
```

Append a tag:

```php
use phpowermove\docblock\tags\AuthorTag;

$author = new AuthorTag();
$author->setName('gossi');
$docblock->appendTag($author);
```

or with fluent API:

```php
use phpowermove\docblock\tags\AuthorTag;

$docblock->appendTag(AuthorTag::create()
	->setName('gossi')
);
```

Check tag existence:

```php
$docblock->hasTag('author');
```

### 3. Get back the string

Call `toString()`:

```php
$docblock->toString();
```

or if you are in a write-context, the magical `__toString()` will take care of it:

```php
echo $docblock;
```

## Documentation Api

See https://phpowermove.github.io/docblock

## Contributing

Feel free to fork and submit a pull request (don't forget the tests) and I am happy to merge.

## References

- This project uses the parsers from [phpDocumentor/ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock)

## Changelog

Refer to [Releases](https://github.com/phpowermove/docblock/releases)