# Guía de Despliegue: Taquilla del Sol

## 1. Introducción

Este documento describe el proceso completo para instalar, configurar y desplegar el backend del proyecto **Taquilla del Sol**. La aplicación está construida sobre Laravel y gestiona la venta de entradas para eventos, incluyendo la autenticación de usuarios, gestión de eventos, y procesamiento de pagos con Stripe.

### Tecnologías Principales

-   **Backend**: Laravel 11+
-   **Lenguaje**: PHP 8.2+
-   **Base de Datos**: MySQL / MariaDB
-   **Autenticación**: Laravel Sanctum (API Tokens)
-   **Pagos**: Stripe (Checkout y Webhooks)
-   **Dependencias PHP**: Composer
-   **Dependencias Frontend**: Node.js / NPM (para Vite)
-   **Túnel Local**: Cloudflare Tunnel (`cloudflared`) para pruebas de webhooks.
-   **Servidor Web**: Apache o Nginx

---

## 2. Requisitos del Sistema

Asegúrate de que tu servidor o entorno de desarrollo cumple con los siguientes requisitos:

-   **PHP**: `^8.2`
-   **Composer**: Versión 2.x
-   **Node.js**: `^18.0` o superior
-   **NPM**: `^9.0` o superior
-   **Base de Datos**: MySQL `8.0+` o MariaDB `10.6+`
-   **Git**: Para clonar el repositorio.
-   **Servidor Web**: Apache o Nginx con `mod_rewrite` habilitado.

### Extensiones PHP Obligatorias

Asegúrate de que las siguientes extensiones de PHP estén instaladas y habilitadas:

-   `BCMath`
-   `Ctype`
-   `cURL`
-   `DOM`
-   `Fileinfo` (requerido para la gestión de imágenes)
-   `JSON`
-   `Mbstring`
-   `OpenSSL`
-   `PCRE`
-   `PDO` y `PDO_MySQL`
-   `Tokenizer`
-   `XML`

---

## 3. Instalación del Proyecto

Sigue estos pasos para configurar el proyecto en un entorno local o de servidor.

1.  **Clonar el Repositorio**
    ```bash
    git clone https://github.com/tu-usuario/taquilla_del_sol_laravel.git
    cd taquilla_del_sol_laravel
    ```

2.  **Instalar Dependencias de PHP**
    ```bash
    composer install --no-dev # Para producción
    # O solo 'composer install' para desarrollo
    ```

3.  **Instalar Dependencias de Node.js**
    ```bash
    npm install
    npm run build # Para compilar los assets para producción
    ```

4.  **Crear y Configurar Archivo de Entorno**
    Copia el archivo de ejemplo y genera la clave de la aplicación.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configurar la Base de Datos**
    Edita el archivo `.env` con las credenciales de tu base de datos:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=taquillaria_del_sol
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6.  **Configurar Laravel Sanctum**
    Añade los dominios de tu frontend (si aplica) para la autenticación stateful (cookie-based). Para la autenticación por tokens, esta configuración es menos crítica pero es buena práctica tenerla.
    ```dotenv
    SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
    SESSION_DOMAIN=.localhost
    ```

7.  **Ejecutar Migraciones**
    Crea la estructura de la base de datos.
    ```bash
    php artisan migrate
    ```

8.  **Ejecutar Seeders (Opcional)**
    Puebla la base de datos con datos iniciales (como categorías, asientos maestros, etc.).
    ```bash
    php artisan db:seed
    ```

9.  **Crear Enlace Simbólico de Almacenamiento**
    Este paso es **crucial** para que las imágenes de los eventos subidas sean accesibles públicamente.
    ```bash
    php artisan storage:link
    ```

---

## 4. Variables de Entorno Obligatorias (`.env`)

A continuación se detallan las variables de entorno más importantes que deben configurarse.

### 🔹 Variables Principales

| Variable                  | Descripción                                                                                             | Ejemplo                               |
| ------------------------- | ------------------------------------------------------------------------------------------------------- | ------------------------------------- |
| `APP_NAME`                | Nombre de la aplicación.                                                                                | `"Taquilla del Sol"`                  |
| `APP_ENV`                 | Entorno de la aplicación. `local` para desarrollo, `production` para producción.                        | `production`                          |
| `APP_KEY`                 | Clave de encriptación de Laravel. Generada con `php artisan key:generate`.                              | `base64:...`                          |
| `APP_DEBUG`               | Activa/desactiva el modo de depuración. `false` en producción.                                          | `false`                               |
| `APP_URL`                 | La URL base de tu aplicación.                                                                           | `https://api.taquilladelsol.com`      |
| `DB_HOST` / `DB_DATABASE` | Credenciales de conexión a la base de datos.                                                            | `127.0.0.1` / `taquillaria_del_sol`   |

### 🔹 Variables de Correo (para códigos de verificación)

