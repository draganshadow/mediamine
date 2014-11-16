#!/bin/sh
cp -f /app/config/pg_hba.conf /etc/postgresql/9.3/main/pg_hba.conf
cp -f /app/config/postgresql.conf /etc/postgresql/9.3/main/postgresql.conf
chmod +x /app/config/configure.sh
exec /app/config/configure.sh