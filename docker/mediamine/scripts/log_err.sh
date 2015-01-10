#!/bin/sh
> /var/log/nginx/error.log
> /var/log/fpm-php.www.log
tail -f /var/log/nginx/error.log /var/log/fpm-php.www.log 1>&2