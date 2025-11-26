# Documentación de la API - Taquilla del Sol

## Descripción General

Esta es la documentación oficial para la API REST de **Taquilla del Sol**, un sistema de gestión de taquillas y venta de entradas para eventos. La API proporciona endpoints para gestionar usuarios (clientes, empresas, administradores), eventos, categorías, asientos, tickets y pagos.

La URL base para todos los endpoints es `http://tu-dominio.com/api/`.

## Autenticación

La API utiliza **Laravel Sanctum** para la autenticación basada en tokens. Para acceder a los endpoints protegidos, debes seguir estos pasos:

1.  **Obtener un Token**: Realiza una petición `POST` a uno de los endpoints de login (`/login/cliente`, `/login/empresa`, `/login/admin`). Si las credenciales son correctas, la API devolverá un token de acceso.

2.  **Enviar el Token**: En cada petición a un endpoint protegido, debes incluir el token en la cabecera `Authorization`.

    ```
    Authorization: Bearer {tu_token_de_acceso}
    ```

### Roles y Habilidades (Abilities)

Sanctum gestiona el acceso a través de "abilities" (habilidades) que se asignan al token en el momento del login. Los roles definidos en la API son:

-   `Cliente`: Acceso a funcionalidades de compra y gestión de perfil de cliente.
-   `Empresa`: Acceso a funcionalidades de gestión de eventos y perfil de empresa.
-   `Admin`: Acceso total a la plataforma.

---

## Módulos de la API

### 1. Autenticación y Verificación

Endpoints para registro, inicio de sesión y recuperación de contraseñas.

#### Registrar Cliente

-   **Método**: `POST`
-   **Ruta**: `/api/registrar/cliente`
-   **Descripción**: Crea una nueva cuenta de cliente.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "nombre": "Juan",
        "apellido": "Pérez",
        "documento": 12345678,
        "fecha_nacimiento": "1990-05-15",
        "telefono": 3001234567,
        "sexo": "M",
        "correo": "juan.perez@example.com",
        "clave": "password123"
    }
    ```
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "message": "Cliente Juan registrado correctamente",
        "user": { /* ...datos del cliente... */ },
        "token_access": "1|xxxxxxxxxxxxxxxx",
        "rol": "Cliente",
        "token_type": "Bearer"
    }
    ```
-   **Errores Comunes**:
    -   `400 Bad Request`: Errores de validación (campos faltantes, correo/documento duplicado).

#### Login de Cliente

-   **Método**: `POST`
-   **Ruta**: `/api/login/cliente`
-   **Descripción**: Inicia sesión como cliente y obtiene un token de acceso.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "correo": "juan.perez@example.com",
        "clave": "password123"
    }
    ```
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "message": "Inicio de sesión exitoso",
        "token": "1|xxxxxxxxxxxxxxxx",
        "token_type": "Bearer",
        "cliente": { /* ...datos del cliente... */ }
    }
    ```
-   **Errores Comunes**:
    -   `401 Unauthorized`: Credenciales incorrectas.
    -   `402 Payment Required`: Error de validación (código de estado no estándar, debería ser 422 o 400).

#### Login de Empresa

-   **Método**: `POST`
-   **Ruta**: `/api/login/empresa`
-   **Descripción**: Inicia sesión como empresa y obtiene un token de acceso.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "nit": "900123456-7",
        "clave": "password123"
    }
    ```
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "message": "Inicio de sesion exitoso",
        "token": "2|xxxxxxxxxxxxxxxx",
        "token_type": "Bearer",
        "empresa": { /* ...datos de la empresa... */ }
    }
    ```

#### Login de Administrador

-   **Método**: `POST`
-   **Ruta**: `/api/login/admin`
-   **Descripción**: Inicia sesión como administrador y obtiene un token de acceso.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "documento": 10203040,
        "clave": "adminpass"
    }
    ```

#### Login Compartido (Admin/Cliente)

-   **Método**: `POST`
-   **Ruta**: `/api/login/admin-cliente`
-   **Descripción**: Inicia sesión con un correo y clave, y el sistema determina si es un admin o un cliente.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "correo": "usuario@example.com",
        "clave": "password123"
    }
    ```

