#!/bin/bash

# Chờ DB sẵn sàng (tối đa 60s)
until php artisan migrate --force; do
  >&2 echo "Database is unavailable - sleeping"
  sleep 5
done

# Clear và cache lại config
php artisan config:clear
php artisan config:cache

php artisan db:seed --class=AdminSeeder --force

# Chạy server Laravel
exec php artisan serve --host=0.0.0.0 --port=8080