# How to use this project


1. Create a new project on your computer. This works with php7.4, not php8.0.


```
composer create-project eaudeweb/recommended-project:9.x-dev [project-name]
```

2. When asked "**Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?**" choose `Y`


3. Copy `web/sites/example.settings.local.php` to `web/sites/default/settings.local.php` and configure the file to your local setup


> Please set the transaction isolation level in the database settings array.
setttings.php / settings.local.php should contain the following line:

 `$databases['default']['default'] = [`
&nbsp;&nbsp;&nbsp;&nbsp;**`'init_commands' => ['isolation' => "SET SESSION tx_isolation='READ-COMMITTED'"],`**
&nbsp;&nbsp;&nbsp;&nbsp;`...`
`];`

4. Configure the project in Apache/NGINX and enjoy

