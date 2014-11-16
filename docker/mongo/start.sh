#!/bin/sh

docker stop mediamine-mongo-container
docker rm mediamine-mongo-container
#docker run --name mediamine-mongo-container -d -p 27017:27017 mediamine/mongo
docker run --name mediamine-mongo-container -d mediamine/mongo