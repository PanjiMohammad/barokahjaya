#!/bin/bash

# Buat storage symlink
php artisan storage:link || true

# Jalankan migrate (opsional, aman untuk dijalankan berulang)
php artisan migrate --force || true

# Jalankan Apache (wajib di akhir agar container tetap hidup)
apache2-foreground
