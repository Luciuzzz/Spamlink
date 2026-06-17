# Proyecto Spamlink

Landing tipo "link in bio" construida con Laravel 12 + Filament 4. Permite administrar identidad visual, enlaces sociales, multimedia (texto/imágenes/video), mapa con Leaflet y formulario de contacto con Turnstile.

## 🔗 Enlaces del proyecto

- **Despliegue (cloud):** https://spamlink.onrender.com  <!-- TODO: reemplazar por la URL real de Render -->
- **Repositorio:** https://github.com/Luciuzzz/Spamlink
- **Tablero Trello:** <!-- TODO: pegar link del tablero -->
- **Video de exposición:** <!-- TODO: pegar link del video -->

## Stack principal

- Laravel 12
- Filament 4
- TailwindCSS 4 + Vite
- Leaflet
- Cloudflare Turnstile

## Requisitos

- PHP >= 8.2
- Composer
- PostgreSQL >= 14
- Node.js >= 20
- npm >= 10

> O simplemente **Docker + Docker Compose v2** (ver sección Docker más abajo),
> que arma todo el entorno sin instalar nada de lo anterior.

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

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
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

## Backups automáticos

El proyecto incluye el comando `app:backup`, que:

- hace dump de la base de datos,
- copia los logs de Laravel,
- y elimina backups viejos según la retención configurada.

Variables relevantes en `.env`:

```env
BACKUP_PATH=/mnt/docker-data/spamlink/backups
BACKUP_KEEP_DAYS=7
BACKUP_INCLUDE_LOGS=true
BACKUP_INCLUDE_DATABASE=true
BACKUP_SCHEDULE_TIME=02:00
```

Ejecución manual:

```bash
php artisan app:backup
```

Para automatizarlo con el scheduler de Laravel, agrega este cron:

```cron
* * * * * cd /home/luis/spamlink && php artisan schedule:run >> /dev/null 2>&1
```

Si vas a guardar los backups en el HDD, apunta `BACKUP_PATH` a la ruta montada del disco, no a `/home/luis`.

---

## 🐳 Contenerización con Docker

El proyecto incluye un `Dockerfile` multi-stage que compila los assets con Vite
(Node 20) y sirve la aplicación con PHP 8.2 + Apache. Se levanta con
**Docker Compose**, que orquesta la aplicación y la base de datos PostgreSQL.

Compose levanta dos servicios definidos en
[`docker-compose.yml`](docker-compose.yml): `app` (Laravel + Filament) y `db`
(PostgreSQL 16). La app espera a que la base esté disponible, ejecuta las
migraciones y crea el usuario administrador inicial.

```bash
docker compose build
docker compose up -d
```

| Servicio | Descripción | Puerto |
|----------|-------------|--------|
| `db` | PostgreSQL 16 | 5432 |
| `app` | API + panel Laravel/Filament | 8080 |

La aplicación queda disponible en: **http://localhost:8080**

**Usuario administrador** creado automáticamente:

- Email: `admin@correo.com`
- Contraseña: `qwerty`

Comandos útiles:

```bash
docker compose ps              # Estado de los contenedores
docker compose logs -f app     # Ver logs de la aplicación
docker compose logs -f db      # Ver logs de PostgreSQL
docker compose down            # Apagar (conserva los datos)
docker compose down -v         # Apagar y BORRAR la base (empezar limpio)
```

Ejecutar comandos dentro de los contenedores:

```bash
docker compose exec app php artisan migrate:fresh --seed   # recargar la base
docker compose exec app php artisan test                   # correr tests
docker compose exec db psql -U spamlink -d spamlink        # consola PostgreSQL
```

Credenciales de la base (definidas en `docker-compose.yml`):

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=spamlink
DB_USERNAME=spamlink
DB_PASSWORD=secret
```

### En otra PC — todo montado con un comando

```bash
git clone https://github.com/Luciuzzz/Spamlink.git
cd Spamlink
docker compose up -d
```

No requiere instalar PHP, PostgreSQL ni Node: Docker arma todo el entorno.

> **Evidencias:** las capturas de `docker compose build`, `docker compose up` y
> la app funcionando están en [`docs/evidencias/`](docs/evidencias/).

---

## ☁️ Despliegue en la nube (Render)

La aplicación está desplegada en **Render** usando el mismo `Dockerfile`.
La configuración del servicio está declarada en [`render.yaml`](render.yaml).

Pasos para desplegar:

1. Crear una cuenta en [Render](https://render.com).
2. **New + → Web Service** y conectar el repositorio de GitHub.
3. Render detecta el `Dockerfile` (runtime **Docker**).
4. Cargar las variables de entorno (`APP_KEY`, claves de Turnstile, etc.).
5. **Create Web Service** → Render construye la imagen y publica la app.

> La URL pública del despliegue está al inicio de este README.

---

## ⚙️ Automatización del despliegue

Scripts que automatizan `git add` + `commit` + `push` + `docker build` + `docker run`.

### Windows

```bat
deploy.bat "mensaje de commit"
```

### Linux / macOS

```bash
chmod +x deploy.sh        # solo la primera vez
./deploy.sh "mensaje de commit"
```

Si no se pasa mensaje de commit, se genera uno automático con fecha y hora.

### Automatización programada (Plan B)

La ejecución programada con **Crontab** (Linux) y **Programador de tareas**
(Windows) está documentada en
[`docs/automatizacion-programada.md`](docs/automatizacion-programada.md).

---

## 👥 Integrantes y distribución de tareas

| Integrante | Rama | Aporte principal |
|------------|------|------------------|
| <!-- Nombre 1 --> | `main` / `EnzoCalderon203` | <!-- describir --> |
| <!-- Nombre 2 --> | `rich` | <!-- describir --> |
| <!-- Nombre 3 --> | `wizard` | <!-- describir --> |

El flujo de trabajo usó ramas por integrante y **Pull Requests** hacia `main`.
