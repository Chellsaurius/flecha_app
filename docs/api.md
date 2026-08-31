# API REST

## 1. Descripción

Flecha App incluye una API REST para la administración de libros, autores y géneros.

La API está desarrollada con Laravel y utiliza Laravel Sanctum para la autenticación mediante tokens.

Los recursos disponibles son:

- Books: CRUD completo.
- Authors: CRUD completo y búsqueda por nombre.
- Genres: CRUD completo y búsqueda por nombre.

La API utiliza JSON tanto para las solicitudes como para las respuestas.

---

## 2. Autenticación

Los endpoints de la API están protegidos mediante Laravel Sanctum.

El cliente debe enviar un token válido utilizando el encabezado:

```http
Authorization: Bearer TOKEN
```

También se recomienda indicar:

```http
Accept: application/json
```

Ejemplo:

```http
GET /api/books
Authorization: Bearer TOKEN
Accept: application/json
```

Una solicitud sin autenticación válida devuelve:

```http
401 Unauthorized
```

Ejemplo de respuesta:

```json
{
  "message": "Unauthenticated."
}
```

---

## 3. URL base

En desarrollo o despliegue, la URL base depende del servidor utilizado.

Ejemplo:

```text
https://dominio-ejemplo.com/api
```

Los ejemplos de este documento utilizan rutas relativas para evitar depender de un dominio específico.

---

## 4. Códigos HTTP

| Código | Significado |
|---|---|
| `200 OK` | Consulta o actualización realizada correctamente |
| `201 Created` | Recurso creado correctamente |
| `204 No Content` | Recurso eliminado correctamente |
| `401 Unauthorized` | No se proporcionó autenticación válida |
| `404 Not Found` | El recurso solicitado no existe |
| `409 Conflict` | La operación entra en conflicto con relaciones existentes |
| `422 Unprocessable Content` | Los datos enviados no cumplen las reglas de validación |

---

# 5. Books

## 5.1. Listar libros

```http
GET /api/books
```

Retorna la lista paginada de libros incluyendo información relacionada del autor y género.

Ejemplo:

```http
GET /api/books
```

Respuesta:

```http
200 OK
```

---

## 5.2. Buscar libros

```http
GET /api/books?search=texto
```

El parámetro `search` permite buscar coincidencias por:

- título;
- nombre del autor;
- nombre del género.

Ejemplo:

```http
GET /api/books?search=Dickens
```

La búsqueda utiliza coincidencias parciales y no distingue mayúsculas y minúsculas mediante `ILIKE` de PostgreSQL.

---

## 5.3. Consultar un libro

```http
GET /api/books/{id}
```

Ejemplo:

```http
GET /api/books/1
```

Si el libro existe:

```http
200 OK
```

Si no existe:

```http
404 Not Found
```

---

## 5.4. Crear un libro

```http
POST /api/books
```

Body:

```json
{
  "title": "Grandes esperanzas",
  "author_id": 1,
  "genre_id": 2,
  "publication_year": 1861,
  "description": "Novela de Charles Dickens"
}
```

El cliente no proporciona `book_code`.

El identificador es generado automáticamente por el backend utilizando:

- una letra aleatoria entre `A-Z`;
- dos números entre `00-99`.

Ejemplo:

```text
A54
```

Respuesta correcta:

```http
201 Created
```

El campo `book_code` tiene además una restricción `UNIQUE` en PostgreSQL.

### Validaciones

- `title`: obligatorio, texto, máximo 255 caracteres.
- `author_id`: obligatorio y debe existir en `authors`.
- `genre_id`: obligatorio y debe existir en `genres`.
- `publication_year`: obligatorio, entero entre 1 y el año actual.
- `description`: opcional y de tipo texto.

---

## 5.5. Actualizar un libro

```http
PUT /api/books/{id}
```

Ejemplo:

```http
PUT /api/books/1
```

Body:

```json
{
  "title": "Grandes esperanzas - edición actualizada",
  "author_id": 1,
  "genre_id": 2,
  "publication_year": 1861,
  "description": "Descripción actualizada"
}
```

El `book_code` no es modificable mediante la actualización.

El identificador generado durante la creación permanece asociado al libro durante todo su ciclo de vida.

Respuesta:

```http
200 OK
```

---

## 5.6. Eliminar un libro

```http
DELETE /api/books/{id}
```

Ejemplo:

```http
DELETE /api/books/1
```

La eliminación es física, de acuerdo con los requerimientos del proyecto.

Respuesta:

```http
204 No Content
```

Después de la eliminación, consultar nuevamente el recurso produce:

```http
404 Not Found
```

---

# 6. Authors

## 6.1. Listar autores

```http
GET /api/authors
```

Respuesta:

```http
200 OK
```

---

## 6.2. Buscar autores

```http
GET /api/authors?search=texto
```

Ejemplo:

```http
GET /api/authors?search=Dickens
```

La búsqueda se realiza sobre el campo `name` utilizando una coincidencia parcial con `ILIKE`.

---

## 6.3. Consultar un autor

```http
GET /api/authors/{id}
```

Ejemplo:

```http
GET /api/authors/1
```

Respuesta cuando existe:

```http
200 OK
```

Si no existe:

```http
404 Not Found
```

---

## 6.4. Crear un autor

```http
POST /api/authors
```

Body:

```json
{
  "name": "Charles Dickens"
}
```

Respuesta:

```http
201 Created
```

### Validaciones

- `name`: obligatorio.
- Debe ser texto.
- Máximo 150 caracteres.
- Debe ser único.

El modelo normaliza el nombre antes de almacenarlo.

---

## 6.5. Actualizar un autor

```http
PUT /api/authors/{id}
```

También puede ser expuesto mediante `PATCH` cuando se utilizan las rutas `apiResource` de Laravel.

