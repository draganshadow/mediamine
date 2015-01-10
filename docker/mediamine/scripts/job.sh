#!/bin/sh
cd /app/src
php app/console rabbitmq:consumer -m 1 job >> app/logs/consumers.log 2>&1