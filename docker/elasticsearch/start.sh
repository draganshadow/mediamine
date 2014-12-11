#!/bin/sh

docker stop mediamine-elasticsearch-container
docker rm mediamine-elasticsearch-container
#docker run --name mediamine-elasticsearch-container -d -p 9200:9200 mediamine/elasticsearch
docker run --name mediamine-elasticsearch-container -d -p 9200:9200 mediamine/elasticsearch