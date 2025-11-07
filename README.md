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
    git clone TU_REPO_URL backend
    cd backend
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
    SANCTUM_TOKEN_INACTIVITY_LIMIT=20
    ```

5. Ejecuta las migraciones (_queda pendiente la asignación de roles por su cargo_):

    ```sh
    php artisan migrate
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

## Ejecutar el Servidor (Producción)

Construye el proyecto:

```sh
npm run build
```

Para iniciar el servidor, usa:

```sh
php artisan serve
```

## Pruebas de la API

Para probar los endpoints, puedes usar **Postman**.

## Helpers

## Programador de tareas (Scheduler)

Laravel permite programar tareas automáticas usando el scheduler. Las tareas se definen en `app/Console/Kernel.php` usando el método `schedule`. Ejemplo de tareas programadas:

```php
$schedule->command('notifications:generate')->dailyAt('06:00');
$schedule->command('sessions:clean')->daily();
```

Para que el programador de tareas funcione correctamente, es necesario agregar la siguiente línea al cron de tu servidor, para que Laravel ejecute las tareas cada minuto:

```sh
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Reemplaza `/ruta/a/tu/proyecto` por la ruta real de tu proyecto.


No olvidar que para usar helpers agregarlos en el autoload de composer.json y ejecutar el comando:
composer dump-autoload
