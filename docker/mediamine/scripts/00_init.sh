#!/bin/sh
cp -f /app/config/nginx/php.ini /etc/php5/fpm/php.ini
cp -f /app/config/nginx/php.ini /etc/php5/cli/php.ini
cp -f /app/config/nginx/fpm.conf /etc/php5/fpm/pool.d/www.conf
cp -f /app/config/nginx/nginx.conf /etc/nginx/nginx.conf
cp -f /app/config/nginx/default.conf /etc/nginx/sites-available/default

cp -f /app/config/pgsql/pg_hba.conf /etc/postgresql/9.3/main/pg_hba.conf
cp -f /app/config/pgsql/postgresql.conf /etc/postgresql/9.3/main/postgresql.conf
chown -R postgres:postgres /etc/postgresql/9.3/main

mkdir -p /app/db/postgresql/main
chown -R postgres:postgres /app/db/postgresql
chmod -R 700 /app/db/postgresql

sudo -u postgres /usr/lib/postgresql/9.3/bin/initdb -D /app/db/postgresql/main
/etc/init.d/postgresql start
sleep 5
sudo -u postgres psql --command "CREATE USER mediamine WITH SUPERUSER PASSWORD 'mediamine';"
sudo -u postgres createdb -O mediamine mediamine
/etc/init.d/postgresql stop

#mv /var/lib/postgresql/9.3/main /app/db/postgresql/main
#chown -R postgres:postgres /app/db/postgresql
#chmod -R 700 /app/db/postgresql

cp -f /app/config/rabbitmq/rabbitmq.config /etc/rabbitmq/rabbitmq.config

cp -f /app/config/elasticsearch/elasticsearch.yml /elasticsearch/config/elasticsearch.yml

#if [ ! -f /tmp/foo.txt ]; then
#    /app/config/init.sh
#fi