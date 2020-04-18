# Femte Arenan

## Introduction
Femte Arenan is a personal website for Anders Björkland. It performs as a portfolio and api site for personal projects.
The project is built upon Symfony 5 and utilizes Doctrine.

## Deployment
How the project should be deployed depends on the hosting service. 

### One.com
Deploying to one.com requires SSH as the project needs to be split into private and public sub-categories.

#### Generate a .htaccess
The purpose of editing .htaccess and provide it to the host server is so it can interpret routes in a smooth manner. That way each request can omit the [domain]/**index.php**/[subdomain]:

```
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On

    # Explicitly disable rewriting for front controllers
    RewriteRule ^index_dev.php - [L]
    RewriteRule ^index.php - [L]

    RewriteCond %{REQUEST_FILENAME} !-f

    # Change below before deploying to production
    #RewriteRule ^(.*)$ /index.php [QSA,L]
    RewriteRule ^(.*)$ /index.php [QSA,L]
</IfModule>
```

#### Edit composer.json
Add instructions for Symfony of which directory to treat as the index directory:
"extra": {
        "public-dir": "httpd.www"
}

#### Edit index.php
Since the code base is split up, we need to instruct Symfony of where it can find its instructions. Add the following to the top of the index.php-file:

```php
require dirname(__DIR__).'/httpd.private/config/bootstrap.php';
```

#### Edit /config/services.yaml
We need to update Symfony parameters in the services.yaml-file. the kernel-path isn't used here, since kernel is another directory from the destination. There are two parameters to update:

```yaml
parameters:
    images_directory: '/customers/a/1/0/femtearenan.se/httpd.www/uploads/images'
    files_directory: '/customers/a/1/0/femtearenan.se/httpd.www/uploads/files'
```

#### Edit /config/packages/twig.yaml
We need to update Twig parameters in the twig.yaml-file. There are two parameters to update:

```yaml
globals:
    images_directory: '/customers/a/1/0/femtearenan.se/httpd.www/uploads/images'
    files_directory: '/customers/a/1/0/femtearenan.se/httpd.www/uploads/files'
```

#### Split files between private and public directories
origin | destination
--- | ---
/public/* | httpd.www/
/composer.json | httpd.www/
/.htaccess | httpd.www/
/bin | httpd.private/
/config | httpd.private/
/src | httpd.private/
/vendor | httpd.private/
/templates | httpd.private/
/var | httpd.private/

#### Update .env-file with db-credentials
Update with correct user and password accordingly:
```
DATABASE_URL=mysql://[db-name]:[db-password]@[domain].mysql/[db-name]$
```
