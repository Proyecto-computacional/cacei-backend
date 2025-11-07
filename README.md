# Backend

Este es el backend del proyecto, desarrollado con Laravel y PostgreSQL.

## Requisitos

Asegúrate de tener instalados los siguientes requisitos antes de continuar:

-   [PHP 8.1+](https://www.php.net/)
-   [Composer](https://getcomposer.org/)
-   [PostgreSQL](https://www.postgresql.org/)
-   [PgAdmin 4](https://www.pgadmin.org/) (opcional para administración visual)

## Instalación

1. Clona el repositorio:

    ```sh
    git clone <URL_DEL_REPOSITORIO>
    cd cacei-backend
    ```

2. Instala las dependencias con Composer:

    ```sh
    composer install
    ```

3. Copia el archivo de configuración y genera la clave de la aplicación:

    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. Configura el archivo `.env` con los datos de tu base de datos PostgreSQL:

    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=tu_base_de_datos
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```

5. Ejecuta las migraciones:

    ```sh
    php artisan migrate
    php artisan db:seed
    ```

-   Asegurarse de tener instalado el paquete: spatie/laravel-backup \*

```sh
   composer require spatie/laravel-backup
```

-   Para el manejo de documentos Word, instalar el paquete: phpoffice/phpword \*

```sh
   composer require phpoffice/phpword
```

## Ejecutar el Servidor (Desarrollo)

Para iniciar el servidor localmente necesitarás dos terminales, en la primera, usa:

```sh
npm run dev
```

En la segunda:

```sh
php artisan serve
```

-   Al realizar cambios en los archivos de configuración, ejecutar estos comandos:

```sh
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan serve
```

## Ejecutar el Servidor (Producción)

Para iniciar el servidor, usa:

```sh
php artisan serve
```

## Pruebas de la API

Para probar los endpoints, puedes usar **Postman**.

## Helpers y configuraciones

### Programador de tareas (Scheduler)

Laravel permite programar tareas automáticas usando el scheduler. Las tareas se definen en `app/Console/Kernel.php` usando el método `schedule`. Ejemplo de tareas programadas:

```php
$schedule->command('notifications:generate')->dailyAt('06:00');
$schedule->command('sessions:clean')->daily();
```

Para que el programador de tareas funcione correctamente, es necesario agregar la siguiente línea al cron del servidor, para que Laravel ejecute las tareas cada minuto:

```sh
php artisan schedule:run >> /dev/null 2>&1
```

No olvidar que para usar helpers agregarlos en el autoload de composer.json y ejecutar el comando:

```sh
composer dump-autoload
```

### Configuración de PHP

Para que la aplicación permita manejar tamaños elevados de evidencias y trabajos, es necesario modificar `php.ini`, almacenado en los archivos de PHP del dispositivo

Busca el directorio `php/php.ini`, donde se deben editar estos parámetros:

```ini
; para subir archivos
upload_max_filesize = 100M
post_max_size = 100M

; para procesos pesados
memory_limit = 512M

; para tiempos elevados de trabajos
max_execution_time = 300
max_input_time = 300

; para horario de zona
date.timezone = "America/Mexico_City"

; en caso de tener problemas de autenticación de la aplicación
curl.cainfo = "C:\xampp\php\extras\ssl\cacert.pem"
openssl.cafile = "C:\xampp\php\extras\ssl\cacert.pem"

; descomentar las siguientes líneas para otorgar autorización para usar pg admin
extension=pdo_pgsql
extension=pgsql
```

## Troubleshooting

-   Error 413 (Request Entity Too Large -> revisar `php.ini` los parámetros `post_max_size` y `upload_max_filesize`
-   Error 500 -> revisar registros en `storage/logs/laravel.log`
-   Problemas de sesión -> revisar permisos del directorio `storage/framework/sessions`
-   Problemas de autenticación -> revisar `php.ini` los parámetros `curl.cainfo` y `openssl.cafile`. Sustituir por "C:ruta\a\xampp\php\extras\ssl\cacert.pem"
