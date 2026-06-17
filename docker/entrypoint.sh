#!/usr/bin/env bash
set -e

cd /var/www/html

# 1) Asegurar archivo .env (en cloud se usan variables de entorno reales)
if [ ! -f .env ]; then
    echo "[entrypoint] No existe .env, copiando desde .env.example"
    cp .env.example .env
fi

# 2) Generar APP_KEY si no está definida
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null && [ -z "${APP_KEY}" ]; then
    echo "[entrypoint] Generando APP_KEY"
    php artisan key:generate --force
fi

# 3) Base de datos SQLite por defecto (si no se configura otra)
if [ ! -f database/database.sqlite ]; then
    echo "[entrypoint] Creando base de datos SQLite"
    touch database/database.sqlite
fi
# SQLite necesita escribir el archivo Y su directorio (journal/WAL)
chown -R www-data:www-data database || true
chmod -R 775 database || true

# 4) Enlace simbólico de storage
php artisan storage:link || true

# 5) Migraciones (no aborta el arranque si fallan)
echo "[entrypoint] Ejecutando migraciones"
php artisan migrate --force || true

# 5b) Usuario admin inicial (admin@correo.com / qwerty)
#     El seeder no es idempotente: en reinicios falla por duplicado y se ignora.
echo "[entrypoint] Sembrando usuario admin inicial"
php artisan db:seed --class=UserSeeder --force || true

# 6) Cache de rutas y vistas para producción
#    NOTA: no se usa config:cache porque config/wizard.php contiene closures
#    no serializables. Limpiamos la config por si quedó cache previa.
php artisan config:clear || true
php artisan route:cache || true
php artisan view:cache || true

# 7) Permisos finales
chown -R www-data:www-data storage bootstrap/cache database || true

echo "[entrypoint] Iniciando: $*"
exec "$@"
