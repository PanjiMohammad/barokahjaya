# Gunakan base image PHP 8.0 dengan Apache
FROM php:8.0-apache

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    libzip-dev unzip zip git curl \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip pdo pdo_mysql gd

# Aktifkan modul Apache rewrite
RUN a2enmod rewrite

# Set direktori kerja
WORKDIR /var/www/html

# Copy semua file project Laravel ke container
COPY . /var/www/html

# Copy Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission folder Laravel agar bisa menulis log/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Buka port 80 (Apache)
EXPOSE 80