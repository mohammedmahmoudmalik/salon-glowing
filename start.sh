#!/bin/sh
echo "=== PORT=${PORT} ==="
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         bootstrap/cache
chmod -R 775 storage bootstrap/cache
ln -sfn /app/storage/app/public /app/public/storage
php artisan view:clear
php artisan config:cache
if [ "$FRESH_SEED" = "true" ]; then
    php artisan migrate:fresh --force --seed || true
else
    php artisan migrate --force || true
    if [ "$RUN_SEED" = "true" ]; then
        if [ -n "$SEED_CLASS" ]; then
            php artisan db:seed --class="$SEED_CLASS" --force || true
        else
            php artisan db:seed --force || true
        fi
    fi
fi
echo "=== Starting server on port ${PORT} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT}
