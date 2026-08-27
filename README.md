# Flecha App - Sistema de Gestión de Biblioteca

Aplicación web desarrollada con Laravel, Livewire y PostgreSQL para la gestión
de libros, autores y géneros.

El sistema incluye:

- CRUD de libros.
- CRUD de autores.
- CRUD de géneros.
- Búsqueda y paginación.
- Generación automática de identificadores de libro.
- Autenticación web.
- API REST protegida con Laravel Sanctum.
- Validaciones reutilizables.
- Pruebas automatizadas.
- Documentación técnica y de arquitectura.

---

## Tecnologías

- PHP 8.3+
- Laravel 13
- Livewire 4
- Flux UI
- Tailwind CSS
- Vite
- PostgreSQL
- Laravel Sanctum
- Laravel Fortify
- Pest / PHPUnit
- Git

---

## Requerimientos

Antes de ejecutar el proyecto se requiere:

- PHP 8.3 o superior.
- Composer.
- PostgreSQL.
- Node.js.
- npm.
- Git.

Extensiones PHP principales:

- `pdo_pgsql`
- `pgsql`
- `curl`
- `fileinfo`
- `mbstring`
- `openssl`
- `xml`
- `ctype`
- `zip`

Los requerimientos de plataforma también pueden verificarse mediante:

```bash
composer check-platform-reqs
```

