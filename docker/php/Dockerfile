FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    pkg-config \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pgsql pdo_pgsql zip bcmath pdo

# Clean apt-get cache
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# install composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
        && chmod +x /usr/local/bin/composer

RUN usermod -u 1000 www-data \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
USER www-data

CMD ["php-fpm", "-F"]