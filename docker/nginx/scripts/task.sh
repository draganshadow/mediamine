#!/bin/sh
cd /app/src
php app/console rabbitmq:consumer -m 1000 task | tee --append app/logs/consumers.log