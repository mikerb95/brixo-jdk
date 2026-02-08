FROM php:8.2-apache

# 1. Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP requeridas por CodeIgniter 4
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl mysqli zip

# 3. Habilitar mod_rewrite de Apache (necesario para las rutas de CI4)
RUN a2enmod rewrite

# 4. Configurar DocumentRoot para apuntar a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecer directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar archivos del proyecto (respetando .dockerignore)
COPY . /var/www/html

# 8. Instalar dependencias de PHP (producción)
# Instala dependencias en producción (no interactivo, prefer dist)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction --no-progress

# 9. Ajustar permisos para la carpeta writable
# Apache corre como www-data, necesita escribir en writable
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# 10. Exponer puerto 80 (Render usa este puerto por defecto para detectar la app)
EXPOSE 80

# Config para proxies (Render) opcional: permitir encabezados X-Forwarded-Proto
# Apache ya suele propagar esto; la app maneja HTTPS detrás de proxy en public/index.php
