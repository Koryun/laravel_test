After pulling recent commit

In console run 

```sh
$ /path/to/laravel_test/laravel composer install
$ php /path/to/laravel_test/laravel/artisan key:generate
```

In apache configurations add following
```xml
<Directory /path/to/laravel_test/public>
    Options +Indexes +FollowSymLinks +MultiViews
</Directory>
```
Make sure that /path/to/laravel_test/laravel/storage is permitted to be written.
