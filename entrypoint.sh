#!/bin/bash
set -e

# Run database migrations FIRST (penting untuk membuat tabel 'cache' dan 'sessions' sebelum config di-cache)
php artisan migrate --force

# Clear dan Build ulang cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Buat symbolic link untuk folder storage (wajib untuk gambar/bukti transfer)
php artisan storage:link

# Eksekusi argumen yang diteruskan dari Render (contoh: php artisan tabqur:monthly-report)
# Jika tidak ada argumen, jalankan server web (apache2-foreground)
if [ $# -gt 0 ]; then
    exec "$@"
else
    apache2-foreground
fi
