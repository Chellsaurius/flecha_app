# Arquitectura de la Solución

## 1. Descripción general

La solución fue desarrollada como una aplicación web monolítica modular utilizando
Laravel, Livewire y PostgreSQL.

La aplicación proporciona dos interfaces principales de acceso a la lógica del
sistema:

1. Una interfaz web administrativa desarrollada con Livewire.
2. Una API REST protegida mediante Laravel Sanctum.

Ambas interfaces comparten los mismos modelos, reglas de negocio, servicios y
base de datos.

La arquitectura busca mantener una separación clara de responsabilidades sin
introducir componentes distribuidos que no son necesarios para el alcance y
volumen esperado del sistema.

---

## 2. Tecnologías principales

| Componente | Tecnología |
|---|---|
| Backend | Laravel 13 |
| Lenguaje | PHP 8.3 |
| Frontend | Livewire 4 |
| Componentes UI | Flux |
| Estilos | Tailwind CSS |
| Base de datos | PostgreSQL |
| Autenticación web | Laravel Fortify |
| Autenticación API | Laravel Sanctum |
| Compilación frontend | Vite |
| Pruebas | Pest / PHPUnit |
| Control de versiones | Git |

---

## 3. Arquitectura lógica

La aplicación puede representarse mediante las siguientes capas:


┌─────────────────────────────────────────────┐
│                  Usuarios                   │
│                                             │
│   Navegador web          Cliente API        │
└───────────┬──────────────────┬──────────────┘
            │                  │
            ▼                  ▼
┌──────────────────┐   ┌──────────────────────┐
│     Livewire     │   │      API REST        │
│  Interfaz Web    │   │   Controllers HTTP   │
└────────┬─────────┘   └──────────┬───────────┘
         │                        │
         └────────────┬───────────┘
                      ▼
          ┌──────────────────────┐
          │ Lógica reutilizable │
          │                      │
          │ Models               │
          │ Validation Rules     │
          │ Services             │
          │ Query Scopes         │
          └──────────┬───────────┘
                     ▼
             ┌───────────────┐
             │  PostgreSQL   │
             └───────────────┘
4. Interfaz web

La interfaz administrativa utiliza Livewire.

Livewire permite mantener la lógica del componente en el servidor y actualizar
partes específicas de la interfaz sin realizar recargas completas de página.

Las principales pantallas administrativas son:

Dashboard.
Administración de libros.
Administración de autores.
Administración de géneros.

Cada módulo permite realizar las operaciones correspondientes mediante
componentes Livewire.

Responsabilidades de Livewire

Los componentes Livewire son responsables de:

Mantener el estado temporal de los formularios.
Recibir acciones del usuario.
Ejecutar validaciones.
Mostrar mensajes de error o confirmación.
Gestionar paginación.
Ejecutar operaciones CRUD desde la interfaz.
Actualizar dinámicamente la información mostrada.

La lógica reutilizable no se mantiene exclusivamente dentro de los componentes
Livewire.

5. API REST

La aplicación expone una API REST independiente de la interfaz web.

La API permite realizar operaciones sobre libros y consultar catálogos de autores
y géneros.

Las rutas están protegidas mediante Laravel Sanctum.

Flujo general
Cliente
   |
   | HTTP + Bearer Token
   v
Laravel Sanctum
   |
   v
API Route
   |
   v
Controller
   |
   v
Form Request / Validation Rules
   |
   v
Model / Service
   |
   v
PostgreSQL

Los códigos HTTP utilizados incluyen:

200 OK
201 Created
204 No Content
401 Unauthorized
404 Not Found
422 Unprocessable Entity
6. Separación entre interfaz web y API

La interfaz Livewire no realiza llamadas HTTP a la propia API de Laravel.

Esto fue una decisión intencional.

Al encontrarse Livewire y el backend dentro de la misma aplicación, realizar una
petición HTTP desde Laravel hacia su propia API añadiría una capa innecesaria:

Livewire
   ↓
HTTP
   ↓
API Laravel
   ↓
Laravel nuevamente

En su lugar se utiliza:

Livewire
   ↓
Modelos / servicios
   ↓
PostgreSQL

La API permanece disponible como una interfaz independiente para aplicaciones,
integraciones o clientes externos.

De esta forma se evita duplicar infraestructura sin perder la capacidad de
integración mediante REST.

7. Capa de modelos

La aplicación utiliza Eloquent ORM para representar las entidades principales:

Book

Representa los libros registrados.

Relaciones:

Book belongsTo Author
Book belongsTo Genre

También contiene un scope reutilizable para la búsqueda de libros.

Author

Representa autores.

Relación:

Author hasMany Books

Incluye normalización del atributo name antes de almacenarlo.

Genre

Representa los géneros literarios.

Relación:

Genre hasMany Books

Incluye normalización del atributo name antes de almacenarlo.

8. Servicios

La generación del identificador de libro fue separada en:

