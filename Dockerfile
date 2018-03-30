FROM php:7.1.3-apache
RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        git \
        zlib1g-dev \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

# Basic lumen packages
RUN docker-php-ext-install \
        mcrypt \
        mbstring \
        tokenizer \
        zip

# Add php.ini for production
COPY php/php.ini-production $PHP_INI_DIR/php.ini
COPY apache/apache2.conf /etc/apache2/apache2.conf

#  Configuring Apache
RUN  rm /etc/apache2/sites-available/000-default.conf \
         && rm /etc/apache2/sites-enabled/000-default.conf

# Enable rewrite module
RUN a2enmod rewrite

WORKDIR /var/www/html

# Download and Install Composer
RUN curl -s http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Add vendor binaries to PATH
ENV PATH=/var/www/html/vendor/bin:$PATH

# Frontend tasks
RUN apt-get update && apt-get install -y \
        xz-utils \
        && apt-get clean \
        && rm -rf /var/lib/apt/lists/*

ONBUILD COPY composer.json composer.lock artisan /var/www/html/
ONBUILD COPY database /var/www/html/database/

ONBUILD RUN composer install --prefer-dist --optimize-autoloader --no-scripts --no-dev --profile --ignore-platform-reqs -vvv

ONBUILD COPY package.json /var/www/html/

ONBUILD COPY . /var/www/html

ONBUILD RUN php artisan clear-compiled
ONBUILD RUN php artisan optimize
ONBUILD RUN php artisan config:cache

# Configure directory permissions for the web server
ONBUILD RUN chgrp -R www-data storage /var/www/html/bootstrap/cache
ONBUILD RUN chmod -R ug+rwx storage /var/www/html/bootstrap/cache

ONBUILD RUN chgrp -R www-data storage /var/www/html/storage
ONBUILD RUN chmod -R ug+rwx storage /var/www/html/storage

# Configure data volume
ONBUILD VOLUME /var/www/html/storage/app
ONBUILD VOLUME /var/www/html/storage/framework/sessions
ONBUILD VOLUME /var/www/html/storage/logs

# Transform into a lightweight image
ONBUILD RUN rm -Rf tests/

COPY laravel-apache2-foreground /usr/local/bin/

CMD ["laravel-apache2-foreground"]