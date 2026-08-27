# Estimación de Costos

## 1. Objetivo

Este documento presenta una estimación de los costos asociados a los componentes
tecnológicos y a la infraestructura propuesta para la solución.

La mayoría de las tecnologías utilizadas son de código abierto y no requieren
pago de licencias.

Los costos de infraestructura dependerán del ambiente seleccionado para el
despliegue.

---

## 2. Costos de software

| Componente | Licenciamiento | Costo |
|---|---|---:|
| PHP | Open Source | $0 |
| Laravel | Open Source | $0 |
| Livewire | Open Source | $0 |
| Laravel Sanctum | Incluido en ecosistema Laravel | $0 |
| Laravel Fortify | Open Source | $0 |
| PostgreSQL | Open Source | $0 |
| Tailwind CSS | Open Source | $0 |
| Vite | Open Source | $0 |
| Pest / PHPUnit | Open Source | $0 |
| Composer | Open Source | $0 |
| Git | Open Source | $0 |
| Ubuntu Server | Open Source | $0 |

Por lo tanto, la solución no requiere actualmente la adquisición de licencias
comerciales para su funcionamiento.

---

## 3. Herramientas de desarrollo

Durante el desarrollo pueden utilizarse herramientas gratuitas o ediciones sin
costo adicional.

Ejemplos:


Visual Studio Code
Git
GitHub
DBeaver u otro cliente de base de datos
Navegador web
Terminal / PowerShell


Algunas herramientas de administración de base de datos pueden disponer de
licencias comerciales dependiendo de la edición utilizada, pero no son una
dependencia de ejecución del sistema.

La aplicación puede administrarse utilizando herramientas gratuitas como:


psql
pgAdmin


---

## 4. Ambiente local de desarrollo

El desarrollo puede ejecutarse completamente en una computadora local.


Equipo del desarrollador
        |
        ├── Laravel
        ├── PostgreSQL
        ├── Node.js
        └── Navegador


### Costo adicional de infraestructura


$0


No se considera aquí el costo del equipo físico, conexión a Internet o consumo
eléctrico, ya que corresponden a infraestructura existente del desarrollador.

---

## 5. Ambiente local de demostración

Para demostrar la solución puede utilizarse una máquina virtual local con Ubuntu
Server.

Ejemplo:


Computadora física
      |
      v
Máquina Virtual Ubuntu
      |
      ├── Laravel
      ├── PostgreSQL
      └── Servidor Web


Si se utilizan recursos existentes, el costo adicional es:


$0


Esta alternativa es adecuada para:

- Desarrollo.
- Evaluación técnica.
- Pruebas.
- Demostraciones controladas.

---

## 6. Exposición temporal para demostración

Si se requiere mostrar temporalmente la aplicación fuera de la red local puede
utilizarse un servicio de túnel HTTPS que disponga de un plan gratuito.

Para una demostración de corta duración no es obligatorio adquirir:

- Dominio.
- Certificado TLS comercial.
- Servidor público permanente.

El costo adicional puede mantenerse en:


$0


si se utiliza una alternativa gratuita disponible.

Este mecanismo se considera únicamente para demostración y no como estrategia
de producción permanente.

---

## 7. Servidor de producción

Para una implementación real accesible permanentemente desde Internet sería
necesario disponer de infraestructura de hosting.

La arquitectura propuesta inicialmente requiere únicamente:


1 servidor


que contenga:


Servidor Web
PHP / Laravel
Livewire
PostgreSQL


El costo dependerá del proveedor seleccionado, región, recursos asignados,
almacenamiento, respaldo y nivel de soporte.

Por este motivo no se establece un precio fijo dentro de la arquitectura.

---

## 8. Recursos iniciales estimados

Para el volumen previsto del ejercicio, una instalación inicial podría utilizar
recursos modestos.

Ejemplo conceptual:

| Recurso | Configuración inicial |
|---|---|
| CPU | 1–2 vCPU |
| RAM | 2–4 GB |
| Almacenamiento | 20–40 GB SSD |
| Sistema operativo | Ubuntu Server |
| Base de datos | PostgreSQL en el mismo servidor |
| Aplicación | Laravel + Livewire |

Estas cantidades representan una configuración inicial y deberán ajustarse
según mediciones reales de utilización.

---

## 9. Dominio

Un dominio personalizado es opcional para el ejercicio.

En una implementación productiva podría añadirse:


biblioteca.ejemplo.com


El precio dependerá del registrador y de la extensión seleccionada.

