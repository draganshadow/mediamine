#!/bin/sh
/etc/init.d/postgresql start
sudo -u postgres psql --command "CREATE USER mediamine WITH SUPERUSER PASSWORD 'mediamine';"
sudo -u postgres createdb -O mediamine mediamine
/etc/init.d/postgresql stop