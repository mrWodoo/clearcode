clearcode
=========

Configure proper virtual host
```
<VirtualHost *:80>
        ServerName cc.dev
        ServerAdmin webmaster@localhost

        DocumentRoot /var/www/public/clearcode/web/

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
#
Run composer from project's root directory

```composer install```