#### Enviar Código de Verificación

-   **Método**: `POST`
-   **Ruta**: `/api/envio/codigo/verificacion`
-   **Descripción**: Envía un código de 6 dígitos al correo electrónico proporcionado si el usuario existe.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "email": "usuario_registrado@example.com"
    }
    ```

#### Verificar Código

-   **Método**: `POST`
-   **Ruta**: `/api/verificar/codigo`
-   **Descripción**: Valida si el código proporcionado para un correo es correcto y no ha expirado.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "correo": "usuario_registrado@example.com",
        "codigo": 123456
    }
    ```

#### Olvidé mi Clave (Cliente, Empresa, Admin)

-   **Método**: `POST`
-   **Rutas**:
    -   `/api/olvide/clave/cliente`
    -   `/api/olvide/clave/empresa`
    -   `/api/olvide/clave/admin`
-   **Descripción**: Permite restablecer la contraseña de un usuario (cliente, empresa o admin) usando su correo. Requiere que el código de verificación haya sido validado previamente.
-   **Acceso**: Público
-   **Body (JSON)**:
    ```json
    {
        "correo": "usuario_registrado@example.com",
        "clave": "nuevaClaveSegura123"
    }
    ```

#### Logout (Cliente, Empresa, Admin)

-   **Método**: `POST`
-   **Rutas**:
    -   `/api/logout/cliente`
    -   `/api/logout/empresa`
    -   `/api/logout/admin`
