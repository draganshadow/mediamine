#!/bin/sh
php /app/src/app/console cron:scan
cat < echo "*/5 * * * * php /app/src/app/console cron:run" | crontab -