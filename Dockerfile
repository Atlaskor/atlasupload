FROM php:8.2-apache

RUN apt-get update && apt-get install -y bash

RUN apt-get install wget
# Update & install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip unzip \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libicu-dev \
    libxslt-dev \
    libmagickwand-dev \
    imagemagick \
    libmcrypt-dev \
    libreadline-dev \
    libtidy-dev \
    libgmp-dev \
    libldb-dev \
    bash \
    nano \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mbstring \
        mysqli \
        pdo \
        pdo_mysql \
        xml \
        zip \
        intl \
        bcmath \
        soap \
        exif \
        opcache \
        gettext \
        tidy \
        xsl \
        gmp \
    && pecl install imagick \
    && docker-php-ext-enable imagick
    
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN echo '<Directory /var/www/html>\nAllowOverride All\nRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf

