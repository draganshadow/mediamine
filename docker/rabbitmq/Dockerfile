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

# Install RabbitMQ
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys F7B8CEA6056E8E56 && \
    echo "deb http://www.rabbitmq.com/debian/ testing main" >> /etc/apt/sources.list && \
    apt-get update

RUN apt-get install -y rabbitmq-server && \
    rabbitmq-plugins enable rabbitmq_management

ADD scripts/00_init.sh /etc/my_init.d/00_init.sh
RUN chmod +x /etc/my_init.d/00_init.sh

RUN mkdir /etc/service/rabbitmq
ADD scripts/rabbitmq.sh /etc/service/rabbitmq/run
RUN chmod +x /etc/service/rabbitmq/run

# For RabbitMQ
EXPOSE 5672

# For erlang
EXPOSE 4369

# For RabbitMQ Admin
EXPOSE 15672

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*