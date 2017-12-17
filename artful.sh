#!/usr/bin/env bash

apt-get update

# install mysql 5.7
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password password wk9ncVSkXX8pwGff'
debconf-set-selections <<< 'mysql-server-5.7 mysql-server/root_password_again password wk9ncVSkXX8pwGff'
apt-get -y install mysql-server-5.7
mysql -uroot -pwk9ncVSkXX8pwGff -e "CREATE DATABASE easywallet_dev DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

apt-get install -y apache2
apt-get install -y php7.1 php7.1-mysql php7.1-intl php-xml php7.1-mbstring #php5-xdebug

mkdir /var/www/easywallet

chown -R www-data:www-data /var/www/easywallet

cat << EOF >  /etc/apache2/sites-available/easywallet.conf
<VirtualHost *:80>
    ServerName easywallet.venol.it

    ServerAdmin webmaster@venol.it
    DocumentRoot /var/www/easywallet/public

    ErrorLog /var/log/apache2/easywallet-error.log
    CustomLog /var/log/apache2/easywallet-access.log combined

    <Directory /var/www/easywallet/public>
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
EOF

a2ensite easywallet_dev
a2enmod rewrite
service apache2 restart

# create swap file 
# https://www.digitalocean.com/community/tutorials/how-to-add-swap-on-ubuntu-14-04
dd if=/dev/zero of=/swapfile bs=1M count=512
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile

if ! grep -q "swapfile" /etc/fstab; then
    echo "/swapfile none swap sw 0 0" >> /etc/fstab
fi