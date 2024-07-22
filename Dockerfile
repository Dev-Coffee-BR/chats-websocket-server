FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y \
    curl \
    wget \
    unzip

RUN apt-get update \
    && apt-get install -y \
    build-essential \
    php-dev \
    libssl-dev \
    php-pear \
    php-curl \
    php-xml \
    php-mbstring \
    php-zip \
    php-gd \
    php-mysql \
    libbrotli-dev

RUN pecl install swoole --enable-sockets=yes --enable-openssl=yes --enable-mysqlnd=no --enable-swoole-curl=yes --enable-cares=no --enable-brotli=no --enable-swoole-pgsql=no --with-swoole-odbc=no --with-swoole-oracle=no --enable-swoole-sqlite=no

RUN  echo "extension=swoole.so" > /etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/mods-available/swoole.ini

RUN ln -s /etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/mods-available/swoole.ini etc/php/$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')/cli/conf.d/20-swoole.ini

RUN  apt-get clean

RUN php -v \
    && php -m \
    && php --ri swoole 

# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /opt/www/

WORKDIR /opt/www

# RUN composer install

EXPOSE 8000

ENTRYPOINT [ "php", "websocket.php"]