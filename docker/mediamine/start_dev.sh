#!/bin/sh

docker stop mediamine
docker rm mediamine
docker run --name mediamine \
    -v $MEDIAMINE_HOME_DEV/docker/mediamine/config:/app/config \
    -v /var/opt/mediamine:/app/db \
    -v /var/log/mediamine:/app/log \
    -v /tmp/mediamine/stream:/app/src/web/stream \
    -v /tmp/mediamine/resized:/app/src/web/images/resized \
    -d -p 9999:80 \
    draganshadow/mediamine