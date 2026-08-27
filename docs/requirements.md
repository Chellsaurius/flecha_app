# Requerimientos del Sistema

## 1. Objetivo

Desarrollar una aplicación web para la gestión de una biblioteca que permita
administrar libros, autores y géneros, proporcionando una interfaz web y una API
REST protegida para el acceso a la información.

---

## 2. Requerimientos funcionales

### RF-01 - Autenticación

El sistema deberá permitir el acceso de usuarios autenticados a las funciones
administrativas.

Las rutas administrativas y los endpoints de la API deberán estar protegidos
contra accesos no autorizados.

---

### RF-02 - Registro de libros

El sistema deberá permitir registrar libros indicando:

- Título.
- Autor.
- Género.
- Año de publicación.
- Descripción opcional.

El identificador del libro no deberá ser proporcionado por el usuario.

---

### RF-03 - Generación de identificador de libro

Al registrar un libro, el sistema deberá generar automáticamente un identificador
único compuesto por:

- Una letra aleatoria entre A y Z.
- Dos dígitos aleatorios entre 00 y 99.

Ejemplos:

- A12
- B04
- Z99

El sistema deberá verificar que el identificador no haya sido utilizado
previamente.

La base de datos deberá garantizar adicionalmente su unicidad mediante una
restricción UNIQUE.

---

### RF-04 - Consulta de libros

El sistema deberá permitir consultar el listado de libros registrados mostrando:

- Código.
- Título.
- Autor.
- Género.
- Año de publicación.

Los resultados deberán mostrarse paginados.

---

### RF-05 - Búsqueda de libros

El usuario deberá poder buscar libros utilizando texto parcial.

La búsqueda deberá considerar:

- Título del libro.
- Nombre del autor.
- Nombre del género.

La búsqueda no deberá distinguir entre mayúsculas y minúsculas.

---

### RF-06 - Consulta individual de libro

La API deberá permitir consultar los detalles de un libro específico mediante
su identificador interno.

---

### RF-07 - Actualización de libros

El sistema deberá permitir actualizar:

- Título.
- Autor.
- Género.
- Año de publicación.
- Descripción.

El código único generado al momento de crear el libro no deberá modificarse
durante la actualización.

---

### RF-08 - Eliminación de libros

El sistema deberá permitir eliminar físicamente un libro de la base de datos.

Se conserva el campo `is_active` como preparación para una posible evolución
hacia borrado lógico, pero el comportamiento actual utiliza eliminación física
debido a los requerimientos del ejercicio.

---

### RF-09 - Administración de autores

El sistema deberá permitir:

- Consultar autores.
- Buscar autores.
- Crear autores.
- Actualizar autores.
- Eliminar autores.

No deberá permitirse eliminar un autor que tenga libros asociados.

---

### RF-10 - Administración de géneros

El sistema deberá permitir:

- Consultar géneros.
- Buscar géneros.
- Crear géneros.
- Actualizar géneros.
- Eliminar géneros.

No deberá permitirse eliminar un género que tenga libros asociados.

---

### RF-11 - Normalización de nombres

Los nombres de autores y géneros deberán normalizarse al almacenarse para
mantener consistencia en la información.

Se utiliza formato Title Case y se eliminan espacios innecesarios.

---

### RF-12 - API REST

El sistema deberá proporcionar una API REST para libros.

Endpoints principales:

- `GET /api/books`
- `GET /api/books/{book}`
- `POST /api/books`
- `PUT /api/books/{book}`
- `DELETE /api/books/{book}`
- `GET /api/authors`
- `GET /api/genres`

El acceso deberá requerir autenticación mediante Laravel Sanctum.

---

### RF-13 - Validación

El sistema deberá validar los datos antes de almacenarlos.

Entre las validaciones se incluyen:

- Título obligatorio.
- Autor existente.
- Género existente.
- Año de publicación entero.
- Año de publicación no superior al año actual.
- Longitud máxima de campos.
- Unicidad de autores y géneros.

---

## 3. Requerimientos no funcionales

### RNF-01 - Seguridad

El sistema deberá:

- Requerir autenticación para las funciones administrativas.
- Proteger la API mediante Laravel Sanctum.
- Utilizar consultas parametrizadas mediante Eloquent.
- Validar los datos recibidos.
- Evitar que el cliente pueda proporcionar el código único de un libro.
- Proteger formularios Livewire mediante el mecanismo CSRF de Laravel/Livewire.

---

### RNF-02 - Integridad de datos

La base de datos deberá utilizar:

- Llaves primarias.
- Llaves foráneas.
- Restricciones UNIQUE.
- Restricciones de integridad referencial.

Las relaciones de libros con autores y géneros utilizarán `RESTRICT` para evitar
la eliminación de registros relacionados.

---

### RNF-03 - Mantenibilidad

El código deberá organizarse de forma que las reglas reutilizables no dependan
exclusivamente de la interfaz.

Se emplean:

- Modelos Eloquent.
- Form Requests.
- Clases reutilizables de validación.
- Servicios.
- Scopes de consulta.
- Componentes Livewire.

---

### RNF-04 - Usabilidad

La interfaz deberá:

- Permitir realizar operaciones sin recargar completamente la página.
- Mostrar mensajes de validación.
- Mostrar mensajes de éxito y error.
- Solicitar confirmación antes de eliminar registros.
- Ser compatible con modo claro y oscuro.
- Mantener navegación simple mediante un menú lateral.

---

### RNF-05 - Rendimiento

Los listados deberán utilizar paginación para evitar recuperar todos los
registros simultáneamente.

Las relaciones de libros con autores y géneros deberán utilizar eager loading
para evitar el problema N+1.

---

### RNF-06 - Compatibilidad

El sistema deberá poder ejecutarse utilizando:

- PHP 8.3 o superior.
- Laravel 13.
- PostgreSQL.
- Node.js / npm.
- Navegadores web modernos.

---

### RNF-07 - Pruebas

El proyecto deberá disponer de pruebas automatizadas para validar:

- Autenticación.
- API REST.
- CRUD de libros.
- CRUD de autores.
- CRUD de géneros.
- Validaciones.
- Restricciones de eliminación.
- Generación del código del libro.

Actualmente la suite completa cuenta con:

- 53 pruebas satisfactorias.
- Pruebas de regresión del starter kit.
- Pruebas de API.
- Pruebas de componentes Livewire.

---

## 4. Restricciones y consideraciones

### Código de libro

El formato solicitado permite un máximo teórico de:

26 letras × 100 combinaciones numéricas = 2600 códigos únicos.

Por lo tanto, el mecanismo es adecuado para el alcance del ejercicio, pero no
sería apropiado para una biblioteca con más de 2600 registros.

En un sistema de mayor escala sería necesario ampliar el formato del
identificador.

### Índices

No se mantiene un índice específico sobre `publication_year` debido al tamaño
esperado del sistema y a que actualmente no existen consultas frecuentes que
justifiquen su mantenimiento.

Las restricciones UNIQUE de PostgreSQL generan sus propios índices.

Para búsquedas de texto de mayor escala podría evaluarse posteriormente el uso
de `pg_trgm` junto con índices GIN o GiST.

### Particionamiento

No se implementó particionamiento de la tabla de libros debido al volumen
esperado de información.

Añadir particionamiento en el alcance actual incrementaría la complejidad sin
proporcionar una mejora medible de rendimiento.