FROM php:7.1-apache

ARG WITH_XDEBUG=false

COPY ["docker/php-apache/000-default.conf", "/etc/apache2/sites-available/000-default.conf"]

RUN apt update && apt upgrade -y
#RUN apt install sqlite3 -y

# Removed temporary file of APT
RUN rm /var/lib/apt/lists/* -R

RUN a2enmod rewrite
RUN docker-php-ext-install pdo mysqli pdo_mysql mbstring

RUN if [ $WITH_XDEBUG = "true" ] ; then \
    pecl install xdebug-2.9.8; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
#    echo "xdebug.client_host = "
fi;

# use your users $UID and $G`ID below
RUN groupadd apache-www-volume -g 1000
RUN useradd apache-www-volume -u 1000 -g 1000


CMD ["apache2-foreground"]