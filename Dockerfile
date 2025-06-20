# Gunakan base image PHP 8.0 dengan Apache
FROM php:8.0-apache

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libzip-dev unzip zip git curl \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan modul Apache rewrite
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke /public (folder Laravel)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

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