Instalación
1. Clonar repositorio
git clone (https://github.com/Chellsaurius/flecha_app.git)
    ssh: git@github.com:Chellsaurius/flecha_app.git
cd flecha-app
2. Instalar dependencias PHP
composer install
3. Instalar dependencias frontend
npm install
4. Configurar ambiente

Copiar:

cp .env.example .env

En Windows también puede copiarse manualmente o utilizar:

Copy-Item .env.example .env

Configurar PostgreSQL:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flechaDB
DB_USERNAME=postgres
DB_PASSWORD=

Las credenciales deberán sustituirse por las correspondientes al ambiente.

5. Generar clave de Laravel
php artisan key:generate
6. Crear base de datos

Crear una base PostgreSQL llamada:

flechaDB

o utilizar otro nombre y modificar DB_DATABASE en .env.

7. Ejecutar migraciones
php artisan migrate

Opcionalmente pueden cargarse los datos iniciales:

php artisan db:seed
8. Ejecutar aplicación

Para desarrollo:

composer run dev

Este comando inicia los servicios de desarrollo definidos por el proyecto,
incluyendo Laravel y Vite.

La aplicación podrá accederse normalmente desde:

http://127.0.0.1:8000
Funcionalidades
Libros

El módulo de libros permite:

Registrar libros.
Consultar libros.
Buscar por título, autor o género.
Editar libros.
Eliminar libros físicamente.
Paginar resultados.

Cada libro recibe automáticamente un código con formato:

A12
B04
Z99

El código está compuesto por:

1 letra A-Z
+
2 dígitos 00-99

El usuario no puede proporcionar ni modificar este identificador.

Autores

El módulo de autores permite:

Crear.
Consultar.
Buscar.
Editar.
Eliminar.

Un autor no puede eliminarse mientras tenga libros asociados.

Los nombres son normalizados antes de almacenarse.

Ejemplo:

gabriel garcía márquez

se almacena como:

Gabriel García Márquez
Géneros

El módulo de géneros permite:

Crear.
Consultar.
Buscar.
Editar.
Eliminar.

Un género no puede eliminarse mientras tenga libros asociados.

API REST

La aplicación incluye una API protegida mediante Laravel Sanctum.

Endpoints principales:

GET    /api/books
GET    /api/books/{book}
POST   /api/books
PUT    /api/books/{book}
DELETE /api/books/{book}

GET    /api/authors
GET    /api/genres

Las rutas protegidas requieren:

Authorization: Bearer <token>
Ejemplo de creación de libro
POST /api/books
Content-Type: application/json
Authorization: Bearer <token>
{
    "title": "Pedro Páramo",
    "author_id": 1,
    "genre_id": 1,
    "publication_year": 1955,
    "description": "Novela mexicana."
}

Respuesta esperada:

201 Created

El servidor genera automáticamente book_code.

Validaciones

Entre las validaciones implementadas se encuentran:

Título obligatorio.
Autor existente.
Género existente.
Año de publicación entero.
Año no superior al actual.
Nombre único de autor.
Nombre único de género.
Longitud máxima de campos.

Las reglas principales están centralizadas en:

app/Support/
Base de datos

Entidades principales:

authors
genres
books

Relaciones:

Author 1 ───── N Book N ───── 1 Genre

La base de datos utiliza:

Primary Keys.
Foreign Keys.
Restricciones UNIQUE.
RESTRICT para evitar eliminaciones inconsistentes.

PostgreSQL es utilizado tanto en desarrollo como en pruebas.

Pruebas

La aplicación utiliza Pest y PHPUnit.

Ejecutar toda la suite:

php artisan test

Resultado de referencia actual:

53 tests passed

La cobertura funcional incluye:

Autenticación.
API REST.
Creación de libros.
Validaciones.
Generación de book_code.
Actualización de libros.
Eliminación física.
Búsqueda.
Componentes Livewire.
Autores.
Géneros.
Restricciones de eliminación.
Pruebas de regresión del starter kit.

Para más información:

docs/testing.md
Compilación frontend

Para generar los assets de producción:

npm run build

No es necesario mantener npm run dev ejecutándose en un ambiente de
producción.

Arquitectura

La solución utiliza una arquitectura monolítica modular.

             Usuario Web
                  |
                  v
              Livewire
                  |
                  |
                  v
            Laravel / Eloquent
                  |
                  v
              PostgreSQL


             Cliente API
                  |
                  v
               Sanctum
                  |
                  v
             Controllers
                  |
                  v
            Laravel / Eloquent
                  |
                  v
              PostgreSQL

La interfaz Livewire no consume la API REST de la propia aplicación.

Ambas interfaces reutilizan directamente modelos, servicios y reglas de negocio.

La API permanece disponible para consumidores externos.

Decisiones de diseño
Código de libro

El formato solicitado dispone de un máximo teórico de:

26 × 100 = 2600 códigos

Por lo tanto, es adecuado para el alcance del ejercicio pero debería ampliarse
si el sistema necesitara almacenar una cantidad superior de libros.

Eliminación

Los libros utilizan eliminación física debido al requerimiento del ejercicio.

La columna:

is_active

se conserva como preparación para una posible implementación futura de borrado
lógico.

Índice por año

No se mantiene un índice específico para:

publication_year

porque el volumen esperado y las consultas actuales no justifican el costo de
mantenimiento adicional.

Particionamiento

No se utiliza particionamiento debido al tamaño esperado de la tabla.

Añadirlo actualmente incrementaría la complejidad sin aportar una mejora
medible.

Seguridad

La aplicación utiliza:

Autenticación web.
Laravel Fortify.
Laravel Sanctum.
CSRF mediante Laravel/Livewire.
Validación de entradas.
Consultas parametrizadas mediante Eloquent.
Foreign Keys.
Restricciones UNIQUE.

En producción debe utilizarse:

APP_ENV=production
APP_DEBUG=false
Documentación

La documentación detallada se encuentra en:

docs/

Incluye:

Requerimientos
Arquitectura
Componentes
Pruebas
Despliegue
Costos
Diagramas de secuencia
Despliegue

Para el alcance inicial se propone:

Ubuntu Server
├── Web Server
├── PHP / Laravel
├── Livewire
└── PostgreSQL

La infraestructura puede separarse posteriormente si el volumen o los
requerimientos lo justifican.

La guía completa se encuentra en:

docs/deployment.md
Control de versiones

El proyecto utiliza Git para control de versiones.

Los archivos con información sensible como:

.env
.env.testing

no deben almacenarse en el repositorio.

Se incluyen archivos de ejemplo para facilitar la configuración de nuevos
ambientes.

Estado del proyecto

Funcionalidades principales:

[✓] Autenticación
[✓] CRUD de libros
[✓] CRUD de autores
[✓] CRUD de géneros
[✓] Búsqueda
[✓] Paginación
[✓] API REST
[✓] Autenticación API
[✓] Validaciones
[✓] Integridad referencial
[✓] Pruebas automatizadas
[✓] Compilación frontend
[✓] Documentación técnica
Consideraciones futuras

Posibles mejoras según crecimiento o nuevos requerimientos:

Borrado lógico.
Auditoría.
Roles y permisos.
Imágenes de portadas.
Búsqueda PostgreSQL mediante pg_trgm.
Índices adicionales según métricas reales.
Separación de base de datos.
Redis.
Procesamiento mediante colas.
Frontend o aplicación móvil consumiendo la API.
Monitoreo y respaldos automatizados.

Estas mejoras no se incluyen actualmente porque no forman parte de los
requerimientos necesarios para el alcance actual.
