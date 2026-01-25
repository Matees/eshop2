FROM php:8.3-cli

RUN apt-get update && apt-get install -y unzip git curl procps \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql bcmath pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize
