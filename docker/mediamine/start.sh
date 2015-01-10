#!/bin/sh

docker stop mediamine
docker rm mediamine
docker run --name mediamine -d -p 9200:9200 mediamine/mediamine