-   **Descripción**: Invalida el token de acceso actual del usuario.
-   **Acceso**: Autenticado (`Cliente`, `Empresa`, `Admin` según corresponda).
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "message": "Sesión cerrada correctamente"
    }
    ```

---

### 2. Clientes

Endpoints para la gestión de usuarios clientes.

| Método | Ruta                               | Descripción                               | Acceso        |
| :----- | :--------------------------------- | :---------------------------------------- | :------------ |
| `GET`  | `/api/listarClientes`              | Lista todos los clientes.                 | Admin         |
| `GET`  | `/api/verCliente/{id}`             | Obtiene los datos de un cliente.          | Admin         |
| `GET`  | `/api/me/cliente`                  | Obtiene los datos del cliente autenticado.| `Cliente`     |
| `PUT`  | `/api/actualizarCliente/{id}`      | Actualiza el perfil de un cliente.        | `Cliente`     |
| `PUT`  | `/api/cambiar/clave/cliente/{id}`  | Cambia la contraseña de un cliente.       | `Cliente`     |
| `PUT`  | `/api/cambiar/correo/cliente/{id}` | Cambia el correo de un cliente.           | `Cliente`     |
| `DELETE`| `/api/eliminarCliente/{id}`        | Elimina un cliente (lógica de admin).     | Admin         |
| `DELETE`| `/api/cliente/eliminar-cuenta/{id}`| Elimina la cuenta del propio cliente.     | `Cliente`     |

#### Actualizar Cliente

-   **Ruta**: `PUT /api/actualizarCliente/{id}`
-   **Body (JSON)**: (Campos opcionales)
    ```json
    {
        "nombre": "Juan Carlos",
        "apellido": "Pérez",
        "documento": 12345678,
        "fecha_nacimiento": "1990-05-15",
        "sexo": "M",
        "telefono": "3009876543"
    }
    ```

---

### 3. Empresas

Endpoints para la gestión de empresas organizadoras de eventos.

| Método | Ruta                               | Descripción                               | Acceso        |
| :----- | :--------------------------------- | :---------------------------------------- | :------------ |
| `GET`  | `/api/listarEmpresas`              | Lista todas las empresas.                 | Admin         |
| `GET`  | `/api/empresa/{id}`                | Obtiene los datos de una empresa.         | Admin         |
| `POST` | `/api/registrarEmpresa`            | Registra una nueva empresa.               | Público       |
| `PUT`  | `/api/actualizarEmpresa/{id}`      | Actualiza el perfil de una empresa.       | `Empresa`     |
| `PUT`  | `/api/cambiar/correo/empresa/{id}` | Cambia el correo de una empresa.          | `Empresa`     |
| `DELETE`| `/api/eliminarEmpresa/{id}`        | Elimina una empresa.                      | Admin         |
| `GET`  | `/api/total-vendido-empresa-año/{id}`| Total vendido en el año actual.           | `Empresa`     |
| `GET`  | `/api/total-eventos-realizados/{id}`| Total de eventos realizados.              | `Empresa`     |
| `GET`  | `/api/total-asientos-vendidos/{id}`| Total de asientos vendidos.               | `Empresa`     |
| `GET`  | `/api/crecimiento-mensual/{id}`    | Crecimiento de ventas vs año anterior.    | `Empresa`     |

#### Registrar Empresa

-   **Ruta**: `POST /api/registrarEmpresa`
-   **Body (JSON)**:
    ```json
    {
        "nombre_empresa": "Eventos S.A.S",
        "nit": 900123456,
        "representante_legal": "Ana Gómez",
        "documento_representante": 87654321,
        "nombre_contacto": "Carlos Ruiz",
        "telefono": "3101234567",
        "correo": "contacto@eventossas.com",
        "clave": "empresa123"
    }
    ```

---

### 4. Administradores

Endpoints para la gestión de administradores del sistema.

| Método | Ruta                               | Descripción                               | Acceso    |
| :----- | :--------------------------------- | :---------------------------------------- | :-------- |
| `GET`  | `/api/listarAdministradores`       | Lista todos los administradores.          | Admin     |
| `GET`  | `/api/me/administrador`            | Obtiene los datos del admin autenticado.  | `Admin`   |
| `POST` | `/api/registrarAdministradores`    | Registra un nuevo administrador.          | Admin     |
| `PUT`  | `/api/actualizarAdministradores/{id}`| Actualiza el perfil de un administrador.  | Admin     |
| `PUT`  | `/api/cambiarClave/{id}`           | Cambia la clave de un administrador.      | Admin     |

---

### 5. Categorías

Endpoints para gestionar las categorías de los eventos.

| Método | Ruta                               | Descripción                     | Acceso    |
| :----- | :--------------------------------- | :------------------------------ | :-------- |
| `GET`  | `/api/listarCategorias`            | Lista todas las categorías.     | Público   |
| `POST` | `/api/registrarCategoria`          | Crea una nueva categoría.       | Admin     |
| `PUT`  | `/api/actualizarCategoria/{id}`    | Actualiza una categoría.        | Admin     |
| `DELETE`| `/api/eliminarCategoria/{id}`      | Elimina una categoría.          | Admin     |

---

### 6. Eventos

Endpoints para la gestión de eventos.

| Método | Ruta                               | Descripción                               | Acceso        |
| :----- | :--------------------------------- | :---------------------------------------- | :------------ |
| `GET`  | `/api/listarEventos`               | Lista todos los eventos.                  | Público       |
| `GET`  | `/api/eventos/disponibles`         | Lista solo los eventos con estado 'activo'.| Público       |
| `GET`  | `/api/evento/{id}`                 | Obtiene los detalles de un evento.        | Público       |
| `POST` | `/api/registrarEventos`            | Crea un nuevo evento.                     | `Empresa`     |
| `PUT`  | `/api/actualizarEventos/{id}`      | Actualiza un evento.                      | `Empresa`     |
| `DELETE`| `/api/eliminarEventos/{id}`        | Elimina un evento.                        | `Empresa`/Admin|
| `POST` | `/api/cambiar/estado/evento/{id}`  | Cambia el estado de un evento.            | Admin         |
| `GET`  | `/api/proxima-funcion/{id}`        | Muestra la próxima función de un cliente. | `Cliente`     |
| `GET`  | `/api/contador/proxima-funcion/{id}`| Cuenta las funciones próximas de un cliente.| `Cliente`     |
| `GET`  | `/api/contador/funciones-vistas/{id}`| Cuenta las funciones vistas de un cliente.| `Cliente`     |
| `GET`  | `/api/contador/obras-activas/{id}` | Cuenta las obras activas de una empresa.  | `Empresa`     |

#### Registrar Evento

-   **Ruta**: `POST /api/registrarEventos`
-   **Body (`multipart/form-data`)**:
    -   `titulo` (string)
    -   `descripcion` (string)
    -   `fecha` (date, YYYY-MM-DD)
    -   `hora_inicio` (string, HH:MM:SS)
    -   `hora_final` (string, HH:MM:SS)
    -   `imagen` (file, jpg,jpeg,png,webp)
    -   `estado` (string, `pendiente`)
    -   `empresa_id` (integer)
    -   `categoria_id` (integer)
    -   `precioPrimerPiso` (integer)
    -   `precioSegundoPiso` (integer)
    -   `precioGeneral` (integer)

#### Cambiar Estado del Evento

-   **Ruta**: `POST /api/cambiar/estado/evento/{id}`
-   **Descripción**: Cambia el estado de un evento. Si el nuevo estado es `activo`, genera automáticamente los asientos disponibles para ese evento.
-   **Acceso**: Admin
-   **Body (JSON)**:
    ```json
    {
        "estado": "activo"
    }
    ```
    *Valores permitidos para `estado`: `activo`, `pendiente`, `cancelado`, `finalizado`.*

---

### 7. Tickets

Endpoints para la gestión de tickets o boletos.

| Método | Ruta                               | Descripción                               | Acceso        |
| :----- | :--------------------------------- | :---------------------------------------- | :------------ |
| `GET`  | `/api/listarTickets`               | Lista todos los tickets.                  | Admin         |
| `POST` | `/api/registrarTickets`            | Crea un nuevo ticket (uso interno).       | Admin         |
| `PUT`  | `/api/actualizarTickets/{id}`      | Actualiza un ticket.                      | Admin         |
| `DELETE`| `/api/eliminarTickets/{id}`        | Elimina un ticket.                        | Admin         |
| `POST` | `/api/verificador-ticket`          | Verifica si un ticket es válido y no ha sido usado. | Admin |
| `GET`  | `/api/mis-tickets/cliente/{id}`    | Lista todos los tickets de un cliente.    | `Cliente`     |
| `GET`  | `/api/ticket/pdf/{ticketId}`       | Descarga el ticket en formato PDF.        | `Cliente`     |
| `GET`  | `/api/ticket/detalles/{id}`        | Obtiene la información detallada de un ticket. | `Cliente`     |

#### Verificar Uso de Ticket

-   **Ruta**: `POST /api/verificador-ticket`
-   **Descripción**: Se usa en la entrada del evento para validar un ticket y marcarlo como `usado`.
-   **Acceso**: Admin (o un rol específico de portero).
-   **Body (JSON)**:
    ```json
    {
        "ticket_id": 123
    }
    ```
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "message": "🎭 Ticket válido. ¡Bienvenido al Teatro del Sol!",
        "ticket": { /* ...datos del ticket actualizado... */ }
    }
    ```
