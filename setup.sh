#!/bin/bash
echo "Starting deployment setup..."

# Wait for database if needed (optional)
# Run migrations to update DB schema on start
php artisan migrate --force

# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground
