# Estrategia de Despliegue

## 1. Objetivo

Este documento describe la estrategia propuesta para desplegar la aplicación de
gestión de biblioteca en un ambiente Linux.

Para el alcance actual se propone utilizar un único servidor Ubuntu que contenga:

- Aplicación Laravel.
- Interfaz Livewire.
- API REST.
- PostgreSQL.
- Servidor web.
- PHP.

La decisión busca mantener una arquitectura sencilla, económica y adecuada al
volumen esperado del sistema.

---

## 2. Arquitectura física propuesta


```text
                 Usuario Web
                     |
                     | HTTPS
                     v
            ┌─────────────────────┐
            │    Ubuntu Server    │
            │                     │
            │  Apache / Nginx     │
            │        |            │
            │        v            │
            │ Laravel + Livewire  │
            │        |            │
            │        ├── API REST │
            │        |            │
            │        v            │
            │    PostgreSQL       │
            └─────────────────────┘
```


Aunque los componentes se encuentren físicamente en el mismo servidor, se
mantiene una separación lógica entre:


```text
Frontend
   |
Livewire
   |
Laravel
   |
PostgreSQL
```


y:


```text
Cliente API
   |
Laravel Sanctum
   |
Controllers
   |
Laravel
   |
PostgreSQL
```


---

## 3. Frontend

La interfaz frontend utiliza:


- Livewire
- Flux
- Tailwind CSS
- Vite


Livewire se ejecuta dentro de la misma aplicación Laravel.

Los recursos CSS y JavaScript se compilan utilizando:

```bash
npm run build
```


El resultado generado por Vite es servido como contenido estático por el
servidor web.

No se requiere un servidor Node.js ejecutándose permanentemente en producción.

Node.js solamente es necesario durante el proceso de construcción de los assets.

---

## 4. Backend

El backend utiliza:


- PHP 8.3+
- Laravel 13
- Eloquent ORM
- Laravel Sanctum
- Laravel Fortify


El servidor web debe tener como Document Root únicamente:


/project/public


Nunca debe exponerse directamente la raíz completa del proyecto Laravel.

---

## 5. Base de datos

La solución utiliza PostgreSQL.

Para el despliegue inicial PostgreSQL puede ejecutarse en el mismo servidor que
Laravel.

Ejemplo de conexión:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flechaDB
DB_USERNAME=usuario_aplicacion
DB_PASSWORD=********
```


Se recomienda utilizar un usuario específico para la aplicación en lugar de
conectarse utilizando el superusuario de PostgreSQL.

---

## 6. Requerimientos del servidor

Sistema operativo propuesto:


Ubuntu Server


Software requerido:


Web Server:
Apache o Nginx

Runtime:
PHP 8.3 o superior

Base de datos:
- PostgreSQL

Dependencias:
Composer

Build frontend:
Node.js
npm

Control de versiones:
Git


Extensiones PHP principales requeridas por el proyecto:


- pdo_pgsql
- pgsql
- curl
- fileinfo
- mbstring
- openssl
- tokenizer
- xml
- ctype
- json
- zip


La lista definitiva puede verificarse mediante:

```bash
composer check-platform-reqs
```


---

## 7. Obtención del proyecto

El código fuente se obtiene desde el repositorio Git.

Ejemplo:

```bash
git clone <URL_DEL_REPOSITORIO>
cd flecha-app
```


En futuras actualizaciones se puede utilizar:

```bash
git pull
```


sobre una rama previamente validada.

---

## 8. Instalación de dependencias PHP

En producción se recomienda instalar dependencias sin paquetes de desarrollo:

```bash
composer install --no-dev --optimize-autoloader
```


Esto instala las versiones registradas en:


composer.lock


y evita instalar herramientas utilizadas exclusivamente durante desarrollo.

---

## 9. Configuración del ambiente

Se crea el archivo:


- .env


a partir de:


.env.example


Ejemplo:

```bash
cp .env.example .env
```


Variables importantes:

```env
APP_NAME=flecha_app
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dominio-o-direccion
```

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flechaDB
DB_USERNAME=usuario_aplicacion
DB_PASSWORD=contraseña_segura


En producción:

```env
APP_DEBUG=false
```


debe permanecer deshabilitado para evitar mostrar información interna de la
aplicación.

---

## 10. Generación de clave de aplicación

Después de crear el `.env`:

```bash
php artisan key:generate
```


Laravel almacena la clave resultante en:

```env
APP_KEY=
```


La clave no debe compartirse públicamente ni versionarse en Git.

---

## 11. Preparación de PostgreSQL

Antes de ejecutar las migraciones debe existir:


flechaDB


junto con el usuario autorizado para acceder a ella.

El usuario utilizado por Laravel debe disponer de los permisos necesarios para:


- SELECT
- INSERT
- UPDATE
- DELETE
- CREATE
- ALTER
- INDEX
- REFERENCES


durante la instalación inicial y ejecución de migraciones.

---

## 12. Migraciones

La estructura de base de datos se crea mediante:

```bash
php artisan migrate --force
```


La opción:


--force


es necesaria en producción para confirmar explícitamente la ejecución de
migraciones.

Gracias a las correcciones realizadas durante las pruebas, el historial de
migraciones permite reconstruir la estructura completa en una base limpia.

---

## 13. Seeders

Si se desean cargar datos iniciales:

```bash
php artisan db:seed --force
```


El uso de seeders deberá evaluarse según el ambiente.

No se recomienda insertar datos de demostración innecesarios en una instalación
productiva real.

---

## 14. Compilación de frontend

Las dependencias frontend se instalan utilizando:

```bash
npm ci
```


Posteriormente:

```bash
npm run build
```


`npm ci` utiliza las versiones registradas en:


package-lock.json


lo que ayuda a obtener instalaciones reproducibles.

Una vez compilados los assets, no es necesario mantener:

```bash
npm run dev
```


ejecutándose en producción.

---

## 15. Permisos de Laravel

El usuario utilizado por el servidor web deberá tener permisos de escritura
sobre:


storage/
bootstrap/cache/


Por ejemplo, la configuración exacta dependerá del usuario configurado para
Apache o PHP-FPM.

No se recomienda proporcionar permisos globales `777`.

---

## 16. Caché de producción

Después de completar la configuración pueden optimizarse diferentes componentes
de Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```


