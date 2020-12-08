# FondosAFP

Información gratuita y actualizada de los fondos de pensiones de las AFPs de Chile.

[![codecov](https://codecov.io/bb/nantrack/fondosafp/branch/master/graph/badge.svg?token=B0frAtaWJ9)](https://codecov.io/bb/nantrack/fondosafp)

## Servicios relacionados

*  [Circle CI](https://circleci.com/bb/NaNtrack/fondosafp)
*  [Codecov](https://codecov.io/bb/nantrack/fondosafp)

## Instalación

# Paso 1
```
    git clone git@github.com:FondosAFP/website.git
    cd website/
    composer install
```


## Configuración

*  Copiar el archivo **config/app.default.php** a **config/app.php**
*  Configurar correctamente el **Datasource default**

```php
    'Datasources' => [
        'default' => [
            ...
            'host' => '127.0.0.1',
            'port' => '<puerto de mysql>',
            'username' => '<username>',
            'password' => '<password>',
            'database' => '<nombre de la base de datos>',
            ...
```
* Crear la base de datos fondosafp en mysql y correr el archivo **config/schema/fondosafp.sql**

* Correr las migraciones

```
    bin/cake migrations migrate
```

*  Configurar las claves de Google para el inicio de sesión
   (https://console.developers.google.com/apis/credentials?project=YOUR-PROJECT-ID)

```php
    'Google' => [
        'application_name' => 'FondosAFP',
        'client_id' => 'YOUR CLIENT_ID',
        'client_secret' => 'YOUR CLIENT_SECRET',
        'redirect_uri' => 'http://localhost/google_callback',
    ],
```

*  Configurar las claves de Facebook para el inicio de sesión
   (https://developers.facebook.com/apps/)

```php
    'Facebook' => [
        'app_id' => 'YOUR CLIENT ID',
        'app_secret' => 'YOUR CLIENT SECRET',
        'default_graph_version' => 'v2.7',
        'persistent_data_handler' => 'session'
    ]
```

## Testing

Para poder testear la aplicación corra el siguiente comando en la terminal:

    phpunit
