FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Allow .htaccess to override settings
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set PHP upload limits
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
