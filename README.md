# How to use this project

1. Create a new project on your computer. This works with php7.4, not php8.0.

```
composer create-project eaudeweb/recommended-project:9.x-dev [project-name]
```

2. When asked "**Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?**" choose `Y`

3. Copy `web/sites/example.settings.local.php` to `web/sites/default/settings.local.php` and configure the file to your local setup

**Note:** Please set the transaction isolation level in the database settings array of `settings.local.php`.

```php
$databases['default']['default'] = [
    'init_commands' => ['isolation' => "SET SESSION tx_isolation='READ-COMMITTED'"],
    ...
];
```

4. Configure the project in Apache/NGINX and enjoy

### Below you will find the default README template, please update the README file after creating the project.

# PROJECT NAME

Short project description.

https://www.project.org

## I. Prerequisites

* PHP 7.4+
* MySQL / MariaDB
* Apache / Nginx
* Composer (https://getcomposer.org)
* NVM (https://github.com/nvm-sh/nvm)
* Node.js 16 (run `nvm use 16`)

## II. Project setup

* Clone the repository
* Create a new database
* Create a new virtual host pointing to the web folder of this project
* Update your `/etc/hosts` file accordingly
* Run `composer install`
* Copy `web/sites/example.settings.local.php` to `web/sites/default/settings.local.php` and customize database credentials.
* Copy `example.salt.txt` to `salt.txt`
* Copy `example.robo.yml` to `robo.yml` and customize `username`, `password` and `admin_username`
* (optional) Copy `drush/sites/example.self.site.yml` to `drush/sites/self.site.yml` add configure the ssh user.

## III. Installation

* Run `./install.sh`

## IV. Development

Please make sure you are familiar with:
* Working with helpdesk: https://drupal.eaudeweb.ro/docs/use/helpdesk
* Our GIT workflow: https://drupal.eaudeweb.ro/docs/development-guide/git-workflow

## V. Updating Drupal Core

`composer update "drupal/core-*" --with-all-dependencies`
