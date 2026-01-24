FROM php:8.3-cli

RUN apt-get update && apt-get install -y unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize
