#!/bin/bash
set -e

# Clear caching
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations (opsional: bisa di-comment jika ingin run manual)
php artisan migrate --force

# Start Apache in foreground
apache2-foreground
