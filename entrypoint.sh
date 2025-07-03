#!/bin/bash

# Buat storage symlink
php artisan storage:link || true

# Jalankan migrate (opsional, aman dijalankan berulang)
php artisan migrate --force || true

# Clear & cache konfigurasi dan route (mencegah error route tidak dikenali)
php artisan config:clear || true
php artisan route:clear || true
php artisan route:cache || true

# Jalankan Apache agar container tetap hidup
apache2-foreground
