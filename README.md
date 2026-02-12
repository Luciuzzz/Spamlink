# Proyecto Spamlink

Landing tipo "link in bio" construida con Laravel 12 + Filament 4. Permite administrar identidad visual, enlaces sociales, multimedia (texto/imágenes/video), mapa con Leaflet y formulario de contacto con Turnstile.

## Stack principal

- Laravel 12
- Filament 4
- TailwindCSS 4 + Vite
- Leaflet
- Cloudflare Turnstile

## Requisitos

- PHP >= 8.2
- Composer
- MySQL o MariaDB
- Node.js >= 20
- npm >= 10

## Instalación local

1. Clonar repositorio.

```bash
git clone https://github.com/Luciuzzz/Spamlink.git
cd Spamlink
```

2. Instalar dependencias.

```bash
composer install
npm install
```

3. Configurar entorno.

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

4. Configurar variables en `.env`:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

SERVICES_TURNSTILE_SITE_KEY=tu_site_key
SERVICES_TURNSTILE_SECRET_KEY=tu_secret_key
```

5. Migrar base de datos.

```bash
php artisan migrate
```

6. Levantar entorno de desarrollo.

```bash
composer run dev
```

Alternativa mínima:

```bash
php artisan serve
npm run dev
```

## Funcionalidades actuales

- Gestión de enlaces sociales con íconos.
- Sección multimedia configurable por bloques.
- Cargador de imágenes multimedia con cropper (relación 16:9).
- Mapa de ubicación (Leaflet) en la sección de identidad.
- Botones flotantes (WhatsApp y compartir) con corrección de superposición sobre el mapa.
- Navegación dinámica: el ícono de multimedia solo aparece si la sección multimedia está activa.
- Protección anti-spam en contacto con Turnstile.

## Comandos útiles

```bash
php artisan route:list
php artisan migrate:rollback
php artisan test
npm run build
```