-   **Errores Comunes**:
    -   `404 Not Found`: El ticket no existe.
    -   `200 OK` con `success: false`: El ticket ya ha sido usado.

---

### 8. Pagos

Endpoints para procesar pagos, principalmente a través de Stripe.

| Método | Ruta                               | Descripción                               | Acceso        |
| :----- | :--------------------------------- | :---------------------------------------- | :------------ |
| `GET`  | `/api/listarPagos`                 | Lista todos los registros de pago.        | Admin         |
| `POST` | `/api/registrarPagos`              | Registra un pago manualmente.             | Admin         |
| `PUT`  | `/api/actualizarPagos/{id}`        | Actualiza un registro de pago.            | Admin         |
| `DELETE`| `/api/eliminarPagos/{id}`          | Elimina un registro de pago.              | Admin         |
| `POST` | `/api/pago/stripe`                 | Crea una sesión de pago en Stripe para App Móvil. | `Cliente`     |
| `POST` | `/api/pago/stripe-web`             | Crea una sesión de pago en Stripe para Web. | `Cliente`     |
| `GET`  | `/api/pago-exitoso/...`            | Callback de Stripe para pago exitoso (App). | Público       |
| `GET`  | `/api/pago-exitoso-web`            | Callback de Stripe para pago exitoso (Web). | Público       |
| `POST` | `/api/stripe/webhook`              | Recibe eventos de webhook desde Stripe.   | Público (Stripe)|

