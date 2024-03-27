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

| Using DDEV                                                                | Using LAMP stack                                  |
|---------------------------------------------------------------------------|---------------------------------------------------|
| [mkcert](https://github.com/FiloSottile/mkcert?tab=readme-ov-file#mkcert) | PHP 8.2 |
| [DDEV 1.22.0+](https://ddev.com/get-started)                              | MySQL 5.7.8+ / MariaDB 10.3.7+                    |
|                                                                           | Apache / NGINX                                    |
|                                                                           | [Composer](https://getcomposer.org)              |
|                                                                           | [NVM](https://github.com/nvm-sh/nvm)             |
|                                                                           | Node.js 18 (run `nvm use 18`)                     |

## II. Project setup

* Clone the repository
* Copy `example.robo.yml` to `robo.yml` and customize `username`, `password` and `admin_username`
* Copy `.env.example` to `.env` and configure the variables there

If you are using LAMP stack and not DDEV, also the following steps are also required:

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
* DDEV deveopment guide: https://drupal.eaudeweb.ro/docs/technical/documentation/development-ddev
