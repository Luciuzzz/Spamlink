# Proyecto Link Tree / Landing Page

Este proyecto es una aplicación web tipo "link tree" construida con **Laravel 12**, **Filament**, **TailwindCSS**, y **JavaScript**. Permite configurar enlaces, mostrar información de la empresa, mini-mapa con Leaflet y un modal de contacto con Turnstile (Cloudflare).

---

## Requisitos

Antes de levantar el proyecto, asegúrate de tener instalado:

- PHP >= 8.2
- Composer
- MySQL o MariaDB
- Node.js >= 20
- npm >= 10
- Git
- Opcional: Redis o similar si usas cache/sesiones avanzadas

---

## Levantar el proyecto localmente

1. **Clonar el repositorio:**

        
        git clone <URL_DEL_REPOSITORIO>
        cd <NOMBRE_DEL_PROYECTO>

2. **Instalar dependencias PHP:**

        composer install

3. **Instalar dependencias JS y compilar assets:**

        npm install
        npm run dev   # Para desarrollo
        npm run build # Para producción

4. **Configurar archivo .env:**

        cp .env.example .env

5. **Edita las variables:**

    APP_URL=http://localhost:8000

    DB_CONNECTION=mysql

    DB_HOST=127.0.0.1

    DB_PORT=3306

    DB_DATABASE=nombre_de_tu_bd

    DB_USERNAME=tu_usuario

    DB_PASSWORD=tu_password

    SERVICES_TURNSTILE_SITE_KEY=tu_site_key

    SERVICES_TURNSTILE_SECRET_KEY=tu_secret_key

6. **Generar key de aplicación y migrar base de datos:**

        php artisan key:generate
        php artisan migrate
        php artisan storage:link

7. **Levantar servidor de desarrollo:**

        php artisan serve

Por defecto, estará disponible en: http://127.0.0.1:8000
Consideraciones adicionales

- El mini-mapa usa Leaflet y requiere conexión a internet para los tiles de OpenStreetMap.

- Los fondos responsivos se configuran desde la sección Settings de Filament.

- El modal de contacto utiliza Cloudflare Turnstile para validar formularios.

- Para ver cambios en CSS/JS en desarrollo, usar npm run dev y recargar la página.

-- Asegúrate de tener permisos de escritura en storage y bootstrap/cache.

**Comandos útiles**
    
        php artisan route:list → Ver rutas disponibles.
    
        php artisan migrate:rollback → Revertir última migración.
    
        npm run watch → Mantener compilación automática de assets.


---