| Variable             | Descripción                                      | Ejemplo                               |
| -------------------- | ------------------------------------------------ | ------------------------------------- |
| `MAIL_MAILER`        | Driver para el envío de correos.                 | `smtp`                                |
| `MAIL_HOST`          | Host del servidor SMTP.                          | `smtp.mailgun.org`                    |
| `MAIL_PORT`          | Puerto del servidor SMTP.                        | `587`                                 |
| `MAIL_USERNAME`      | Usuario para autenticación SMTP.                 | `postmaster@sandbox...`               |
| `MAIL_PASSWORD`      | Contraseña para autenticación SMTP.              | `xxxxxxxxxxxx`                        |
| `MAIL_ENCRYPTION`    | Protocolo de encriptación.                       | `tls`                                 |
| `MAIL_FROM_ADDRESS`  | Dirección de correo del remitente.               | `no-reply@taquilladelsol.com`         |
| `MAIL_FROM_NAME`     | Nombre del remitente.                            | `"${APP_NAME}"`                       |

### 🔹 Variables de Stripe

| Variable                | Descripción                                                                                             | Ejemplo                               |
| ----------------------- | ------------------------------------------------------------------------------------------------------- | ------------------------------------- |
| `STRIPE_KEY`            | Llave pública de Stripe (Publishable Key).                                                              | `pk_test_...`                         |
| `STRIPE_SECRET`         | Llave secreta de Stripe (Secret Key).                                                                   | `sk_test_...`                         |
| `STRIPE_WEBHOOK_SECRET` | Firma secreta para validar los webhooks de Stripe (Signing secret).                                     | `whsec_...`                           |

### 🔹 Variables de Sanctum y Sesión

| Variable                   | Descripción                                                                                             | Ejemplo                               |
| -------------------------- | ------------------------------------------------------------------------------------------------------- | ------------------------------------- |
| `SANCTUM_STATEFUL_DOMAINS` | Dominios que pueden usar autenticación basada en cookies (para un frontend SPA).                        | `mitienda.com,localhost:3000`         |
| `SESSION_DOMAIN`           | Dominio para las cookies de sesión. Debe ser un dominio padre si el frontend y backend son subdominios. | `.mitienda.com`                       |

### 🔹 Variables Opcionales (para URLs de Stripe)

| Variable                   | Descripción                                                                                             | Ejemplo                               |
| -------------------------- | ------------------------------------------------------------------------------------------------------- | ------------------------------------- |
| `FRONTEND_URL_WEB`         | URL a la que Stripe redirige tras un pago exitoso desde la web.                                         | `https://taquilladelsol.com/pago-exitoso` |
| `FRONTEND_URL_CANCEL_WEB`  | URL a la que Stripe redirige tras un pago cancelado desde la web.                                       | `https://taquilladelsol.com/pago-cancelado` |

---

## 5. Configuración de Stripe

Sigue estos pasos para configurar la integración con Stripe:

1.  **Obtener Llaves de API**:
    -   Ve a tu Dashboard de Stripe.
    -   Copia la **Publishable key** y pégala en `STRIPE_KEY` en tu archivo `.env`.
    -   Copia la **Secret key** y pégala en `STRIPE_SECRET` en tu archivo `.env`.

2.  **Configurar Webhooks**:
    -   El webhook es esencial para confirmar pagos de forma asíncrona y segura.
    -   Ve a la sección Webhooks en tu Dashboard.
    -   Haz clic en "Add an endpoint".
    -   **Endpoint URL**: `https://tu-dominio.com/api/stripe/webhook`. Para pruebas locales, usa un túnel (ver sección 6).
    -   **Events to send**: Haz clic en "Select events" y busca y selecciona `checkout.session.completed`.
    -   Haz clic en "Add endpoint".

3.  **Obtener el Secreto del Webhook**:
    -   Una vez creado el endpoint, haz clic en él.
    -   Busca la sección "Signing secret" y haz clic en "Click to reveal".
    -   Copia este valor y pégalo en `STRIPE_WEBHOOK_SECRET` en tu archivo `.env`.

---

## 6. Cloudflared / Túnel Público para Pruebas Locales

Para probar el webhook de Stripe en tu máquina local, necesitas exponer tu servidor local a internet. `cloudflared` es una excelente herramienta para esto.

1.  **Instalación de `cloudflared`**:
    ```bash
    npm install -g cloudflared
    ```

2.  **Iniciar el Servidor de Laravel**:
    Abre una terminal y ejecuta:
    ```bash
    php artisan serve
    # Laravel se ejecutará en http://localhost:8000
    ```

3.  **Abrir el Túnel**:
    Abre **otra terminal** y ejecuta:
    ```bash
    cloudflared tunnel --url http://localhost:8000
    ```
    `cloudflared` te proporcionará una URL pública temporal (ej. `https://random-words.trycloudflare.com`).

4.  **Actualizar el Webhook en Stripe**:
    -   Usa la URL generada por `cloudflared` y añádele la ruta del webhook: `https://random-words.trycloudflare.com/api/stripe/webhook`.
    -   Ve a tu configuración de webhooks en Stripe y actualiza la "Endpoint URL" con esta nueva dirección.
    -   Ahora, cuando realices un pago de prueba, Stripe podrá enviar notificaciones a tu servidor local.

