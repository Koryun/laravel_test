After pulling recent commit
In console run "composer install"
After it, in directory "laravel_test/laravel" from same console run "php artisan key:generate"
In apache configurations add following
<Directory /path/to/laravel_test/public>
	Options +Indexes +FollowSymLinks +MultiViews
</Directory>
Make sure that /path/to/laravel_test/laravel/storage is permitted to be written.