App\Services\BookCodeGenerator

Su responsabilidad exclusiva es generar códigos con el formato solicitado:

A00 - Z99

El servicio:

Genera una letra aleatoria entre A y Z.
Genera dos dígitos entre 00 y 99.
Comprueba si el código ya existe.
Repite el proceso si existe una colisión.

La base de datos mantiene adicionalmente una restricción UNIQUE sobre
book_code, proporcionando una segunda garantía de integridad ante posibles
condiciones de concurrencia.

9. Validación

Para evitar duplicar reglas entre la API REST y los componentes Livewire se
crearon clases reutilizables:

App\Support\BookValidationRules
App\Support\AuthorValidationRules
App\Support\GenreValidationRules

Por ejemplo:

BookValidationRules
        │
        ├── StoreBookRequest
        ├── UpdateBookRequest
        └── Livewire Books

Esto permite mantener una única definición de las principales reglas de
validación.

10. Consultas

La búsqueda de libros se encuentra encapsulada mediante un scope del modelo
Book.

Esto permite utilizar la misma lógica desde diferentes interfaces:

API BookController
       │
       ├──────────┐
       │          │
       ▼          ▼
 Book::search()  Livewire
                  │
                  ▼
             Book::search()

La consulta permite buscar mediante:

Título.
Autor.
Género.

PostgreSQL utiliza ILIKE para realizar búsquedas sin distinguir entre
mayúsculas y minúsculas.

11. Base de datos

La solución utiliza PostgreSQL.

Las tablas principales son:

authors
genres
books
users
personal_access_tokens

Relaciones principales:

authors
   1
   |
   | N
books
   N
   |
   | 1
genres

La tabla books contiene llaves foráneas hacia authors y genres.

Las relaciones utilizan RESTRICT al eliminar para evitar registros
inconsistentes.

Además de la validación de aplicación, la base de datos mantiene restricciones
de integridad como:

Primary Keys.
Foreign Keys.
UNIQUE.
NOT NULL.
Valores por defecto.
12. Seguridad

La arquitectura utiliza diferentes mecanismos de seguridad:

Autenticación web

El acceso administrativo requiere autenticación.

El starter kit utiliza Laravel Fortify e incluye soporte para:

Registro.
Verificación de correo.
Confirmación de contraseña.
Autenticación de dos factores.
API

Los endpoints de la API se encuentran protegidos por:

auth:sanctum

El acceso requiere un token válido.

Validación

Los datos recibidos son validados antes de ejecutar operaciones de persistencia.

SQL Injection

Las operaciones de datos utilizan Eloquent y consultas parametrizadas.

Integridad

Las Foreign Keys y restricciones UNIQUE proporcionan protección adicional a
nivel de base de datos.

13. Estrategia de eliminación

Actualmente los libros utilizan eliminación física:

DELETE FROM books

Esta decisión corresponde directamente al requerimiento funcional del ejercicio.

Sin embargo, la tabla cuenta con el atributo:

is_active

que permitiría evolucionar posteriormente hacia una estrategia de eliminación
lógica.

Autores y géneros no pueden eliminarse mientras existan libros relacionados.

Esta validación se realiza en la aplicación y se complementa mediante las
restricciones RESTRICT de PostgreSQL.

14. Estrategia de despliegue

Para el alcance actual se propone desplegar la solución en un único servidor
Linux.

Internet / Red
      |
      v
┌──────────────────────────┐
│      Ubuntu Server       │
│                          │
│  Web Server              │
│  PHP / Laravel           │
│  Livewire                │
│  PostgreSQL              │
└──────────────────────────┘

La separación física entre frontend, backend y base de datos no se considera
necesaria actualmente.

Esto reduce:

Costos.
Complejidad operativa.
Configuración de red.
Superficie de errores.
Tiempo de administración.

La arquitectura permite separar posteriormente la base de datos o incorporar
nuevos clientes de la API si el volumen o los requerimientos cambian.

15. Justificación de la arquitectura

Se seleccionó una arquitectura monolítica modular debido a que el sistema
presenta un dominio pequeño y bien definido.

Separar inicialmente la aplicación en múltiples servidores o servicios
independientes introduciría complejidad adicional sin proporcionar una ventaja
significativa para el volumen esperado.

Laravel proporciona dentro de una misma aplicación:

Routing.
Autenticación.
Validación.
ORM.
API REST.
Componentes interactivos mediante Livewire.
Pruebas automatizadas.

Por ello, mantener estos componentes dentro de una misma aplicación permite
desarrollar y mantener la solución con menor complejidad.

La existencia de una API REST mantiene abierta la posibilidad de incorporar en
el futuro:

Aplicaciones móviles.
Frontends independientes.
Integraciones con terceros.
Otros consumidores de información.

Por lo tanto, la arquitectura actual prioriza simplicidad y mantenibilidad sin
impedir una evolución futura.