> **Nota**: Cada vez que reinicies `cloudflared`, la URL cambiará. Deberás actualizarla en el Dashboard de Stripe.

---

## 7. Despliegue en Producción

Pasos recomendados para desplegar el proyecto en un servidor de producción (ej. VPS con Ubuntu).

1.  **Subir Archivos**:
    Usa Git para clonar el proyecto en el servidor. Es el método recomendado para facilitar futuras actualizaciones.
    ```bash
    git clone https://github.com/tu-usuario/taquilla_del_sol_laravel.git /var/www/taquilla_del_sol
    cd /var/www/taquilla_del_sol
    ```

2.  **Instalar Dependencias de Producción**:
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```

3.  **Configurar Archivo `.env` de Producción**:
    Copia `.env.example` a `.env` y rellena todas las variables con los valores de producción (base de datos, Stripe en modo LIVE, URLs, etc.). Asegúrate de que `APP_ENV=production` y `APP_DEBUG=false`.

4.  **Configurar Permisos de Laravel**:
    El servidor web necesita permisos de escritura en ciertas carpetas.
    ```bash
    sudo chown -R www-data:www-data /var/www/taquilla_del_sol/storage
    sudo chown -R www-data:www-data /var/www/taquilla_del_sol/bootstrap/cache
    sudo chmod -R 775 /var/www/taquilla_del_sol/storage
    sudo chmod -R 775 /var/www/taquilla_del_sol/bootstrap/cache
    ```

5.  **Configurar Servidor Web (Ejemplo Nginx)**:
    Crea un archivo de configuración en `/etc/nginx/sites-available/taquilla_del_sol`:
    ```nginx
    server {
        listen 80;
        server_name api.taquilladelsol.com;
        root /var/www/taquilla_del_sol/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```
    Activa el sitio y reinicia Nginx:
    ```bash
    sudo ln -s /etc/nginx/sites-available/taquilla_del_sol /etc/nginx/sites-enabled/
    sudo nginx -t
    sudo systemctl restart nginx
    ```

6.  **Configurar HTTPS con Certbot**:
    Es **obligatorio** para producción, especialmente para recibir pagos.
    ```bash
    sudo apt install certbot python3-certbot-nginx
    sudo certbot --nginx -d api.taquilladelsol.com
    ```

7.  **Ejecutar Migraciones y Enlace de Storage**:
    ```bash
    php artisan migrate --force # El flag --force es necesario en producción
    php artisan storage:link
    ```

8.  **Optimización de Laravel**:
    Crea cachés de configuración y rutas para mejorar el rendimiento.
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

---

## 8. Actualización del Servidor

Para actualizar el código en producción:

1.  **Activar Modo de Mantenimiento** (opcional pero recomendado):
    ```bash
    php artisan down
    ```
2.  **Obtener Cambios**:
    ```bash
    git pull origin main
    ```
3.  **Instalar Dependencias**:
    ```bash
    composer install --optimize-autoloader --no-dev
    npm install && npm run build # Si hay cambios en el frontend
    ```
4.  **Ejecutar Migraciones**:
    ```bash
    php artisan migrate --force
    ```
5.  **Limpiar y Regenerar Cachés**:
    ```bash
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
6.  **Desactivar Modo de Mantenimiento**:
    ```bash
    php artisan up
    ```

---

## 9. Notas Especiales del Proyecto

-   **Manejo de Archivos**: Las imágenes de los eventos se guardan en `storage/app/public/eventos/{slug-del-evento}`. El enlace simbólico (`storage:link`) hace que esta carpeta sea accesible desde `public/storage`.
-   **Roles y Habilidades**: La autenticación se basa en Laravel Sanctum. Los tokens generados en el login tienen "abilities" (`Cliente`, `Empresa`, `Admin`) que restringen el acceso a ciertos endpoints.
-   **Puerto por Defecto**: El comando `php artisan serve` utiliza el puerto `8000` por defecto.
-   **Seguridad**: En producción, asegúrate de que `APP_DEBUG` sea `false` y de que los permisos de los archivos y carpetas sean correctos para evitar vulnerabilidades.

---

## 10. Anexos

### Comandos Útiles de Laravel

-   Limpiar todas las cachés: `php artisan optimize:clear`
-   Ver lista de rutas: `php artisan route:list`
-   Ejecutar una tarea de la cola manualmente: `php artisan queue:work`
-   Revertir la última migración: `php artisan migrate:rollback`

### Flujo de Autenticación

1.  El cliente/empresa/admin envía credenciales (correo/clave, nit/clave, etc.) al endpoint de `login` correspondiente.
2.  Si las credenciales son válidas, Laravel genera un token de Sanctum con la "ability" correcta (`Cliente`, `Empresa` o `Admin`).
3.  La API devuelve el token al cliente.
4.  Para las siguientes peticiones a rutas protegidas, el cliente debe incluir el token en la cabecera `Authorization: Bearer {token}`.
5.  El middleware `auth:sanctum` y `abilities` validan el token y el rol antes de permitir el acceso al controlador.