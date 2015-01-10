# Use phusion/baseimage as base image. To make your builds reproducible, make
# sure you lock down to a specific version, not to `latest`!
# See https://github.com/phusion/baseimage-docker/blob/master/Changelog.md for
# a list of version numbers.
FROM phusion/baseimage:0.9.13

# Set correct environment variables.
ENV HOME /root

# Regenerate SSH host keys. baseimage-docker does not contain any, so you
# have to do that yourself. You may also comment out this instruction; the
# init system will auto-generate one during boot.
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh

# Use baseimage-docker's init system.
CMD ["/sbin/my_init"]

# ...put your own build instructions here...

RUN add-apt-repository ppa:webupd8team/java && apt-get update
RUN apt-get -y install dialog net-tools lynx nano wget curl htop

RUN echo oracle-java8-installer shared/accepted-oracle-license-v1-1 select true | sudo /usr/bin/debconf-set-selections
RUN apt-get install -y oracle-java8-set-default

# Install ElasticSearch.
RUN \
  cd /tmp && \
  wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.3.2.tar.gz && \
  tar xvzf elasticsearch-1.3.2.tar.gz && \
  rm -f elasticsearch-1.3.2.tar.gz && \
  mv /tmp/elasticsearch-1.3.2 /elasticsearch

# Import MongoDB public GPG key AND create a MongoDB list file
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
RUN echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | tee /etc/apt/sources.list.d/10gen.list

# Update apt-get sources AND install MongoDB
RUN apt-get update && apt-get install -y mongodb-org

# Create the MongoDB data directory
RUN mkdir -p /data/db

RUN mkdir /etc/service/mongo
ADD scripts/mongo.sh /etc/service/mongo/run
RUN chmod +x /etc/service/mongo/run
RUN apt-get -y install libav-tools
RUN apt-get -y install python-software-properties
RUN add-apt-repository -y ppa:nginx/stable
RUN apt-get update

RUN apt-get -y install --fix-missing nginx-extras php5-fpm php5-mysql php5-pgsql php5-json php-symfony2-yaml php-file-iterator php-apc php5-xdebug php5-imagick php5-imap php5-mcrypt php-pear php5-dev php5-intl php5-gd php5-curl
RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN pecl install mongo
RUN echo "extension=mongo.so" >> /etc/php5/cli/conf.d/20-mongo.ini
RUN echo "extension=mongo.so" >> /etc/php5/fpm/conf.d/20-mongo.ini

RUN apt-get -y install php5-redis

# Add the PostgreSQL PGP key to verify their Debian packages.
# It should be the same key as https://www.postgresql.org/media/keys/ACCC4CF8.asc
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys B97B0AFCAA1A47F044F244A07FCC7D46ACCC4CF8

# Add PostgreSQL's repository. It contains the most recent stable release
#     of PostgreSQL, ``9.3``.
RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ precise-pgdg main" > /etc/apt/sources.list.d/pgdg.list

# Install ``python-software-properties``, ``software-properties-common`` and PostgreSQL 9.3
#  There are some warnings (in red) that show up during the build. You can hide
#  them by prefixing each apt-get statement with DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y python-software-properties software-properties-common postgresql-9.3 postgresql-client-9.3
RUN apt-get update && apt-get install -y postgresql-contrib

USER root

RUN mkdir /etc/service/pgsql
ADD scripts/pgsql.sh /etc/service/pgsql/run
RUN chmod +x /etc/service/pgsql/run
# Install RabbitMQ
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys F7B8CEA6056E8E56 && \
    echo "deb http://www.rabbitmq.com/debian/ testing main" >> /etc/apt/sources.list && \
    apt-get update && \
    apt-get install -y rabbitmq-server && \
    rabbitmq-plugins enable rabbitmq_management

RUN mkdir /etc/service/rabbitmq
ADD scripts/rabbitmq.sh /etc/service/rabbitmq/run
RUN chmod +x /etc/service/rabbitmq/run
# Install Redis
RUN apt-get install -y redis-server

RUN mkdir /etc/service/redis
ADD scripts/redis.sh /etc/service/redis/run
RUN chmod +x /etc/service/redis/run


RUN mkdir /app
WORKDIR /app
ADD . /app

RUN mkdir -p /var/run/php5-fpm/

ADD scripts/00_init.sh /etc/my_init.d/00_init.sh
RUN chmod +x /etc/my_init.d/00_init.sh

RUN mkdir /etc/service/log_err
ADD scripts/log_err.sh /etc/service/log_err/run
RUN chmod +x /etc/service/log_err/run

RUN mkdir /etc/service/log
ADD scripts/log.sh /etc/service/log/run
RUN chmod +x /etc/service/log/run

RUN mkdir /etc/service/fpm
ADD scripts/fpm.sh /etc/service/fpm/run
RUN chmod +x /etc/service/fpm/run

RUN mkdir /etc/service/nginx
ADD scripts/nginx.sh /etc/service/nginx/run
RUN chmod +x /etc/service/nginx/run

RUN mkdir /etc/service/job
ADD scripts/job.sh /etc/service/job/run
RUN chmod +x /etc/service/job/run

RUN mkdir /etc/service/task
ADD scripts/task.sh /etc/service/task/run
RUN chmod +x /etc/service/task/run


# Define mountable directories.
VOLUME ["/data"]

# Mount elasticsearch.yml config
ADD config/elasticsearch.yml /elasticsearch/config/elasticsearch.yml


RUN mkdir /etc/service/elasticsearch
ADD scripts/elasticsearch.sh /etc/service/elasticsearch/run
RUN chmod +x /etc/service/elasticsearch/run

# Define working directory.
WORKDIR /data


EXPOSE 6379
EXPOSE 5672
EXPOSE 4369
EXPOSE 15672
EXPOSE 5432
EXPOSE 80 443 8080
EXPOSE 27017
EXPOSE 9200
EXPOSE 9300

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*