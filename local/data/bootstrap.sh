#!/usr/bin/env bash

ENV="dev"
BASE_DIR="/vagrant"

echo "Hello, Provision is starting...."


apt-get update

apt-get -y install zip unzip


apt-get -y install apache2
a2enmod rewrite

export DEBIAN_FRONTEND="noninteractive"

debconf-set-selections <<< "mysql-server mysql-server/root_password password $1"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $1"
apt-get -y install mysql-server

mysql -u root < /vagrant/data/mysql/create-user.sql

add-apt-repository ppa:ondrej/php
apt-get update

apt-get -y install php7.2

yes | cp -rf /vagrant/data/php/php.ini /etc/php/7.2/apache2/php.ini
yes | cp -rf /vagrant/data/php/php-cli.ini /etc/php/7.2/cli/php.ini

apt-get -y install php7.2-mysql php7.2-curl php7.2-mbstring php7.2-xml php7.2-zip


# phpMyAdmin
cd /var/www/html
curl -o phpMyAdmin.tar.gz https://files.phpmyadmin.net/phpMyAdmin/4.7.9/phpMyAdmin-4.7.9-english.tar.gz
mkdir -p phpMyAdmin
tar -xzf phpMyAdmin.tar.gz -C phpMyAdmin --strip-components=1
rm -f phpMyAdmin.tar.gz
yes | cp -rf /vagrant/data/phpMyAdmin/config.inc.php /var/www/html/phpMyAdmin/config.inc.php
chown -R www-data:www-data phpMyAdmin


# web
yes | cp -rf /vagrant/data/apache2/web.conf /etc/apache2/sites-available/web.conf
a2ensite web

# db
yes | cp -rf /vagrant/data/apache2/pma.conf /etc/apache2/sites-available/pma.conf
a2ensite pma


#service apache2 reload


# Composer
cd /tmp/
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/bin/composer


curl -sL https://deb.nodesource.com/setup_8.x  | sudo -E bash -
sudo apt-get install -y nodejs

#npm install --global gulp-cli
#npm install -g bower

