# FilterViaDotAccessData

This project uses [dflydev/dot-access-data](https://github.com/dflydev/dot-access-data) to provide simple output filtering for applications built with [annotated-command](https://github.com/consolidation/annotated-command) / [Robo](https://github.com/consolidation/robo).

[![ci](https://github.com/consolidation/filter-via-dot-access-data/workflows/CI/badge.svg)](https://travis-ci.org/consolidation/filter-via-dot-access-data)
[![scrutinizer](https://scrutinizer-ci.com/g/consolidation/filter-via-dot-access-data/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation/filter-via-dot-access-data/?branch=master)
[![codecov](https://codecov.io/gh/consolidation/filter-via-dot-access-data/branch/main/graph/badge.svg?token=CAaB7ofhxx)](https://codecov.io/gh/consolidation/filter-via-dot-access-data)
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

## Overview

This project provides a simple logic expression evaluator which can be used in conjunction with [dflydev/dot-access-data](https://github.com/dflydev/dot-access-data) to filter out results of the sort that you might return as a RowsOfFields object, or a nested yaml/json array.

### API

To use this filter in your annotated-commands-aware application (see [g1a/starter](https://github.com/g1a/starter)), ensure that the filter hooks are registered with 
```
$commandClasses = [ 
    \Consolidation\Filter\Hooks\FilterHooks::class,   // Filter hooks
    \MyApp\Commands\MyCommands::class,                // Commandfiles for your application
];
$runner = new \Robo\Runner($commandClasses);
```
Then, any command that returns RowsOfFields data (see [consolidation/output-formatters](https://github.com/consolidation/output-formatters)) or an array may utilize the output filter feature simply by annotating its command method with `@filter-output`.
```
    /**
     * Convert a command from one format to another, potentially with filtering.
     *
     * @command example
     * @filter-output
     * @return array
     */
    public function example(array $parameters, $options = ['format' => 'yaml'])
    {
        return $this->doSomething($parameters);
    }
```
Annotating a command in this way will automaitically attach a `--filter[=FILTER]` option to the command. The output of the command may then be filtered by providing a simple expression:
```
$ mycmd example p1 p2 --filter='color=red'
```
A `contains` comparison may be done via the `*=` operator:
```
$ mycmd example p1 p2 --filter='color*=red'
```
And, finally, regex compares are also available via `~=`:
```
$ mycmd example p1 p2 --filter='color~=#^red.*#'
```
The filter decides whether to include or exclude each **top-level element** based on the result of evaluating the provided expression on each element.

- Nested data elements may be tested, e.g. via `attributes.color=red`
- Simple boolean logic may be used, e.g. `color=red&&shape=round`

Parenthesis are not supported.

## Getting Started

To build this project locally, follow the steps below.

### Prerequisites

Install dependencies:

```
composer install
```

If you wish to build the phar for this project, install the `box` phar builder via:

```
composer phar:install-tools
```

## Running the tests

The test suite may be run locally by way of some simple composer scripts:

| Test             | Command
| ---------------- | ---
| Run all tests    | `composer test`
| PHPUnit tests    | `composer unit`
| PHP linter       | `composer lint`
| Code style       | `composer cs`     
| Fix style errors | `composer cbf`


## Deployment

Deploy by the following procedure:

- Edit the `VERSION` file to contain the version to release, and commit the change.
- Run `composer release`

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [releases](https://github.com/consolidation/filter-via-dot-access-data/releases) page.

## Authors

* [Greg Anderson](https://github.com/greg-1-anderson)

See also the list of [contributors](https://github.com/consolidation/filter-via-dot-access-data/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
