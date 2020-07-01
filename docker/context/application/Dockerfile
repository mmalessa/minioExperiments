FROM php:7.4.6-cli

ARG DEVELOPER_UID

RUN useradd -m -s /bin/bash -u ${DEVELOPER_UID} developer &&\
    apt update &&\
    apt install -y unzip libmcrypt4 libmcrypt-dev libpq-dev vim mc wget git curl zip curl librabbitmq-dev &&\
    docker-php-ext-install pdo_mysql mysqli pdo_pgsql &&\
    pecl install mongodb &&\
    docker-php-ext-enable mongodb &&\
    rm -rf /var/lib/apt/lists/* &&\
    pecl config-set php_ini /usr/local/etc/php/conf.d/over.ini &&\
    pecl install xdebug amqp &&\
    docker-php-ext-enable xdebug &&\
    docker-php-ext-enable amqp &&\
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer &&\
    cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini &&\
    rm -rf /var/lib/apt/lists/* &&\
    pecl clear-cache &&\
    pecl config-set php_ini /usr/local/etc/php/conf.d/over.ini &&\
    echo "su - developer" > /root/.bashrc

WORKDIR "/application"