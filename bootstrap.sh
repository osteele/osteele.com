#!/usr/bin/env bash

apt-get update
apt-get install -y apache2
a2enmod rewrite
a2enmod actions
rm -rf /var/www
ln -fs /vagrant /var/www
