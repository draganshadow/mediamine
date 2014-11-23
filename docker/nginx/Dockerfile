# Use phusion/baseimage as base image. To make your builds reproducible, make
# sure you lock down to a specific version, not to `latest`!
# See https://github.com/phusion/baseimage-docker/blob/master/Changelog.md for
# a list of version numbers.
FROM phusion/baseimage:0.9.15

# Set correct environment variables.
ENV HOME /root

# Regenerate SSH host keys. baseimage-docker does not contain any, so you
# have to do that yourself. You may also comment out this instruction; the
# init system will auto-generate one during boot.
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh

# Use baseimage-docker's init system.
CMD ["/sbin/my_init"]

# ...put your own build instructions here...

#RUN echo "deb http://archive.ubuntu.com/ubuntu/ precise universe" >> /etc/apt/sources.list
RUN apt-get update
RUN apt-get -y install dialog net-tools lynx nano wget curl htop
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

EXPOSE 80 443 8080

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*