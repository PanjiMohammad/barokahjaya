# Gunakan image dasar PHP 8.0 + Apache
FROM php:8.0-apache

# Install dependensi sistem & ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Install Node.js & npm (LTS v18)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && node -v && npm -v

# Set working directory
WORKDIR /var/www/html

# Copy project Laravel ke container
COPY . .

# Salin Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependency backend Laravel
RUN composer install --no-dev --optimize-autoloader

# Install dependency frontend Laravel (Vite, Tailwind, dll)
RUN if [ -f "package.json" ]; then npm install && npm install; fi

# Salin .env jika belum ada
RUN if [ ! -f ".env" ] && [ -f ".env.example" ]; then cp .env.example .env; fi

# Generate key, cache config, view (jika .env ada)
RUN if [ -f ".env" ]; then \
    php artisan key:generate && \
    php artisan config:clear && \
    php artisan config:cache && \
    php artisan view:cache && \
    php artisan optimize:clear ; \
fi

# Set permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Set Apache root ke /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Buka port 80
EXPOSE 80