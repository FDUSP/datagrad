#!/bin/bash
set -e

git config --global --add safe.directory /var/www/html

composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Publicar assets do USP Theme
php artisan vendor:publish --provider="Uspdev\UspTheme\ServiceProvider" --tag=assets --force
php artisan migrate --force

# Ajusta permissões depois dos comandos artisan, que rodam como root e podem criar arquivos (logs, cache)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec "$@"