#!/bin/sh
cp -f /app/config/php.ini /etc/php5/fpm/php.ini
cp -f /app/config/php.ini /etc/php5/cli/php.ini
cp -f /app/config/fpm.conf /etc/php5/fpm/pool.d/www.conf
cp -f /app/config/nginx.conf /etc/nginx/nginx.conf
cp -f /app/config/default.conf /etc/nginx/sites-available/default
chmod +x /app/config/configure.sh
exec /app/config/configure.sh