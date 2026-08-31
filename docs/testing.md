# Estrategia y Evidencia de Pruebas

## 1. Objetivo

El objetivo de las pruebas es verificar el comportamiento funcional de la
aplicación, prevenir regresiones y comprobar que las principales reglas de
negocio se mantengan correctamente.

La solución utiliza Pest como interfaz de pruebas sobre PHPUnit y las
herramientas de testing proporcionadas por Laravel y Livewire.

Actualmente la suite completa cuenta con:

```text
53 pruebas satisfactorias
```

## 2. Herramientas utilizadas

| Herramienta | Uso |
|---|---|
| Pest | Definición y ejecución de pruebas |
| PHPUnit | Motor de pruebas |
| Laravel Testing | Pruebas HTTP, base de datos y aplicación |
| Livewire Testing | Pruebas de componentes Livewire |
| Laravel Sanctum | Simulación de usuarios autenticados en API |
| PostgreSQL | Base de datos utilizada también durante las pruebas |

## 3. Base de datos de pruebas

Para evitar afectar la información del ambiente de desarrollo se utiliza una
base de datos independiente:

`flechaDB_test`

La configuración se mantiene mediante:

`.env.testing`

Ejemplo:

```env
APP_ENV=testing

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flechaDB_test
DB_USERNAME=postgres
DB_PASSWORD=

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array
```

Las credenciales reales no deben almacenarse en el repositorio.

## 4. Uso de PostgreSQL en pruebas

Inicialmente, la configuración estándar del entorno de pruebas intentaba utilizar:

`SQLite :memory:`

Sin embargo, la aplicación fue diseñada para PostgreSQL y utiliza funcionalidades
específicas del motor, como:

`ILIKE`

Por este motivo se decidió ejecutar las pruebas contra PostgreSQL.

Esto permite que el ambiente de pruebas represente de mejor manera el
comportamiento real de la aplicación.

## 5. Aislamiento entre pruebas

Las pruebas funcionales utilizan:

`uses(RefreshDatabase::class);`

Esto permite ejecutar cada prueba sobre un estado limpio de la base de datos.

El proceso utiliza exclusivamente:

`flechaDB_test`

por lo que la información existente en:

`flechaDB`

no se modifica durante la ejecución de la suite.

## 6. Ejecución de pruebas

Para ejecutar toda la suite:

`php artisan test`

Resultado actual:

53 passed

También pueden ejecutarse archivos individuales.

Por ejemplo:

`php artisan test tests/Feature/BookApiTest.php`

o:

`php artisan test tests/Feature/BookLivewireTest.php`

## 7. Pruebas incluidas por el Starter Kit

El proyecto conserva las pruebas generadas por el starter kit de Laravel.

Estas pruebas verifican funcionalidades como:

- Inicio de sesión.
- Cierre de sesión.
- Registro de usuarios.
- Verificación de correo.
- Recuperación de contraseña.
- Confirmación de contraseña.
- Autenticación de dos factores.
- Acceso al Dashboard.
- Actualización del perfil.
- Actualización de contraseña.
- Eliminación de cuenta.

Estas pruebas funcionan también como pruebas de regresión.

Si una modificación realizada en el sistema afecta accidentalmente alguna de
estas funcionalidades, la suite puede detectarlo.

## 8. Pruebas de la API de libros

Archivo:

`tests/Feature/BookApiTest.php`

Se implementaron ocho pruebas específicas.

### API-01 - Protección de endpoint

**Objetivo:**

Comprobar que un usuario sin autenticación no pueda consultar los libros.

**Entrada:**
`GET /api/books`

sin token.

**Resultado esperado:**
401 Unauthorized

### API-02 - Listado autenticado

**Objetivo:**

Verificar que un usuario autenticado pueda consultar el listado de libros.

**Resultado esperado:**
200 OK

### API-03 - Creación de libro

**Objetivo:**

Comprobar que un usuario autenticado pueda registrar un libro correctamente.

Se verifican:

- Título.
- Autor.
- Género.
- Año de publicación.
- Persistencia en base de datos.

**Resultado esperado:**
201 Created

### API-04 - Generación automática de book_code

Durante la prueba de creación también se verifica que el código generado cumpla
el formato requerido.

Expresión utilizada:

`^[A-Z][0-9]{2}$`

Por ejemplo:

```text
A12
B04
Z99
```

Esto comprueba que el código contenga:

- Una letra mayúscula.
- Dos dígitos.

### API-05 - Validaciones obligatorias

**Objetivo:**

Enviar una solicitud sin los campos obligatorios.

**Campos comprobados:**
- title
- author_id
- genre_id
- publication_year

**Resultado esperado:**
422 Unprocessable Entity

### API-06 - Búsqueda de libros

**Objetivo:**

Comprobar que el endpoint permita localizar libros utilizando el parámetro:

`search`

Ejemplo:

`GET /api/books?search=Rayuela`

**Resultado esperado:**

La respuesta debe contener el libro correspondiente.

