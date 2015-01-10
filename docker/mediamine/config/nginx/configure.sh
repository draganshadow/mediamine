#!/bin/sh
php /app/src/app/console cron:scan
crontab /app/config/crontab.backup