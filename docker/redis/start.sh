#!/bin/sh

docker stop mediamine-redis-container
docker rm mediamine-redis-container
docker run --name mediamine-redis-container -p 6379:6379 -d \
    -v /home/draganshadow/MEDIAMINE/WS/master/mediamine/docker/redis/config:/app/config \
    mediamine/redis