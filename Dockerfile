FROM surnet/alpine-wkhtmltopdf:3.16.2-0.12.6-full

# Instalar PHP y Apache
RUN apk add --no-cache \
    php82 \
    php82-apache2 \
    php82-pdo \
    php82-pdo_mysql \
    php82-mbstring \
    php82-xml \
    php82-zip \
    php82-gd \
    php82-openssl \
    php82-session \
    php82-curl \
    git \
    unzip \
    curl \
    apache2

# Configurar Apache
RUN sed -i 's#/var/www/localhost/htdocs#/var/www/html#' /etc/apache2/httpd.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/httpd.conf

WORKDIR /var/www/html

COPY . .

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php82 -- --install-dir=/usr/bin --filename=composer

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

# Instalar dependencias Laravel
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

EXPOSE 80

CMD ["httpd", "-D", "FOREGROUND"]
