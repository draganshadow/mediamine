#!/bin/sh

docker stop mediamine-pgsql-container
docker rm mediamine-pgsql-container
#docker run --name mediamine-pgsql-container -d -p 5432:5432 mediamine/pgsql
docker run --name mediamine-pgsql-container -d \
    -v /home/draganshadow/MEDIAMINE/WS/master/mediamine/docker/pgsql/config:/app/config \
    mediamine/pgsql