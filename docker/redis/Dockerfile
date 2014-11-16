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

RUN apt-get update && apt-get -y install dialog net-tools lynx nano wget curl htop

# Install Redis
RUN apt-get install -y redis-server

ADD scripts/00_init.sh /etc/my_init.d/00_init.sh
RUN chmod +x /etc/my_init.d/00_init.sh

RUN mkdir /etc/service/redis
ADD scripts/redis.sh /etc/service/redis/run
RUN chmod +x /etc/service/redis/run

EXPOSE 6379

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*