FROM php:8.2-apache

# Instalar librerías necesarias del sistema
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    wget \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libxrender1 \
    libxext6 \
    libfontconfig1 \
    libjpeg62-turbo \
    fontconfig \
    xfonts-base \
    xfonts-75dpi \
    && docker-php-ext-install pdo pdo_mysql zip

# Descargar wkhtmltopdf
RUN wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.6/wkhtmltox_0.12.6-1.bionic_amd64.deb \
    && apt install -y ./wkhtmltox_0.12.6-1.bionic_amd64.deb

# Habilitar .htaccess
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Copiar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

# Instalar paquetes PHP
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar caché
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

EXPOSE 80
