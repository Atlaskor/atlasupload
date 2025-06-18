FROM php:8.2-apache

RUN apt-get update && apt-get install -y bash

RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
