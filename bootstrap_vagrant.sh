#!/usr/bin/env bash

apt-get update

# install mysql 5.6
debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password_again password root'
apt-get -y install mysql-server-5.6
mysql -uroot -proot -e "CREATE DATABASE easywallet_dev DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"

apt-get install -y apache2
apt-get install -y php5 php5-mysql php5-intl php5-xdebug
#apt-get install -y phpmyadmin
apt-get install -y git

if [ ! -d "/vagrant/easywallet_dev" ]; then
    mkdir /vagrant/easywallet_dev
fi

if [ ! -L "/var/www/easywallet_dev" ]; then
    ln -s /vagrant/easywallet_dev /var/www/easywallet_dev
fi

chown -R www-data:www-data /vagrant/easywallet_dev
chown -R www-data:www-data /var/www/easywallet_dev

cat << EOF >  /etc/apache2/sites-available/easywallet_dev.conf
<VirtualHost *:80>
    ServerName dev.easywallet.it

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/easywallet_dev/public
    SetEnv "APP_ENV" "development"

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/easywallet_dev/public>
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