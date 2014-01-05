FROM ubuntu:12.04

RUN apt-get update
RUN apt-get -y upgrade # DATE 2014-01-01
RUN apt-get -y install openssh-server sudo
RUN apt-get -y install apache2 libapache2-mod-php5 pwgen
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
RUN a2enmod actions

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

VOLUME ["/data"]
ADD ./config/startup.sh /opt/startup.sh

EXPOSE 80
EXPOSE 22

# ENTRYPOINT ["/opt/startup.sh"]

ENTRYPOINT ["/usr/sbin/apache2"]
CMD ["-D", "FOREGROUND"]
