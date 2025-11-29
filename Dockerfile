FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    chromium \
    fonts-dejavu \
    xfonts-base \
    && docker-php-ext-install pdo pdo_mysql zip

RUN a2enmod rewrite

# Apuntar Apache a /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chmod -R 777 storage bootstrap/cache

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Generar key (si no existe)
RUN php artisan key:generate || true

RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

EXPOSE 80
