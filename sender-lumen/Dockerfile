FROM php:5.6-apache
RUN a2enmod rewrite
RUN docker-php-ext-install bcmath
COPY ./ /var/www/html
COPY ./000-default.conf /etc/apache2/sites-available
