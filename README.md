# How to use this project

1. Create a new project on your computer.

```
composer create-project eaudeweb/recommended-project:10.x-dev [project-name]
```

2. When asked `"Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?"` choose `Y`
3. Customize `example.robo.yml`
4. Update project name in `.ddev/config.yaml`
5. Update `README.md`

### Below you will find the default README template, please update the README file after creating the project.

# PROJECT NAME

Short project description.

https://www.project.org

## I. Prerequisites

| Using DDEV                                   | Using LAMP stack                                  |
|----------------------------------------------|---------------------------------------------------|
| DDEV 1.22.0+ (https://ddev.com/get-started)  | PHP 8.2 (see https://www.drupal.org/node/3295154) |
|                                              | MySQL 5.7.8+ / MariaDB 10.3.7+                    |
|                                              | Apache / NGINX                                    |
|                                              | Composer (https://getcomposer.org)                |
|                                              | NVM (https://github.com/nvm-sh/nvm)               |
|                                              | Node.js 18 (run `nvm use 18`)                     |

If you are using DDEV and get the `"Could not connect to a docker provider. Please start or install a docker provider."` error you need to add your user to `docker` group:

```
sudo usermod -aG docker $USER
newgrp docker
```

## II. Project setup

* Clone the repository
* Copy `example.robo.yml` to `robo.yml` and customize `username`, `password` and `admin_username`
* Copy `.env.example` to `.env` and configure the variables there

If you are not using DDEV, also the following steps are required:

* Create a new database
* Create a new virtual host pointing to the web folder of this project
* Update your `/etc/hosts` file accordingly
* Copy `web/sites/example.settings.local.php` to `web/sites/default/settings.local.php` and customize database credentials.
* Copy `example.salt.txt` to `salt.txt`

## III. Installation

* Run `./install.sh`

## IV. Development

Please make sure you are familiar with:
* Working with helpdesk: https://drupal.eaudeweb.ro/docs/use/helpdesk
* Our GIT workflow: https://drupal.eaudeweb.ro/docs/development-guide/git-workflow

### DDEV commands

* Run `ddev start` to start the project without reinstalling the database
* Run `ddev stop` to stop the project
* Run `ddev launch` to launch the application in the browser or access http://example.ddev.site:18080
* Running drush commands: `ddev drush command` (e.g. `ddev drush config:export`)
* Running custom apps withi vendor: `ddev exec ./vendor/bin/app command` (e.g. `ddev exec ./vendor/bin/robo site:update`)

### Composer

DDEV provides a built-in command to simplify use of PHP’s dependency manager, Composer, without requiring it to be installed on the host machine.

* `ddev composer help` runs Composer’s help command to learn more about what’s available.

### Email Capture and Review (MailHog)

After your project is started, access the MailHog web interface at http://example.ddev.site:18025, or run `ddev launch -m` to launch it in your default browser.

### Using Development Tools on the Host Machine

Tools that interact with files and require no database connection, such as Git or Composer, can be run from the host machine against the codebase for a DDEV project with no additional configuration necessary.

### Xdebug

* Configure PHPStorm debug: https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging/#phpstorm-rundebug-configuration-debugging
* Run `ddev xdebug on` to enable Xdebug
* Run `ddev xdebug off` to disable Xdebug
