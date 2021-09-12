#!/usr/bin/env bash

apt-get update

# install mysql server
#debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
#debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
apt-get -y install mysql-server
mysql -uroot -proot -e "CREATE DATABASE moneylog DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"
mysql -uroot -proot -e "CREATE USER 'moneylog'@'localhost' IDENTIFIED BY 'password'";
mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost'";

apt-get install -y apache2 git unzip
apt-get install -y libapache2-mod-php7.4 php7.4 php7.4-mysql php7.4-intl php-xml php7.4-mbstring

mkdir /var/www/moneylog

adduser --ingroup www-data fabio
chown -R www-data:www-data /var/www/moneylog
chmod -R g+w /var/www/moneylog

cat << EOF >  /etc/apache2/sites-available/moneylog.conf
<VirtualHost *:80>
    ServerName moneylog.it

    ServerAdmin info@moneylog.it
    DocumentRoot /var/www/moneylog/public

    ErrorLog /var/log/apache2/moneylog-error.log
    CustomLog /var/log/apache2/moneylog-access.log combined

    <Directory /var/www/moneylog/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

a2ensite moneylog
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

curl "https://raw.githubusercontent.com/andreafabrizi/Dropbox-Uploader/master/dropbox_uploader.sh" -o /hom/fabio/dropbox_uploader.sh
chown fabio /hom/fabio/dropbox_uploader.sh
chmod +x /hom/fabio/dropbox_uploader.sh

###############################
# FOR DEVELOPMENT ENVIRONMENT #
###############################

#apt-get install php-xdebug

#cat << EOF >> /etc/php/7.0/apache2/php.ini

# Added for xdebug
#xdebug.remote_enable=1
#xdebug.remote_host=10.0.3.1
#xdebug.remote_port=9000

#EOF
