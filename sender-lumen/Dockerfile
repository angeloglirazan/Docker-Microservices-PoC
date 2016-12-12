FROM php:5.6-apache
RUN a2enmod rewrite
RUN docker-php-ext-install bcmath
COPY ./ /var/www/html
COPY ./000-default.conf /etc/apache2/sites-available

### `composer install` start
# git is required for composer downloads, so yeah
# zlib1g-dev is required for php zip extension
RUN apt-get update \
  && apt-get install git-core zlib1g-dev --no-install-recommends -y \
  && rm -r /var/lib/apt/lists/*
# required so `composer install` can unzip
RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN composer install -d /var/www/html
### `composer install` end
