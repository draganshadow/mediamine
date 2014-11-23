#!/bin/sh
cd /app/src
php app/console rabbitmq:consumer -m 1 job | tee --append app/logs/consumers.log