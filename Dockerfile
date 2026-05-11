FROM dunglas/frankenphp:latest

# Use the recommended install-php-extensions tool for FrankenPHP
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions pdo_pgsql intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency files first for layer caching
COPY composer.json composer.lock symfony.lock ./

RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy the full application
COPY . .

RUN composer dump-autoload

# Set permissions for var/
RUN mkdir -p var && chown -R www-data:www-data var/

EXPOSE 80 443
