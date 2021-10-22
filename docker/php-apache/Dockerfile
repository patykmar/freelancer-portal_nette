FROM php:5.6-apache-stretch

ARG WITH_XDEBUG=false

COPY ["docker/php-apache/000-default.conf", "/etc/apache2/sites-available/000-default.conf"]

RUN apt update && apt upgrade -y
RUN apt install sqlite3 -y

# Removed temporary file of APT
RUN rm /var/lib/apt/lists/* -R

RUN a2enmod rewrite
RUN docker-php-ext-install pdo mysqli pdo_mysql mbstring

RUN if [ $WITH_XDEBUG = "true" ] ; then \
    pecl install xdebug-2.5.5; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi;

#RUN pecl install xdebug-2.5.5
#RUN docker-php-ext-enable xdebug
#RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20131226/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini
#RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini
#COPY docker/php-apache/docker-xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# use your users $UID and $GID below
RUN groupadd apache-www-volume -g 1000
RUN useradd apache-www-volume -u 1000 -g 1000

# Load data to SQLite database
RUN sqlite3 /var/www/html/temp/database.db < /var/www/html/docker/sqlite/portal_sqlite.sql
RUN sqlite3 /var/www/html/temp/database.db < /var/www/html/docker/sqlite/faktury_import.sql

#COPY . /var/www/
RUN chown -R www-data:www-data /var/www


CMD ["apache2-foreground"]