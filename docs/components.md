# Documentación de Componentes

## 1. Objetivo

Este documento describe los principales componentes de la solución, sus
responsabilidades, entradas y salidas.

---

## 2. BookController

**Ubicación**


app/Http/Controllers/BookController.php

Responsabilidad

Gestionar las operaciones HTTP de la API REST relacionadas con libros.

Operaciones
Método	Entrada	Salida
index()	Parámetro opcional search	Listado paginado de libros
store()	Datos validados de libro	Libro creado, HTTP 201
show()	Libro obtenido mediante Route Model Binding	Detalle del libro
update()	Libro + datos validados	Libro actualizado
destroy()	Libro	HTTP 204
Entradas principales
title
author_id
genre_id
publication_year
description

El campo book_code no es aceptado desde el cliente.

Salidas

Las respuestas de libros incluyen sus relaciones:

author
genre
3. AuthorController

Ubicación

app/Http/Controllers/AuthorController.php

Responsabilidad

Proporcionar el catálogo de autores mediante la API.

Entrada

No requiere parámetros.

Salida

Colección de autores ordenados por nombre.

4. GenreController

Ubicación

app/Http/Controllers/GenreController.php

Responsabilidad

Proporcionar el catálogo de géneros mediante la API.

Entrada

No requiere parámetros.

Salida

Colección de géneros ordenados por nombre.

5. StoreBookRequest

Ubicación

app/Http/Requests/StoreBookRequest.php

Responsabilidad

Validar los datos recibidos al registrar un nuevo libro mediante la API.

Entrada

Solicitud HTTP con los datos del libro.

Salida
Datos validados si la solicitud es correcta.
HTTP 422 Unprocessable Entity si existen errores de validación.

Las reglas se obtienen desde:

App\Support\BookValidationRules
6. UpdateBookRequest

Ubicación

app/Http/Requests/UpdateBookRequest.php

Responsabilidad

Validar los datos recibidos durante la actualización de un libro.

Entrada

Solicitud HTTP con los nuevos datos del libro.

Salida
Datos validados.
HTTP 422 cuando existen errores.

El identificador book_code no puede ser modificado mediante esta operación.

7. BookValidationRules

Ubicación

app/Support/BookValidationRules.php

Responsabilidad

Centralizar las reglas utilizadas para validar libros.

Entrada

Datos asociados a:

title
author_id
genre_id
publication_year
description
Salida

Arreglo de reglas de validación compatible con Laravel.

Consumidores
StoreBookRequest
UpdateBookRequest
Livewire Books
8. AuthorValidationRules

Ubicación

app/Support/AuthorValidationRules.php

Responsabilidad

Centralizar la validación de autores.

Entrada
name
authorId opcional

El identificador opcional se utiliza durante actualización para excluir al
registro actual de la validación de unicidad.

Salida

Reglas de validación de Laravel.

9. GenreValidationRules

Ubicación

app/Support/GenreValidationRules.php

Responsabilidad

Centralizar la validación de géneros.

Entrada
name
genreId opcional
Salida

Reglas de validación de Laravel.

10. BookCodeGenerator

Ubicación

app/Services/BookCodeGenerator.php

Responsabilidad

Generar el identificador único solicitado para cada libro.

Entrada

No requiere parámetros externos.

Proceso
Genera una letra aleatoria entre A y Z.
Genera un número aleatorio entre 00 y 99.
Forma un código de tres caracteres.
Consulta si el código ya existe.
Si existe, genera uno nuevo.
Salida

Cadena con formato:

A12
B04
Z99
Consideración

La restricción UNIQUE de PostgreSQL constituye la garantía final de unicidad
ante posibles condiciones de concurrencia.

11. Modelo Book

Ubicación

app/Models/Book.php

Responsabilidad

Representar la entidad libro y sus relaciones.

Entradas persistibles
book_code
title
author_id
genre_id
publication_year
description

is_active no se encuentra habilitado para asignación masiva.

Relaciones
Book belongsTo Author
Book belongsTo Genre
Consulta reutilizable

El scope:

search()

permite buscar por:

Título.
Autor.
Género.
Salida

Instancias Eloquent de Book y consultas asociadas.

12. Modelo Author

Ubicación

app/Models/Author.php

Responsabilidad

Representar autores.

Entrada
name
Procesamiento

El atributo name se normaliza antes de almacenarse:

Se eliminan espacios innecesarios.
Se aplica formato Title Case.
Relación
Author hasMany Books
Salida

Instancias Eloquent de Author.

13. Modelo Genre

Ubicación

app/Models/Genre.php

Responsabilidad

Representar géneros.

Entrada
name
Procesamiento

El nombre se normaliza antes del almacenamiento.

Relación
Genre hasMany Books
Salida

Instancias Eloquent de Genre.

14. Componente Livewire Books

Ubicación

resources/views/components/books/⚡index.blade.php

Responsabilidad

Gestionar la interfaz administrativa de libros.

Entradas del usuario
search
title
author_id
genre_id
publication_year
description
Acciones
save()
edit()
delete()
cancelEdit()
updatedSearch()
Salidas
Listado paginado de libros.
Resultados de búsqueda.
Mensajes de validación.
Mensajes de éxito.
Actualización dinámica de la interfaz.
15. Componente Livewire Authors

Ubicación

resources/views/components/authors/⚡index.blade.php

Responsabilidad

Gestionar autores desde la interfaz web.

Entradas
search
name
Acciones
save()
edit()
delete()
cancelEdit()
Regla de negocio

Antes de eliminar un autor se verifica:

author->books()->exists()

Si existen libros relacionados, la eliminación se rechaza.

Salidas
Lista paginada.
Resultados de búsqueda.
Mensajes de éxito/error.
Validaciones.
16. Componente Livewire Genres

Ubicación

resources/views/components/genres/⚡index.blade.php

Responsabilidad

Gestionar géneros desde la interfaz web.

Entradas
search
name
Acciones
save()
edit()
delete()
cancelEdit()
Regla de negocio

Un género no puede eliminarse mientras tenga libros asociados.

Salidas
Lista paginada.
Resultados de búsqueda.
Validaciones.
Mensajes de éxito/error.
17. Laravel Sanctum

Responsabilidad

Autenticar consumidores de la API REST.

Entrada

Token enviado como:

Authorization: Bearer <token>
Salida
Solicitud autorizada si el token es válido.
HTTP 401 Unauthorized si no existe autenticación válida.
18. PostgreSQL

Responsabilidad

Persistir la información y garantizar integridad de datos.

Entradas

Operaciones generadas desde Eloquent:

SELECT
INSERT
UPDATE
DELETE
Salidas

Registros persistidos o resultados de consultas.

Garantías

PostgreSQL mantiene:

Llaves primarias.
Llaves foráneas.
Restricciones UNIQUE.
Integridad referencial.
Restricciones NOT NULL.
19. Vite / Tailwind CSS

Responsabilidad

Gestionar y compilar los recursos frontend.

Entrada

Archivos CSS y recursos del proyecto.

Salida

Assets optimizados para ejecución en navegador.

El proceso de compilación se ejecuta mediante:

npm run build
20. Pest / PHPUnit

Responsabilidad

Ejecutar las pruebas automatizadas de la aplicación.

Entrada

Suite de pruebas ubicada en:

tests/
Salida

Resultado de ejecución y cantidad de assertions verificadas.

La suite se ejecuta mediante:

php artisan test

Actualmente se encuentran implementadas 53 pruebas satisfactorias.


