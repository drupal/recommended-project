# Site Process

A thin wrapper around the Symfony Process Component that allows applications to use the Site Alias library to specify the target for a remote call.

[![ci](https://github.com/consolidation/site-process/workflows/CI/badge.svg)](https://travis-ci.org/consolidation/site-process)
[![scrutinizer](https://scrutinizer-ci.com/g/consolidation/site-process/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation/site-process/?branch=master)
[![codecov](https://codecov.io/gh/consolidation/site-process/branch/main/graph/badge.svg?token=CAaB7ofhxx)](https://codecov.io/gh/consolidation/site-process)
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)


## Overview

Site Process is a thin wrapper around the [Symfony Process Component](https://symfony.com/doc/3.4/components/process) that allows applications to use the [Site Alias library](https://github.com/consolidation/site-alias) to specify the target for a remote call.

For comparison purposes, the `Process` object may be created to run an application on the local system using the standard Symfony Process Component API like so:
```
$process = new Process(['ls', '-lsa']);
```
Similarly, a remote call can be done using the general-purpose `SiteProcess` API, which is accessible via the ProcessManager object:
```
$processManager = ProcessManager::createDefault();
$process = $processManager->siteProcess($site_alias, ['ls', '-lsa', '{root}']);
```
In this example, if `$site_alias` represents a site on the same system, then the `ls -lsa` command will run locally. If, on the other hand, it represents a remote site, then the `ls -lsa` command will be wrapped in an ssh call to the remote system. In either case, the `{root}` reference will be replaced with the value of the attribute of the site alias named `root`. An exception will be thrown if the named attribute does not exist.

Options may also be specified as an associative array provided as a third parameter:
```
$process = $processManager->siteProcess($site_alias, ['git', 'status'], ['untracked-files' => 'no']);
```
This is equivalent to:
```
$process = $processManager->siteProcess($site_alias, ['git', '--untracked-files=no', 'status']);
```
### Transports
#### SSH
Wraps a command so that it runs on a remote system via the ssh cli.

Example:
```yaml
local:
  host: localhost
  uri: http://localhost
  ssh:
    options: -o PasswordAuthentication=no -i $HOME/.ssh/id_rsa

```
### Vagrant
Wraps commands so they run with `vagrant ssh -c`.

Example:
```yaml
local:
  uri: http://localhost
  vagrant:
```

#### Docker Compose
Wraps a command so that it runs on a remote system via docker-compose.

Example:
```yaml
local:
  host: localhost
  uri: http://localhost
  docker:
    service: drupal
    compose:
      version: 1
      options: --project dockerComposeProjectName --file docker-compose.yml --project-directory dockerComposeWorkDir
    exec:
      options: --user www-data

```

The above would execute commands prefixed with:
```
docker-compose --project dockerComposeProjectName --file docker-compose.yml --project-directory dockerComposeWorkDir exec --user www-data -T drupal
```

`docker.project` and `compose.options --project` do the same thing, docker.project existed before options.

`docker.service` is the exact name of the service as it appears in docker-compose.yml

`docker.compose.version` defaults to `1`. Set to `2` to use the new syntax:

```
docker compose --project dockerComposeProjectName --file docker-compose.yml --project-directory dockerComposeWorkDir exec --user www-data -T drupal
```
The default behaviour is to use `docker-compose exec` to invoke a command on a running container.
You can change this to use `docker-compose run` for containers which by default are not running, with the property `compose.command`.

Check the [docker-compose](https://docs.docker.com/compose/reference/overview/) manual for all available options.

#### Local
Runs the command on the local system.

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

- Run `composer release`

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [releases](https://github.com/consolidation/site-process/releases) page.

| Branch       | Symfony Versions | PHP Versions
| ------------ | ---------------- | ------------
| main (5.x)   | ^5 | ^6          | 8.0+
| 4.x          | ^4               | 7.1+
| 2.x          | ^2 | ^3          | 5.6+

Note that all 3.x releases of consolidation/site-process were skipped simply to align the 4.x versions with Symfony 4.x support.

Note that the API of consolidation/site-process itself is compatible between the 2.x and 4.x versions, so most clients that work with both Symfony 3 and Symfony 4 should be able to require `"consolidation/site-process": "^2 | ^4"` without problems.

## Authors

* [Greg Anderson](https://github.com/greg-1-anderson)
* [Moshe Weitzman](http://weitzman.github.com)

See also the list of [contributors](https://github.com/consolidation/site-process/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* Thanks to PurpleBooth for the [example README template](https://gist.github.com/PurpleBooth/109311bb0361f32d87a2)
