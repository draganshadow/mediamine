#!/bin/sh

docker stop mediamine-nginx-container
docker rm mediamine-nginx-container
docker run --name mediamine-nginx-container -d \
    -p 8080:80 \
    --link mediamine-pgsql-container:pgsql \
    --link mediamine-mongo-container:mongo \
    --link mediamine-rabbitmq-container:rabbitmq \
    --link mediamine-redis-container:redis \
    --link mediamine-elasticsearch-container:elasticsearch \
    -v $MEDIAMINE_HOME_DEV/docker/nginx/config:/app/config \
    -v $MEDIAMINE_HOME_DEV:/app/src \
    -v ~/Vidéos:/app/media/video \
    mediamine/nginx