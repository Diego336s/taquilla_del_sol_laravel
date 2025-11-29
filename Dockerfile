FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    wkhtmltopdf \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilitar mod_rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Copiar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

# Instalar dependencias
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Limpiar cache
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

EXPOSE 80