#### Crear Sesión de Pago (Stripe)

-   **Rutas**:
    -   `POST /api/pago/stripe` (para App Móvil)
    -   `POST /api/pago/stripe-web` (para Web)
-   **Descripción**: Inicia un proceso de pago en Stripe para una lista de asientos.
-   **Acceso**: `Cliente`
-   **Body (JSON)**:
    ```json
    {
        "asientos": [101, 102],
        "id_evento": 5,
        "total": 120000,
        "id_cliente": 12
    }
    ```
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "url": "https://checkout.stripe.com/pay/cs_test_..."
    }
    ```

#### Callback de Pago Exitoso

-   **Rutas**:
    -   `GET /api/pago-exitoso/{idsAsientos}/{idCliente}/{total}/{idEvento}`
    -   `GET /api/pago-exitoso-web?asientos=...&cliente=...&total=...&evento=...`
-   **Descripción**: Endpoint al que Stripe redirige tras un pago exitoso. Procesa la compra, crea el ticket, marca los asientos como no disponibles y envía un correo de confirmación. Los parámetros vienen encriptados en la URL.
-   **Acceso**: Público (invocado por el navegador del usuario).

#### Stripe Webhook

-   **Ruta**: `POST /api/stripe/webhook`
-   **Descripción**: Endpoint para recibir notificaciones asíncronas de Stripe sobre el estado de los pagos (completado, fallido, etc.).
-   **Acceso**: Público (debe ser configurado en el Dashboard de Stripe).

---

### 9. Asientos

Endpoints para consultar la disponibilidad de asientos.

| Método | Ruta                               | Descripción                               | Acceso    |
| :----- | :--------------------------------- | :---------------------------------------- | :-------- |
| `GET`  | `/api/listarAsientos`              | Lista todos los asientos maestros del teatro. | Admin     |
| `GET`  | `/api/asientos/evento/{id}`        | Lista los asientos y su disponibilidad para un evento específico. | Público   |
| `POST` | `/api/registrarAsientos`           | (No implementado en controlador)          | -         |
| `GET`  | `/api/mostrarAsiento/{id}`         | (No implementado en controlador)          | -         |
| `PUT`  | `/api/actualizarAsientos/{id}`     | (No implementado en controlador)          | -         |
| `DELETE`| `/api/eliminarAsientos/{id}`       | (No implementado en controlador)          | -         |

#### Asientos por Evento

-   **Ruta**: `GET /api/asientos/evento/{id}`
-   **Descripción**: Devuelve una lista completa de los asientos para un evento, indicando su ubicación, precio, disponibilidad y el ID único del asiento para ese evento (`id_asiento_evento`).
-   **Acceso**: Público
-   **Respuesta Exitosa (200 OK)**:
    ```json
    {
        "success": true,
        "asientos": [
            {
                "fila": "A",
                "numero": 1,
                "ubicacion": "Zona General",
                "precio": 50000,
                "disponible": true,
                "id_asiento_evento": 1
            },
            {
                "fila": "A",
                "numero": 2,
                "ubicacion": "Zona General",
                "precio": 50000,
                "disponible": false,
                "id_asiento_evento": 2
            }
            // ... más asientos
        ]
    }
    ```