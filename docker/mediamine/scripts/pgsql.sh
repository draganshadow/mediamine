#!/bin/sh

exec sudo -u postgres /usr/lib/postgresql/9.3/bin/postgres -D /app/db/postgresql/main -c config_file=/etc/postgresql/9.3/main/postgresql.conf