### API-07 - Actualización de libro

**Objetivo:**

Comprobar que los datos del libro puedan modificarse correctamente.

Además se valida una regla importante:

`book_code` no debe cambiar

El identificador generado durante la creación debe permanecer sin modificaciones.

### API-08 - Eliminación física

**Objetivo:**

Comprobar que un libro pueda eliminarse mediante la API.

**Entrada:**
`DELETE /api/books/{id}`

**Resultado esperado:**
204 No Content

También se comprueba que el registro deje de existir físicamente en la base de
datos.

## 9. Pruebas Livewire de libros

Archivo:

`tests/Feature/BookLivewireTest.php`

Se implementaron seis pruebas para comprobar el comportamiento del frontend.

### LW-B01 - Renderizado del componente

Verifica que:

`books.index`

pueda renderizarse correctamente.

### LW-B02 - Creación de libro

Simula el llenado del formulario Livewire y ejecuta:

`save()`

Se verifica:

- Persistencia del libro.
- Autor.
- Género.
- Año.
- Código generado automáticamente.

### LW-B03 - Validación del formulario

Ejecuta el formulario sin proporcionar los campos obligatorios.

Se comprueba que Livewire reporte errores de validación.

### LW-B04 - Edición de libro

Ejecuta:

`edit()`

y posteriormente:

`save()`

Se verifica que:

- Los datos sean actualizados.
- El identificador del libro permanezca igual.

### LW-B05 - Eliminación de libro

Ejecuta:

`delete()`

y comprueba que el registro sea eliminado físicamente.

### LW-B06 - Búsqueda

Se establece el valor:

`search`

en el componente Livewire y se verifica que el libro correspondiente aparezca
en la interfaz renderizada.

## 10. Pruebas de autores y géneros

Archivo:

`tests/Feature/CatalogLivewireTest.php`

Este grupo contiene seis pruebas.

### CAT-01 - Creación de autor

Se registra un autor utilizando texto en minúsculas:

```text
octavio paz
```

Se comprueba que el mutator del modelo lo almacene como:

```text
Octavio Paz
```

Esta prueba también verifica indirectamente la normalización mediante Title Case.

### CAT-02 - Actualización de autor

Se modifica un autor existente y se verifica la persistencia del nuevo nombre.

### CAT-03 - Restricción de eliminación de autor

Se crea:

```text
Author
   |
   └── Book
```

Posteriormente se intenta eliminar el autor.

El sistema debe conservar:

- El autor.
- El libro relacionado.

Esto comprueba la regla de negocio que impide eliminar autores utilizados por
libros.

### CAT-04 - Creación de género

Se registra:

```text
ciencia ficción
```

y se verifica que sea almacenado como:

```text
Ciencia Ficción
```

### CAT-05 - Actualización de género

Se verifica que un género pueda modificarse correctamente desde Livewire.

### CAT-06 - Restricción de eliminación de género

Se crea:

```text
Genre
   |
   └── Book
```

y posteriormente se intenta eliminar el género.

El sistema debe conservar ambos registros.

La validación de aplicación se complementa con la Foreign Key configurada con:

`RESTRICT`

en PostgreSQL.

## 11. Validación en múltiples niveles

La aplicación utiliza diferentes niveles de protección.

Por ejemplo, al registrar un libro:

```text
Solicitud
    |
    v
Laravel Validation
    |
    v
Eloquent
    |
    v
Foreign Keys / UNIQUE
    |
    v
PostgreSQL
```

Esto evita depender únicamente de la interfaz para garantizar la integridad de
los datos.

## 12. Pruebas de regresión

Antes de considerar una versión estable del proyecto se ejecuta:

`php artisan test`

La aplicación se considera candidata para entrega únicamente cuando toda la
suite permanece satisfactoria.

Resultado de referencia actual:

53 pruebas satisfactorias

Adicionalmente se verificó correctamente la compilación de recursos frontend:

`npm run build`

## 13. Incidencias detectadas mediante las pruebas

Durante la preparación del ambiente de pruebas se detectaron inconsistencias en
el historial de migraciones que no eran visibles en la base de datos de
desarrollo ya existente.

Entre ellas:

- Definición duplicada de `is_active`.
- Restricción UNIQUE duplicada para autores.
- Restricción UNIQUE duplicada para géneros.
- Intento de eliminar un índice que no había sido creado durante una migración limpia.

Estas inconsistencias fueron corregidas hasta conseguir que una instalación
desde cero pudiera ejecutar correctamente todas las migraciones.

Esto confirmó también que las migraciones del proyecto pueden reconstruir la
estructura completa de la base de datos en un ambiente limpio.

## 14. Resultado

La combinación de pruebas de:

- Autenticación.
- API REST.
- Persistencia.
- Validación.
- Componentes Livewire.
- Reglas de negocio.
- Integridad referencial.

proporciona cobertura funcional sobre los principales flujos requeridos por el
ejercicio.

La suite actual ejecuta satisfactoriamente:

53 pruebas
