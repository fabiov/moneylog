FROM php:7.4-apache

MAINTAINER @fvlgnn

ARG XDEBUG

COPY app.conf /etc/apache2/sites-available/000-default.conf

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i -e "s/^ *post_max_size.*/post_max_size = 65536/g" /usr/local/etc/php/php.ini \
    && sed -i -e "s/^ *upload_max_filesize.*/upload_max_filesize = 65536/g" /usr/local/etc/php/php.ini

RUN docker-php-ext-install pdo_mysql mysqli opcache

RUN apt-get update \
    && apt-get install -y libfreetype6-dev libicu-dev libjpeg62-turbo-dev libpng-dev unzip vim \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl && docker-php-ext-install intl

RUN if [ "$XDEBUG" = "1" ] ; then pecl install xdebug-2.8.1 && docker-php-ext-enable xdebug; else echo XDEBUG DISABLED; fi

RUN a2enmod rewrite
RUN a2enmod headers
#RUN a2enmod ssl
RUN a2enmod include
RUN a2enmod expires
RUN a2enmod actions
RUN a2enmod status
RUN a2enmod info

RUN service apache2 restart

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# mom funzion perché non trova ancora la direvtory /var/www/moneylog
#RUN composer install --working-dir=/var/www/html