También puede utilizarse:

```bash
php artisan optimize
```


Estas operaciones reducen trabajo realizado durante las solicitudes normales.

Cuando se modifique configuración durante mantenimiento se puede limpiar mediante:

```bash
php artisan optimize:clear
```


---

## 17. Servidor web

El servidor web debe apuntar al directorio:


flecha-app/public


El flujo de una solicitud será:


```text
Cliente
   |
   v
Apache / Nginx
   |
   v
public/index.php
   |
   v
Laravel
```


El servidor web no debe exponer directamente:


- .env
vendor/
storage/
database/
config/


---

## 18. Virtualmin

Como alternativa de administración del servidor puede utilizarse Virtualmin.

Virtualmin puede facilitar tareas como:


- Administración del dominio
- Configuración del servidor web
- Certificados TLS
- Administración del sistema
- Logs
- Bases de datos


Sin embargo, Virtualmin funciona únicamente como herramienta de administración.

La aplicación continúa siendo una aplicación Laravel estándar y no depende de
Virtualmin para su funcionamiento.

---

## 19. HTTPS

En un despliegue accesible públicamente se recomienda utilizar HTTPS.

El tráfico esperado sería:


```text
Usuario
   |
   | HTTPS
   v
Servidor Web
   |
   v
Laravel
```


Esto es especialmente importante porque la aplicación maneja:

- Credenciales de inicio de sesión.
- Sesiones.
- Tokens de autenticación.
- Operaciones administrativas.

---

## 20. API en producción

La API estará disponible dentro de la misma aplicación.

Ejemplo:


https://dominio/api/books


Las rutas protegidas requieren:

```http
Authorization: Bearer <token>
```


Laravel Sanctum valida el token antes de permitir el acceso.

Los tokens no deberán almacenarse en:


Repositorio Git
Código fuente
Documentación pública
Capturas de pantalla


---

## 21. Almacenamiento público

Actualmente el proyecto no depende de carga pública de archivos.

Si en una evolución futura se incorporaran imágenes, documentos o archivos
almacenados mediante el disco `public`, sería necesario ejecutar:

```bash
php artisan storage:link
```


No se considera obligatorio para las funciones actuales del sistema.

---

## 22. Colas

La funcionalidad actual del sistema no depende de procesamiento asíncrono.

Por lo tanto, no es necesario mantener permanentemente:

```bash
php artisan queue:work
```


en el despliegue actual.

Si posteriormente se incorporaran procesos como:

- Envío masivo de correos.
- Generación de reportes.
- Procesamiento de archivos.
- Notificaciones.

