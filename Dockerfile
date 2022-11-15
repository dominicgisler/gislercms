FROM php:8.1-apache

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y libicu-dev libzip-dev zip git ruby-sass minify
RUN docker-php-ext-configure intl && docker-php-ext-install intl zip pdo pdo_mysql
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN a2enmod rewrite

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

EXPOSE 80
