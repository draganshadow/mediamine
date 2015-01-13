#!/bin/sh

docker stop mediamine
docker rm mediamine
docker run --name mediamine \
    -v /etc/mediamine/config:/app/config \
    -v /var/opt/mediamine:/app/db \
    -v /var/log/mediamine:/app/log \
    -v /tmp/mediamine/stream:/app/src/web/stream \
    -v /tmp/mediamine/resized:/app/src/web/images/resized \
    -d -p 80:80 \
    dragansadow/mediamine /sbin/my_init -- /app/config/init.sh

docker rm mediamine
docker start mediamine