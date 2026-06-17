# =========================================================
#  Spamlink - Dockerfile (Laravel 12 + Filament 4)
#  Imagen multi-stage: base PHP comun + vendor + assets + runtime
# =========================================================

# ---------- Etapa base: PHP 8.2 + Apache + extensiones ----------
FROM php:8.2-apache AS base

# Dependencias del sistema y extensiones PHP requeridas por Laravel/Filament
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
        libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        gd \
        bcmath \
        intl \
        exif \
    && rm -rf /var/lib/apt/lists/*

# Composer (copiado desde la imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html


# ---------- Etapa 1: dependencias PHP con Composer ----------
# Usa la base (PHP 8.2 con ext-intl) para respetar el composer.lock.
FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction


# ---------- Etapa 2: build de assets con Vite ----------
FROM node:20-alpine AS assets
WORKDIR /app

# Dependencias de front
COPY package.json package-lock.json ./
RUN npm ci

# Codigo del proyecto + vendor (el tema de Filament importa CSS desde vendor/)
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
RUN npm run build


# ---------- Etapa 3: runtime PHP + Apache ----------
FROM base AS app

# Apache: habilitar mod_rewrite y apuntar DocumentRoot a /public
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar el codigo de la aplicacion
COPY . .

# Traer dependencias PHP y assets compilados desde las etapas anteriores
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Generar autoloader optimizado
RUN composer dump-autoload --optimize --no-dev

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render (y otros) inyectan el puerto vía $PORT; Apache escucha en 8080
ENV PORT=8080
RUN sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
    && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!g' /etc/apache2/sites-available/000-default.conf
EXPOSE 8080

# Script de arranque: prepara el entorno y levanta Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