Body:

```json
{
  "name": "Charles Dickens"
}
```

Respuesta:

```http
200 OK
```

La validación de unicidad ignora al propio autor durante la actualización.

---

## 6.6. Eliminar un autor

```http
DELETE /api/authors/{id}
```

Si el autor no tiene libros asociados:

```http
204 No Content
```

Si tiene libros asociados, la eliminación es rechazada:

```http
409 Conflict
```

Ejemplo:

```json
{
  "message": "The author cannot be deleted because it has associated books."
}
```

Esta validación evita eliminar un autor utilizado por registros existentes y complementa la integridad referencial de PostgreSQL.

---

# 7. Genres

## 7.1. Listar géneros

```http
GET /api/genres
```

Respuesta:

```http
200 OK
```

---

## 7.2. Buscar géneros

```http
GET /api/genres?search=texto
```

Ejemplo:

```http
GET /api/genres?search=Fantasía
```

La búsqueda se realiza mediante coincidencia parcial sobre `name`.

---

## 7.3. Consultar un género

```http
GET /api/genres/{id}
```

Respuesta cuando existe:

```http
200 OK
```

Si no existe:

```http
404 Not Found
```

---

## 7.4. Crear un género

```http
POST /api/genres
```

Body:

```json
{
  "name": "Ciencia Ficción"
}
```

Respuesta:

```http
201 Created
```

### Validaciones

- `name`: obligatorio.
- Debe ser texto.
- Máximo 100 caracteres.
- Debe ser único.

---

## 7.5. Actualizar un género

```http
PUT /api/genres/{id}
```

También puede ser expuesto mediante `PATCH` cuando se utilizan las rutas `apiResource` de Laravel.

Body:

```json
{
  "name": "Fantasía"
}
```

Respuesta:

```http
200 OK
```

La validación de unicidad ignora al propio género durante una actualización.

---

## 7.6. Eliminar un género

```http
DELETE /api/genres/{id}
```

Si el género no tiene libros relacionados:

```http
204 No Content
```

Si existen libros asociados:

```http
409 Conflict
```

Ejemplo:

```json
{
  "message": "The genre cannot be deleted because it has associated books."
}
```

---

# 8. Validación

La validación se realiza en el servidor.

Las reglas utilizadas por la API reutilizan las mismas clases de validación empleadas por los componentes Livewire cuando corresponde.

Esto evita mantener reglas diferentes para la interfaz web y para la API.

La arquitectura utilizada es conceptualmente:

```text
Cliente API
    │
    ▼
Form Request
    │
    ▼
Validation Rules
    │
    ▼
Controller
    │
    ▼
Model
    │
    ▼
PostgreSQL
```

Cuando los datos son inválidos, Laravel responde:

```http
422 Unprocessable Content
```

incluyendo los errores correspondientes en formato JSON.

---

# 9. Integridad de datos

La aplicación utiliza varias capas para mantener la integridad:

```text
Validación Laravel
        │
        ▼
Lógica de aplicación
        │
        ▼
Modelos Eloquent
        │
        ▼
Restricciones PostgreSQL
```

Entre las restricciones utilizadas se encuentran:

- claves primarias;
- claves foráneas;
- restricciones `UNIQUE`;
- restricciones de eliminación para relaciones existentes.

La base de datos funciona como última capa de protección de la integridad.

---

# 10. Seguridad

La API utiliza Laravel Sanctum.

Los endpoints requieren autenticación mediante Bearer Token:

```http
Authorization: Bearer TOKEN
```

El token no debe almacenarse directamente en el código fuente ni agregarse al repositorio Git.

Para pruebas manuales puede almacenarse como variable secreta dentro del environment del cliente API.

Ejemplo utilizado con Bruno:

```text
baseUrl = https://servidor
token   = TOKEN_SANCTUM
```

Las solicitudes pueden utilizar posteriormente:

```text
{{baseUrl}}/api/books
```

y:

```text
{{token}}
```

---

# 11. Pruebas manuales

Se utiliza Bruno como cliente REST para realizar pruebas manuales y exploratorias de la API.

Entre las operaciones verificables se encuentran:

- autenticación mediante Bearer Token;
- consultas GET;
- creación mediante POST;
- actualización mediante PUT;
- eliminación mediante DELETE;
- parámetros de búsqueda;
- códigos HTTP;
- validaciones;
- respuestas JSON.

Bruno se utiliza como herramienta de validación manual y no sustituye las pruebas automatizadas.

---

# 12. Pruebas automatizadas

Las pruebas automatizadas se implementan con Pest.

Se verifican, entre otros casos:

- acceso no autenticado;
- listado de recursos;
- creación;
- búsqueda;
- consulta individual;
- actualización;
- validaciones;
- restricciones de eliminación;
- eliminación física;
- persistencia de los cambios en PostgreSQL.

Las pruebas utilizan una base PostgreSQL dedicada para mantener el comportamiento del entorno de prueba cercano al entorno real de ejecución.

---

# 13. Consideraciones y limitaciones

La API está diseñada para el alcance actual del proyecto.

Actualmente:

- Books utiliza paginación.
- Authors y Genres son catálogos pequeños y retornan sus colecciones directamente.
- Las búsquedas utilizan `ILIKE` con coincidencia parcial.
- La autenticación se realiza mediante Laravel Sanctum.

Si el volumen de datos aumentara significativamente se podrían evaluar:

- paginación adicional para catálogos;
- índices especializados;
- PostgreSQL `pg_trgm`;
- versionamiento formal de la API;
- rate limiting específico;
- scopes o abilities de Sanctum según los diferentes tipos de cliente.

Estas mejoras deben implementarse cuando los requerimientos o métricas reales las justifiquen.