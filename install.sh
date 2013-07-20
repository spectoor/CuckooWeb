#!/bin/bash

# Varibales

CUCKOO_ROOT=/home/cuckoo/cuckoo
APACHE_ROOT=/var/www/cuckoo_web

apt-get update 1>/dev/null 2>&1
apt-get -y install autoconf automake libtool re2c flex bison \
		   apache2 php5 php5-sqlite php5-dev 1>/dev/null 2>&1

# ssdeep php libraries

wget http://pecl.php.net/get/ssdeep-1.0.2.tgz
tar xzf ssdeep-1.0.2.tgz
cd ssdeep-1.0.2
phpize5 1>/dev/null 2>&1
./configure 1>/dev/null 2>&1
make 1>/dev/null 2>&1
make install 1>/dev/null 2>&1
cd .. && rm -r ssdeep-1.0.2 && rm ssdeep-1.0.2.tgz

echo Edition du fichier /etc/php5/apache2/php.ini.
echo Copier les lignes suivantes a la section \"Dynamic extensions\".
echo
echo ;extension=pdo.so
echo ;extension=pdo_sqlite.so
echo ;extension=sqlite3.so
echo extension=ssdeep.so
echo
echo Appuyer sur ENTREE pour continuer
read

nano /etc/php5/apache2/php.ini

service apache2 restart 1>/dev/null 2>&1
tar xzf cuckoo_web.tar.gz
cp -R cuckoo_web $APACHE_ROOT
ln -s $CUCKOO_ROOT/storage/analyses/ $APACHE_ROOT/analyses
chown -R www-data $APACHE_ROOT
ln -s $CUCKOO_ROOT/db/cuckoo.db $APACHE_ROOT/cuckoo.db

exit 0
