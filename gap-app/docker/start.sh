# envsubst to replace ${PORT} in Nginx config
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# fix permissions (optional but recommended)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# generate APP_KEY if missing
if [ ! -f /var/www/html/storage/key_generated.flag ]; then
    php artisan key:generate
    touch /var/www/html/storage/key_generated.flag
fi

# run migrations and seeders
php artisan migrate --force || true
php artisan db:seed --force || true

# start PHP-FPM in background
php-fpm -D

# start Nginx in foreground
nginx -g 'daemon off;'
