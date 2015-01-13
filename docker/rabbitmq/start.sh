#!/bin/sh

docker stop mediamine-rabbitmq-container
docker rm mediamine-rabbitmq-container
#docker run --name mediamine-rabbitmq-container -d -p 5672:5672 -p 15672:15672 mediamine/rabbitmq
docker run --name mediamine-rabbitmq-container -p 15672:15672 -d \
    -v $MEDIAMINE_HOME_DEV/docker/rabbitmq/config:/app/config \
    mediamine/rabbitmq