podría añadirse un worker administrado mediante Supervisor o systemd.

---

## 23. Scheduler

Actualmente no existen procesos periódicos propios del dominio que requieran el
scheduler de Laravel.

Por este motivo no es necesario configurar tareas programadas adicionales para
las funcionalidades actuales.

Esta infraestructura puede incorporarse posteriormente si aparece un
requerimiento que la justifique.

---

## 24. Proceso sugerido de despliegue

Un despliegue inicial puede seguir el siguiente flujo:


```text
Repositorio Git
      |
      v
Clonar proyecto
      |
      v
composer install
      |
      v
Configurar .env
      |
      v
Crear/configurar PostgreSQL
      |
      v
php artisan key:generate
      |
      v
php artisan migrate --force
      |
      v
npm ci
      |
      v
npm run build
      |
      v
Configurar permisos
      |
      v
Configurar servidor web
      |
      v
Optimizar Laravel
      |
      v
Validar aplicación
```


---

## 25. Validación posterior al despliegue

Después del despliegue se recomienda comprobar:


- Página de inicio
- Login
- Dashboard
- Listado de libros
- Creación de libro
- Edición de libro
- Eliminación de libro
- Administración de autores
- Administración de géneros
- Búsqueda
- Paginación
- API protegida
- HTTPS
- Logs


También deberá verificarse que:

```env
APP_ENV=production
APP_DEBUG=false
```


---

## 26. Logs

Laravel registra errores de aplicación en:


storage/logs/


El servidor web dispone adicionalmente de sus propios registros de acceso y
errores.

Estos logs permiten investigar incidencias sin mostrar información técnica al
usuario final.

---

## 27. Respaldos

En un ambiente productivo real se recomienda respaldar principalmente:


- PostgreSQL
- .env
- archivos persistentes de usuario


El código fuente no requiere respaldo manual si se encuentra correctamente
versionado en Git.

Para PostgreSQL pueden utilizarse herramientas como:


pg_dump
pg_restore


La frecuencia de respaldos dependerá de la criticidad y frecuencia de cambio de
la información.

---

## 28. Escalamiento futuro

La arquitectura inicial utiliza un único servidor:


```text
┌─────────────────────┐
│ Laravel             │
│ Livewire            │
│ PostgreSQL          │
└─────────────────────┘
```


Si el volumen aumentara, la arquitectura podría evolucionar hacia:


```text
                  ┌───────────────┐
                  │  Web / App    │
                  │    Server     │
                  └───────┬───────┘
                          |
                          v
                  ┌───────────────┐
                  │  PostgreSQL   │
                  │    Server     │
                  └───────────────┘
```


Posteriormente también podrían incorporarse:


- Load Balancer
- Múltiples servidores Laravel
- Redis
- Workers independientes
- CDN
- Base de datos administrada


Estos componentes no se implementan actualmente porque el alcance y volumen del
ejercicio no los justifican.

---

## 29. Estrategia para demostración

Para una demostración temporal puede ejecutarse la aplicación en un servidor
privado y exponerla únicamente durante el período necesario.

En desarrollo local:

```bash
composer run dev
```


puede iniciar los servicios necesarios para trabajar con la aplicación.

Este mecanismo es apropiado para desarrollo, pero no debe utilizarse como
servidor de producción permanente.

Para una demostración pública temporal puede utilizarse un túnel HTTPS hacia el
servidor local o privado, evitando mantener el sistema expuesto de forma
permanente.

---

## 30. Separación futura de componentes

El diseño actual no impide distribuir los componentes posteriormente.

Por ejemplo:


```text
                  Internet
                     |
                     v
             ┌──────────────┐
             │ Laravel /    │
             │ Livewire     │
             └──────┬───────┘
                    |
                    | TCP privado
                    v
             ┌──────────────┐
             │ PostgreSQL   │
             └──────────────┘
```


Incluso un frontend independiente podría consumir:


/api/*


sin modificar la base de datos ni eliminar la API existente.

---

## 31. Justificación

Utilizar un único servidor para el despliegue inicial ofrece:

- Menor costo.
- Menor complejidad.
- Menor cantidad de puntos de falla.
- Configuración más sencilla.
- Mantenimiento más sencillo.
- Tiempo de despliegue reducido.

Separar componentes físicamente desde el inicio no aportaría una mejora
significativa para el volumen esperado.

La estrategia seleccionada permite comenzar con una infraestructura pequeña y
evolucionar cuando exista un requerimiento técnico o de negocio que lo
justifique.
