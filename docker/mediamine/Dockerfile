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

#######################################################################
#
#                       MEDIAMINE INSTALL
#
#######################################################################

RUN mkdir /app
RUN mkdir /app/db
RUN mkdir /app/config
RUN apt-get update && apt-get -y install dialog net-tools lynx nano wget curl htop


####################### ELASTIC SEARCH ################################

RUN add-apt-repository ppa:webupd8team/java && apt-get update

RUN echo oracle-java8-installer shared/accepted-oracle-license-v1-1 select true | sudo /usr/bin/debconf-set-selections
RUN apt-get install -y oracle-java8-set-default

# Install ElasticSearch.
RUN \
  cd /tmp && \
  wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.3.2.tar.gz && \
  tar xvzf elasticsearch-1.3.2.tar.gz && \
  rm -f elasticsearch-1.3.2.tar.gz && \
  mv /tmp/elasticsearch-1.3.2 /elasticsearch

RUN mkdir /etc/service/elasticsearch
ADD scripts/elasticsearch.sh /etc/service/elasticsearch/run
RUN chmod +x /etc/service/elasticsearch/run

EXPOSE 9200
EXPOSE 9300

############################# MONGO DB ################################

# Import MongoDB public GPG key AND create a MongoDB list file
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
RUN echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | tee /etc/apt/sources.list.d/10gen.list

# Update apt-get sources AND install MongoDB
RUN apt-get update && apt-get install -y mongodb-org

RUN mkdir /etc/service/mongo
ADD scripts/mongo.sh /etc/service/mongo/run
RUN chmod +x /etc/service/mongo/run

EXPOSE 27017


############################# NGINX ################################

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

RUN mkdir -p /var/run/php5-fpm/

RUN mkdir /etc/service/fpm
ADD scripts/fpm.sh /etc/service/fpm/run
RUN chmod +x /etc/service/fpm/run

RUN mkdir /etc/service/nginx
ADD scripts/nginx.sh /etc/service/nginx/run
RUN chmod +x /etc/service/nginx/run

RUN mkdir /etc/service/log_err
ADD scripts/log_err.sh /etc/service/log_err/run
RUN chmod +x /etc/service/log_err/run

RUN mkdir /etc/service/log
ADD scripts/log.sh /etc/service/log/run
RUN chmod +x /etc/service/log/run

EXPOSE 80 443 8080


############################# POSTGRES ################################

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

RUN mkdir /etc/service/pgsql
ADD scripts/pgsql.sh /etc/service/pgsql/run
RUN chmod +x /etc/service/pgsql/run

# Expose the PostgreSQL port
EXPOSE 5432


############################# RABBITMQ ################################

USER root

# Install RabbitMQ
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys F7B8CEA6056E8E56 && \
    echo "deb http://www.rabbitmq.com/debian/ testing main" >> /etc/apt/sources.list && \
    apt-get update && \
    apt-get install -y rabbitmq-server && \
    rabbitmq-plugins enable rabbitmq_management

RUN mkdir /etc/service/rabbitmq
ADD scripts/rabbitmq.sh /etc/service/rabbitmq/run
RUN chmod +x /etc/service/rabbitmq/run

# For RabbitMQ
EXPOSE 5672
# For erlang
EXPOSE 4369
# For RabbitMQ Admin
EXPOSE 15672

############################# REDIS ####################################

# Install Redis
RUN apt-get install -y redis-server

RUN mkdir /etc/service/redis
ADD scripts/redis.sh /etc/service/redis/run
RUN chmod +x /etc/service/redis/run

EXPOSE 6379


############################# MEDIAMINE ################################

# Install git
RUN apt-get install -y git

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN ls
RUN git clone --branch 0.4 --depth=1  https://github.com/draganshadow/mediamine.git src

WORKDIR /app/src

RUN composer install --no-scripts --optimize-autoloader

RUN find src -name \.git -type d -exec rm -rf {} \;

ADD scripts/00_init.sh /etc/my_init.d/00_init.sh
RUN chmod +x /etc/my_init.d/00_init.sh

RUN mkdir /etc/service/job
ADD scripts/job.sh /etc/service/job/run
RUN chmod +x /etc/service/job/run

RUN mkdir /etc/service/task
ADD scripts/task.sh /etc/service/task/run
RUN chmod +x /etc/service/task/run


#######################################################################
#                       POST INSTALL CLEANUP
#######################################################################
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*