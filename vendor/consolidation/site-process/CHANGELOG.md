# Changelog

### 5.4.2 - 2024/Dec/13

* PHP 8.4 support

### 5.4.1 - 2024/Jul/10

* Allow Docker Compose v2

### 5.4.0

* Support Symfony 7

### 5.3.0 - 2024/Apr/1

* Allow short options. Needed to pass along -vvv (#76)

### 5.2.0 - 2022/Dec/6

* Transport support for Skpr (#66)
* Allow to set an entrypoint on KubectlTransport (#70)

### 5.1.1 - 2022/Oct/18

* Allow site-alias ^4

### 5.1.0 - 2022/Sep/15

* Support use of "docker-compose run". (#67)
* Support kubeconfig on KubectlTransport.php (#64)

### 5.0.0 - 2022/Feb/18

* Support symfony/process ^6

### 4.2.0 - 2022/Feb/18

* Support kubectl transport (#60)

### 4.1.3 / 4.1.2 - 2022/Jan/18

* Support symfony/process ^5 via illicit access to a private member (#58)
* Avoid verbose output when we have nothing to say in ProcessFailedException. (#54)

### 4.1.1 - 2022/Jan/3

* Support PHP 8.1. (n.b. No code changes to library; this release is merely to enable testing on 8.1 and explicitly declare support.)

### 4.1.0 - 2021/Feb/20

* Support PHP 8

### 4.0.0 - 2020/May/27

* Support symfony/process ^4.4, and other symfony components ^5

### 2.1.0 - 2019/Sep/10

* Added environment variables in aliases (#47)

### 2.0.4 - 2019/Aug/12

* Bugfix: Better error reporting when json output fails to parse. (#46)

### 2.0.3 - 2019/Jun/4

* Bugfix: Use posix_isatty when available. (#43)

### 2.0.2 - 2019/Apr/5

* When the transport is Docker, allow setting any docker-compose flags in the alias file Alexandru Szasz (#39)
* Added vagrant transport. Alexandru Szasz (#40)
* Added Util class to help detect TTY properly. Dane Powell (#41)

### 2.0.1 - 2019/Apr/2

* Do not format output in RealTimeOutput

### 2.0.0 - 2019/Mar/12

* Add a separaate 'addTransports' method for clients that wish to subclass the process manager (#32)
* Rename AliasRecord to SiteAlias;  Use SiteAliasWithConfig::create (#31)
* Use SiteAliasWithConfig (#30)
* Use ConfigAwareInterface/Trait (#26)
* Allow configuration to be injected into ProcessManager. (#22)
* setWorkingDirectory() controls remote execution dir (#25)

### 1.1.0 - 1.1.2 - 2019/Feb/13

* ms-slasher13 improve escaping on Windows (#24)

### 1.0.0 - 2019/Jan/17

* Initial release
