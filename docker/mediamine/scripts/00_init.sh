#!/bin/sh
cp -f /app/config/nginx/php.ini /etc/php5/fpm/php.ini
cp -f /app/config/nginx/php.ini /etc/php5/cli/php.ini
cp -f /app/config/nginx/fpm.conf /etc/php5/fpm/pool.d/www.conf
cp -f /app/config/nginx/nginx.conf /etc/nginx/nginx.conf
cp -f /app/config/nginx/default.conf /etc/nginx/sites-available/default
chmod +x /app/config/nginx/configure.sh
exec /app/config/nginx/configure.sh

cp -f /app/config/pgsql/pg_hba.conf /etc/postgresql/9.3/main/pg_hba.conf
cp -f /app/config/pgsql/postgresql.conf /etc/postgresql/9.3/main/postgresql.conf
chmod +x /app/config/pgsql/configure.sh
exec /app/config/pgsql/configure.sh

cp -f /app/config/rabbitmq/rabbitmq.config /etc/rabbitmq/rabbitmq.config