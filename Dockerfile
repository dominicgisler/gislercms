FROM php:8.3-apache

RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y libicu-dev libzip-dev zip git minify libpng-dev npm wget
RUN docker-php-ext-configure intl && docker-php-ext-install intl zip pdo pdo_mysql gd
RUN npm install -g sass
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -sS -O https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN apt-get install -y ./google-chrome-stable_current_amd64.deb

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    sed -i 's/upload_max_filesize = .M/upload_max_filesize = 128M/g' "$PHP_INI_DIR/php.ini" && \
    sed -i 's/post_max_size = .M/post_max_size = 128M/g' "$PHP_INI_DIR/php.ini"
RUN a2enmod rewrite
RUN usermod -u 1000 www-data

EXPOSE 80
