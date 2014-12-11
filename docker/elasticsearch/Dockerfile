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
RUN echo oracle-java8-installer shared/accepted-oracle-license-v1-1 select true | sudo /usr/bin/debconf-set-selections
RUN apt-get install -y oracle-java8-set-default
RUN apt-get -y install dialog net-tools lynx nano wget curl htop

# Install ElasticSearch.
RUN \
  cd /tmp && \
  wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.3.2.tar.gz && \
  tar xvzf elasticsearch-1.3.2.tar.gz && \
  rm -f elasticsearch-1.3.2.tar.gz && \
  mv /tmp/elasticsearch-1.3.2 /elasticsearch


# Define mountable directories.
VOLUME ["/data"]

# Mount elasticsearch.yml config
ADD config/elasticsearch.yml /elasticsearch/config/elasticsearch.yml


RUN mkdir /etc/service/elasticsearch
ADD scripts/elasticsearch.sh /etc/service/elasticsearch/run
RUN chmod +x /etc/service/elasticsearch/run

# Define working directory.
WORKDIR /data

EXPOSE 9200
EXPOSE 9300

# Clean up APT when done.
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*