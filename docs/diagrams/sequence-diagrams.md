## 1. Creación de libro desde la interfaz web

El siguiente diagrama representa el flujo utilizado cuando un usuario
autenticado registra un libro desde el componente Livewire.

mermaid
sequenceDiagram
    actor U as Usuario
    participant LW as Livewire Books
    participant VR as BookValidationRules
    participant BC as BookCodeGenerator
    participant B as Book Model
    participant DB as PostgreSQL

    U->>LW: Completa formulario y pulsa "Crear libro"

    LW->>VR: Solicita reglas de validación
    VR-->>LW: Retorna reglas

    LW->>LW: Valida datos

    alt Datos inválidos
        LW-->>U: Muestra errores de validación
    else Datos válidos
        LW->>BC: generate()

        loop Mientras el código ya exista
            BC->>B: exists(book_code)
            B->>DB: SELECT
            DB-->>B: Resultado
            B-->>BC: Existe / No existe
        end

        BC-->>LW: Código generado

        LW->>B: Book::create(...)
        B->>DB: INSERT book
        DB-->>B: Registro creado
        B-->>LW: Book

        LW->>LW: Limpia formulario
        LW->>LW: Reinicia paginación
        LW-->>U: Muestra mensaje de éxito
    end
2. Descripción del flujo web
El usuario captura los datos del libro.
Livewire recibe el evento wire:submit="save".
El componente obtiene las reglas desde BookValidationRules.
Laravel valida los datos.
Si existen errores, Livewire los devuelve a la interfaz.
Si los datos son válidos, se solicita un código a BookCodeGenerator.
El servicio genera una letra y dos dígitos aleatorios.
Se comprueba que el código no exista previamente.
Se crea el libro mediante Eloquent.
PostgreSQL persiste la información.
Livewire limpia el formulario y actualiza la interfaz.

La restricción UNIQUE de PostgreSQL sobre book_code constituye la garantía
final de integridad en caso de una posible condición de concurrencia.

3. Creación de libro mediante API REST

El siguiente flujo representa un consumidor externo que registra un libro
utilizando la API.

4. Descripción del flujo API

El flujo de creación mediante API utiliza las mismas reglas y componentes
reutilizables que la interfaz web, pero su punto de entrada es diferente.

Cliente externo
      |
      v
Sanctum
      |
      v
API Route
      |
      v
StoreBookRequest
      |
      v
BookController
      |
      +------------------+
      |                  |
      v                  v
BookCodeGenerator   Book Model
                         |
                         v
                    PostgreSQL

Esto permite que la API y Livewire reutilicen la lógica del sistema sin que la
interfaz web necesite realizar solicitudes HTTP hacia su propia aplicación.

5. Respuestas y excepciones relevantes

Durante estos flujos pueden producirse los siguientes resultados:

Situación	Resultado
Creación correcta mediante API	HTTP 201
Token inválido o ausente	HTTP 401
Datos inválidos	HTTP 422
Autor inexistente	HTTP 422
Género inexistente	HTTP 422
Código válido y disponible	Libro creado
Colisión de código	Se genera otro código
Violación final de UNIQUE	PostgreSQL rechaza la operación
6. Componentes involucrados

Los principales componentes representados en los diagramas son:

Usuario / Cliente API.
Livewire.
Laravel Sanctum.
API Routes.
Form Requests.
BookValidationRules.
BookController.
BookCodeGenerator.
Modelo Book.
PostgreSQL.

Los diagramas muestran que ambos canales de entrada reutilizan los componentes
centrales de lógica y persistencia.


### Cómo comprobarlo

GitHub renderiza los bloques Mermaid dentro del Markdown, así que cuando subas esto al repositorio deberías ver el diagrama gráficamente en lugar del código.

Y estos dos diagramas tienen una ventaja importante para tu presentación:


Web
 ↓
Livewire
 ↓
lógica compartida
 ↓
DB

API
 ↓
Controller
 ↓
lógica compartida
 ↓
DB

Visualmente dejan claro que no tenemos dos sistemas distintos, sino dos puntos de entrada sobre la misma aplicación.


