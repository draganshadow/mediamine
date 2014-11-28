#!/bin/sh
cd /app/src
php app/console rabbitmq:consumer -m 1000 task >> app/logs/consumers.log 2>&1