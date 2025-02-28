# Backend 

Este es el backend del proyecto, desarrollado con Laravel y PostgreSQL.

## Requisitos

Asegúrate de tener instalados los siguientes requisitos antes de continuar:

- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [PostgreSQL](https://www.postgresql.org/)
- [PgAdmin 4](https://www.pgadmin.org/) (opcional para administración visual)

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
   ```

5. Ejecuta las migraciones (*pendientes por ahora, no ejecutar las migraciones*):

   ```sh
   php artisan migrate 
   ```

## Ejecutar el Servidor

Para iniciar el servidor localmente, usa:

```sh
php artisan serve
```

Por defecto, la API correrá en `http://127.0.0.1:8000`.

## Pruebas de la API

Para probar los endpoints, puedes usar **Postman**.