Por este motivo se considera:


Costo variable / opcional


---

## 10. HTTPS

No es necesario adquirir un certificado TLS comercial para una implementación
estándar.

Pueden utilizarse certificados gratuitos mediante proveedores compatibles con
ACME.

Por lo tanto, el costo de certificado puede ser:


$0


La renovación puede automatizarse en el servidor.

---

## 11. Base de datos

PostgreSQL no implica costo de licencia.

En la arquitectura inicial:


Laravel + PostgreSQL


comparten el mismo servidor.

Por lo tanto, no existe un costo adicional de servidor de base de datos.

Si posteriormente PostgreSQL fuera trasladado a:

- Un servidor independiente.
- Un servicio administrado.
- Un clúster de alta disponibilidad.

entonces aparecerían costos adicionales de infraestructura.

---

## 12. Respaldos

En un ambiente de demostración los respaldos pueden mantenerse localmente sin
costo adicional significativo.

Para producción podría ser recomendable utilizar almacenamiento externo.

Ejemplos:


Object Storage
Otro servidor
Servicio administrado de respaldos


El costo dependerá del volumen almacenado y de la frecuencia de retención.

---

## 13. Correo electrónico

El proyecto incluye funcionalidades de autenticación que pueden utilizar correo
electrónico, por ejemplo:

- Verificación de cuenta.
- Recuperación de contraseña.

Durante desarrollo pueden utilizarse mecanismos locales o servicios de prueba.

En producción podría requerirse un proveedor SMTP o servicio transaccional.

Su costo dependerá del volumen de correos enviados.

Para las operaciones principales de biblioteca el correo no representa un
requerimiento funcional obligatorio.

---

## 14. Escenario 1 - Desarrollo


Aplicación local
PostgreSQL local
Git
Herramientas Open Source


### Costo adicional estimado


$0


---

## 15. Escenario 2 - Evaluación / demostración


Ubuntu Server en máquina virtual local
PostgreSQL local
Aplicación Laravel
Exposición temporal opcional


### Costo adicional estimado


$0


si se utilizan recursos ya disponibles y herramientas gratuitas.

---

## 16. Escenario 3 - Producción básica


1 servidor público
1 dominio opcional
HTTPS gratuito
PostgreSQL en el mismo servidor


### Costos


Servidor: variable según proveedor
Dominio: variable
TLS: $0
Licencias de software: $0
PostgreSQL: $0


Este escenario representa la alternativa inicial recomendada para una aplicación
de bajo volumen.

---

## 17. Escenario 4 - Crecimiento futuro

Si el sistema incrementara considerablemente su carga, podrían incorporarse:


Servidor dedicado para PostgreSQL
Balanceador de carga
Múltiples servidores Laravel
Redis
Workers
Almacenamiento externo
CDN
Sistema de respaldos
Monitoreo especializado


Cada nuevo componente incrementaría el costo operativo.

Por este motivo no se incluyen en la arquitectura inicial.

---

## 18. Costo operativo

Además del costo directo de infraestructura debe considerarse el costo de
operación.

Entre las actividades necesarias se encuentran:

- Actualización del sistema operativo.
- Actualización de dependencias.
- Supervisión de logs.
- Respaldos.
- Monitoreo.
- Renovación de dominio.
- Revisión de seguridad.
- Recuperación ante incidentes.

Para un ejercicio técnico estos costos no se cuantifican económicamente, pero
deben considerarse en una implementación productiva real.

---

## 19. Criterio utilizado para optimizar costos

La solución prioriza utilizar únicamente componentes que respondan a una
necesidad actual.

Por ejemplo, no se incluyeron inicialmente:

- Múltiples servidores.
- Kubernetes.
- Redis.
- Servicios de colas independientes.
- Base de datos administrada.
- Balanceadores.
- CDN.

La incorporación de estos componentes sin una necesidad medible aumentaría:


Costo
Complejidad
Mantenimiento
Puntos de falla


sin proporcionar un beneficio proporcional para el alcance actual.

---

## 20. Resumen

### Desarrollo


Costo adicional: $0


### Demostración local


Costo adicional: $0


### Software y licencias


Costo: $0


### Producción


Infraestructura: costo variable según proveedor
Dominio: opcional / variable
TLS: puede utilizarse sin costo


La arquitectura permite comenzar con un costo mínimo y aumentar la
infraestructura únicamente cuando el volumen o los requerimientos de negocio lo
justifiquen.