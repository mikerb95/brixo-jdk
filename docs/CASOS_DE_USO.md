# Brixo — Documento de Casos de Uso

> **Proyecto:** Brixo — Plataforma de Servicios para el Hogar y Construcción  
> **Fecha de generación:** 11 de febrero de 2026  
> **Stacks:** CodeIgniter 4 (PHP) + Spring Boot 3.3.5 (Java 21) con Thymeleaf  
> **Base de datos:** MySQL (utf8mb4)

---

## Tabla de Contenidos

1. [Resumen del Sistema](#1-resumen-del-sistema)
2. [Actores del Sistema](#2-actores-del-sistema)
3. [Diagrama de Actores y Módulos](#3-diagrama-de-actores-y-módulos)
4. [Casos de Uso por Dominio](#4-casos-de-uso-por-dominio)
   - [4.1 Autenticación y Registro](#41-autenticación-y-registro)
   - [4.2 Gestión de Perfil](#42-gestión-de-perfil)
   - [4.3 Panel de Usuario (Dashboard)](#43-panel-de-usuario-dashboard)
   - [4.4 Solicitudes de Servicio](#44-solicitudes-de-servicio)
   - [4.5 Cotizador con IA](#45-cotizador-con-ia)
   - [4.6 Mensajería](#46-mensajería)
   - [4.7 Descubrimiento de Contratistas](#47-descubrimiento-de-contratistas)
   - [4.8 Catálogo de Servicios](#48-catálogo-de-servicios)
   - [4.9 Administración](#49-administración)
   - [4.10 Analytics](#410-analytics)
   - [4.11 Reportes](#411-reportes)
   - [4.12 Recuperación de Contraseña](#412-recuperación-de-contraseña)
   - [4.13 Páginas Informativas](#413-páginas-informativas)
   - [4.14 Presentación Académica](#414-presentación-académica)
5. [Matriz de Trazabilidad](#5-matriz-de-trazabilidad)
6. [Modelo de Datos Relacionado](#6-modelo-de-datos-relacionado)

---

## 1. Resumen del Sistema

**Brixo** es una plataforma web tipo marketplace que conecta **clientes** (personas que necesitan servicios para el hogar o construcción) con **contratistas** (profesionales que ofrecen dichos servicios) en Colombia. La plataforma incluye:

- Registro y autenticación multi-rol (Cliente, Contratista, Administrador)
- Catálogo de servicios organizados por categorías y especialidades
- Mapa interactivo de contratistas geolocalizados
- Sistema de solicitudes de servicio (tablón de tareas)
- Cotizador inteligente con IA (LLM multi-proveedor: Groq, OpenAI, Anthropic)
- Mensajería en tiempo real entre usuarios
- Panel de administración con CRUD de usuarios
- Analytics propios (first-party, GDPR-compliant)
- Exportación de reportes en Excel (XLSX)
- Flujo completo de recuperación de contraseña

---

## 2. Actores del Sistema

| Actor | Descripción | Autenticación |
|-------|-------------|---------------|
| **Visitante** (Anónimo) | Usuario no registrado que navega la plataforma | No requerida |
| **Cliente** | Persona que busca contratar servicios para el hogar o construcción | Sesión autenticada, rol `CLIENTE` |
| **Contratista** | Profesional que ofrece servicios de construcción, plomería, electricidad, etc. | Sesión autenticada, rol `CONTRATISTA` |
| **Administrador** | Gestor de la plataforma con acceso total | Sesión autenticada, rol `ADMIN` |
| **Sistema (LLM)** | Servicio de IA externo para generación de cotizaciones | API Key (Groq/OpenAI/Anthropic) |
| **Sistema (Email)** | Servicio SMTP para envío de correos | Configuración SMTP |
| **Sistema (S3)** | Almacenamiento de imágenes en AWS S3 | Credenciales AWS |

---

## 3. Diagrama de Actores y Módulos

```
                          ┌─────────────────────────────────────────┐
                          │              BRIXO PLATFORM              │
                          └─────────────────────────────────────────┘
                                            │
         ┌──────────┬──────────┬────────────┼────────────┬──────────┬──────────┐
         ▼          ▼          ▼            ▼            ▼          ▼          ▼
   ┌──────────┐┌─────────┐┌─────────┐┌──────────┐┌──────────┐┌─────────┐┌─────────┐
   │  Auth &  ││ Catálogo ││  Mapa   ││Solicitud ││Cotizador ││Mensajes ││  Admin  │
   │ Registro ││Servicios ││Interac. ││ Servicio ││   IA     ││         ││  Panel  │
   └──────────┘└─────────┘└─────────┘└──────────┘└──────────┘└─────────┘└─────────┘
       │            │          │           │           │          │          │
   ┌───┴───┐   ┌────┴───┐  ┌──┴──┐   ┌────┴───┐  ┌───┴──┐  ┌───┴──┐  ┌───┴───┐
   │Visitan│   │Visitan │  │Visit│   │Cliente │  │Visit/│  │Client│  │ Admin │
   │Cliente│   │        │  │     │   │Contrat.│  │Client│  │Contr.│  │       │
   │Contrat│   │        │  │     │   │        │  │      │  │      │  │       │
   └───────┘   └────────┘  └─────┘   └────────┘  └──────┘  └──────┘  └───────┘
```

---

## 4. Casos de Uso por Dominio

---

### 4.1 Autenticación y Registro

#### CU-AUTH-01: Registrar Usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CU-AUTH-01 |
| **Nombre** | Registrar nuevo usuario |
| **Actor principal** | Visitante |
| **Descripción** | Un visitante crea una cuenta como Cliente o Contratista en la plataforma |
| **Precondiciones** | El visitante no tiene una cuenta registrada con ese correo |
| **Flujo principal** | 1. El visitante accede a la página de login/registro<br>2. Selecciona tipo de cuenta (Cliente o Contratista)<br>3. Completa el formulario: nombre, correo, contraseña, teléfono, ciudad<br>4. Si es Contratista, proporciona ubicación en mapa, experiencia y descripción del perfil<br>5. Opcionalmente sube foto de perfil<br>6. El sistema valida: correo único en las 3 tablas (CLIENTE, CONTRATISTA, ADMIN), contraseña fuerte (8+ caracteres, mayúscula, minúscula, dígito, símbolo)<br>7. El sistema hashea la contraseña con BCrypt<br>8. Si hay foto, la sube a S3 o almacenamiento local<br>9. El sistema crea el registro en la tabla correspondiente<br>10. Redirige a `/login` con mensaje de éxito |
| **Flujos alternativos** | - Correo ya existe → error con mensaje<br>- Contraseña débil → error con validación<br>- Contraseñas no coinciden → error |
| **Postcondiciones** | Nueva cuenta creada en BD. El usuario puede iniciar sesión |
| **Endpoints** | `POST /register` |
| **Tablas** | `CLIENTE` o `CONTRATISTA` |

#### CU-AUTH-02: Iniciar Sesión

| Campo | Detalle |
|-------|---------|
| **ID** | CU-AUTH-02 |
| **Nombre** | Iniciar sesión |
| **Actor principal** | Visitante con cuenta registrada |
| **Descripción** | Un usuario se autentica en la plataforma con sus credenciales |
| **Precondiciones** | El usuario tiene una cuenta activa |
| **Flujo principal** | 1. El usuario accede a `/login`<br>2. Ingresa correo y contraseña<br>3. El sistema busca el correo secuencialmente en: CLIENTE → CONTRATISTA → ADMIN<br>4. Verifica la contraseña con BCrypt<br>5. Crea sesión con: id, nombre, correo, rol, foto_perfil<br>6. Redirige según rol: ADMIN → `/admin`, otros → `/panel` |
| **Flujos alternativos** | - Credenciales inválidas → redirige a `/login?error=true`<br>- Soporte para parámetro `redirect_to` (PHP) |
| **Postcondiciones** | Sesión HTTP creada (Spring Session JDBC). Cookie `SESSION` establecida |
| **Endpoints** | `GET /login`, `POST /login` (Spring Security) |

#### CU-AUTH-03: Cerrar Sesión

| Campo | Detalle |
|-------|---------|
| **ID** | CU-AUTH-03 |
| **Nombre** | Cerrar sesión |
| **Actor principal** | Usuario autenticado (cualquier rol) |
| **Descripción** | El usuario cierra su sesión activa |
| **Flujo principal** | 1. El usuario hace clic en "Cerrar sesión"<br>2. El sistema destruye/invalida la sesión<br>3. Elimina cookie `SESSION`<br>4. Redirige a `/` |
| **Postcondiciones** | Sesión invalidada. El usuario debe re-autenticarse |
| **Endpoints** | `POST /logout` (Spring Security) |

---

### 4.2 Gestión de Perfil

#### CU-PERF-01: Ver Perfil Público de Contratista

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PERF-01 |
| **Nombre** | Ver perfil público de contratista |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | Cualquier persona visualiza el perfil completo de un contratista |
| **Flujo principal** | 1. El usuario accede a `/perfil/ver/{id}`<br>2. El sistema carga: datos del contratista, foto de perfil, calificación promedio, reseñas, servicios ofrecidos (vía `CONTRATISTA_SERVICIO`), certificaciones, estado de verificación<br>3. Muestra la vista del perfil público |
| **Flujos alternativos** | - Contratista no encontrado → página 404 |
| **Postcondiciones** | Ninguna (solo lectura) |
| **Endpoints** | `GET /perfil/ver/{id}` |
| **Tablas** | `CONTRATISTA`, `CONTRATISTA_SERVICIO`, `SERVICIO`, `CERTIFICACION`, `RESENA`, `CONTRATO`, `CLIENTE` |

#### CU-PERF-02: Editar Perfil Propio

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PERF-02 |
| **Nombre** | Editar perfil propio |
| **Actor principal** | Cliente / Contratista |
| **Descripción** | El usuario modifica sus datos personales |
| **Flujo principal** | 1. El usuario accede a `/perfil/editar`<br>2. El sistema carga los datos actuales del usuario según su rol<br>3. El usuario modifica: nombre, teléfono, ciudad, y si es contratista: ubicación en mapa, experiencia, descripción, portafolio<br>4. Opcionalmente sube nueva foto de perfil (máx 5MB, formatos: png/jpg/jpeg/webp)<br>5. El sistema valida y actualiza en BD<br>6. Si hay foto nueva, genera thumbnails (300×300, 64×64) y sube a S3 o local<br>7. Actualiza datos en sesión |
| **Postcondiciones** | Datos actualizados en BD y sesión |
| **Endpoints** | `GET /perfil/editar`, `POST /perfil/actualizar` |
| **Tablas** | `CLIENTE` o `CONTRATISTA` |

---

### 4.3 Panel de Usuario (Dashboard)

#### CU-PANEL-01: Ver Dashboard de Cliente

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PANEL-01 |
| **Nombre** | Ver panel de cliente |
| **Actor principal** | Cliente |
| **Descripción** | El cliente accede a su panel principal donde ve un resumen de su actividad |
| **Flujo principal** | 1. El cliente accede a `/panel`<br>2. El sistema carga: contratos del cliente, reseñas escritas, solicitudes creadas<br>3. Muestra vista de panel de cliente con resúmenes |
| **Postcondiciones** | Ninguna (solo lectura) |
| **Endpoints** | `GET /panel` |
| **Tablas** | `CLIENTE`, `CONTRATO`, `RESENA`, `SOLICITUD` |

#### CU-PANEL-02: Ver Dashboard de Contratista

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PANEL-02 |
| **Nombre** | Ver panel de contratista |
| **Actor principal** | Contratista |
| **Descripción** | El contratista accede a su panel con resumen de actividad y solicitudes disponibles |
| **Flujo principal** | 1. El contratista accede a `/panel`<br>2. El sistema carga: contratos asignados, reseñas recibidas, solicitudes abiertas disponibles para tomar<br>3. Muestra vista de panel de contratista |
| **Postcondiciones** | Ninguna (solo lectura) |
| **Endpoints** | `GET /panel` |
| **Tablas** | `CONTRATISTA`, `CONTRATO`, `RESENA`, `SOLICITUD` |

---

### 4.4 Solicitudes de Servicio

#### CU-SOL-01: Crear Solicitud de Servicio

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SOL-01 |
| **Nombre** | Crear solicitud de servicio |
| **Actor principal** | Cliente |
| **Descripción** | El cliente crea una nueva solicitud de servicio que puede ser abierta (tablón) o dirigida a un contratista específico |
| **Precondiciones** | El usuario está autenticado como Cliente |
| **Flujo principal** | 1. El cliente accede a `/solicitud/nueva`<br>2. Opcionalmente llega con `?contratista={id}` para solicitud directa<br>3. Opcionalmente la solicitud viene pre-llenada desde el cotizador (sesión)<br>4. Completa: título, descripción, presupuesto, departamento, ciudad, dirección<br>5. El sistema compone la ubicación como texto<br>6. Crea la solicitud con estado `ABIERTA`<br>7. Redirige a `/panel` con mensaje de éxito |
| **Postcondiciones** | Nueva solicitud en tabla `SOLICITUD` con estado `ABIERTA` |
| **Endpoints** | `GET /solicitud/nueva`, `POST /solicitud/guardar` |
| **Tablas** | `SOLICITUD` |

#### CU-SOL-02: Editar Solicitud de Servicio

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SOL-02 |
| **Nombre** | Editar solicitud existente |
| **Actor principal** | Cliente |
| **Descripción** | El cliente modifica una solicitud que creó anteriormente |
| **Precondiciones** | La solicitud pertenece al cliente autenticado |
| **Flujo principal** | 1. El cliente accede a `/solicitud/editar/{id}`<br>2. El sistema verifica propiedad (ownership)<br>3. El cliente modifica los campos<br>4. El sistema actualiza en BD<br>5. Redirige a `/panel` |
| **Flujos alternativos** | - Solicitud no encontrada → redirige a panel<br>- No es propietario → acceso denegado |
| **Endpoints** | `GET /solicitud/editar/{id}`, `POST /solicitud/actualizar/{id}` |

#### CU-SOL-03: Eliminar Solicitud de Servicio

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SOL-03 |
| **Nombre** | Eliminar solicitud |
| **Actor principal** | Cliente |
| **Descripción** | El cliente elimina una solicitud de servicio propia |
| **Precondiciones** | La solicitud pertenece al cliente autenticado |
| **Flujo principal** | 1. El cliente solicita eliminar vía `/solicitud/eliminar/{id}`<br>2. El sistema verifica propiedad<br>3. Elimina la solicitud de la BD<br>4. Redirige a `/panel` |
| **Endpoints** | `GET /solicitud/eliminar/{id}` |

#### CU-SOL-04: Ver Tablón de Tareas (Solicitudes Abiertas)

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SOL-04 |
| **Nombre** | Consultar tablón de tareas |
| **Actor principal** | Contratista |
| **Descripción** | El contratista consulta todas las solicitudes abiertas disponibles para tomar |
| **Flujo principal** | 1. El contratista accede a `/tablon-tareas`<br>2. El sistema consulta solicitudes donde `id_contratista IS NULL` y estado = `ABIERTA`<br>3. Muestra listado de solicitudes abiertas |
| **Postcondiciones** | Ninguna (solo lectura) |
| **Endpoints** | `GET /tablon-tareas` |
| **Tablas** | `SOLICITUD` |

#### CU-SOL-05: Ver Solicitudes Asignadas (Contratista)

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SOL-05 |
| **Nombre** | Ver solicitudes/contratos asignados |
| **Actor principal** | Contratista |
| **Descripción** | El contratista ve los contratos y solicitudes que tiene asignados |
| **Flujo principal** | 1. El contratista accede a `/solicitudes`<br>2. El sistema consulta contratos asignados al contratista con datos del cliente<br>3. Muestra listado (máximo 50 registros) |
| **Endpoints** | `GET /solicitudes` |
| **Tablas** | `CONTRATO`, `CLIENTE` |

---

### 4.5 Cotizador con IA

#### CU-COT-01: Generar Cotización con IA

| Campo | Detalle |
|-------|---------|
| **ID** | CU-COT-01 |
| **Nombre** | Generar cotización inteligente |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario describe un trabajo y la IA genera una cotización detallada |
| **Flujo principal** | 1. El usuario accede a `/cotizador`<br>2. Ingresa una descripción del trabajo (mínimo 10 caracteres)<br>3. El sistema envía la descripción al servicio LLM configurado<br>4. El LLM retorna una cotización estructurada en JSON con: servicio principal, materiales (nombre, cantidad estimada), personal requerido (rol, horas estimadas), nivel de complejidad (baja/media/alta)<br>5. El sistema valida el esquema de la respuesta<br>6. Almacena el resultado en sesión<br>7. Retorna la cotización al frontend (AJAX/JSON) |
| **Flujos alternativos** | - Descripción muy corta → error de validación<br>- Fallo del LLM → respuesta de error<br>- Sin API key configurada → cotización demo hardcodeada (280,000 COP) |
| **Proveedores IA** | Groq (`llama-3.3-70b-versatile`), OpenAI (`gpt-4o-mini`), Anthropic (`claude-sonnet-4-20250514`) |
| **Endpoints** | `GET /cotizador`, `POST /cotizador/generar` |

#### CU-COT-02: Confirmar Cotización

| Campo | Detalle |
|-------|---------|
| **ID** | CU-COT-02 |
| **Nombre** | Confirmar cotización generada |
| **Actor principal** | Cliente (autenticado) |
| **Precondiciones** | Existe una cotización generada en la sesión del usuario |
| **Descripción** | El cliente confirma la cotización y el sistema la persiste y pre-llena una solicitud |
| **Flujo principal** | 1. El cliente confirma la cotización desde la interfaz<br>2. El sistema guarda en tabla `COTIZACION_CONFIRMADA`: descripción, servicio principal, materiales (JSON), personal (JSON), complejidad, estado `CONFIRMADA`<br>3. Pre-llena datos de solicitud en la sesión<br>4. Redirige a `/solicitud/nueva` (con datos pre-llenados) o a `/cotizador/exito` |
| **Postcondiciones** | Cotización persistida en BD. Datos de solicitud en sesión |
| **Endpoints** | `POST /cotizador/confirmar`, `GET /cotizador/exito` |
| **Tablas** | `COTIZACION_CONFIRMADA` |

---

### 4.6 Mensajería

#### CU-MSG-01: Ver Conversaciones

| Campo | Detalle |
|-------|---------|
| **ID** | CU-MSG-01 |
| **Nombre** | Listar conversaciones |
| **Actor principal** | Cliente / Contratista |
| **Descripción** | El usuario ve todas sus conversaciones activas |
| **Flujo principal** | 1. El usuario accede a `/mensajes`<br>2. El sistema consulta conversaciones únicas del usuario (agrupadas por par de interlocutores)<br>3. Muestra: nombre del otro usuario, vista previa del último mensaje, indicador de no leído |
| **Endpoints** | `GET /mensajes` |
| **Tablas** | `MENSAJE`, `CLIENTE`, `CONTRATISTA` |

#### CU-MSG-02: Chat entre Usuarios

| Campo | Detalle |
|-------|---------|
| **ID** | CU-MSG-02 |
| **Nombre** | Abrir y mantener chat |
| **Actor principal** | Cliente / Contratista |
| **Descripción** | El usuario abre un chat con otro usuario y puede enviar/recibir mensajes |
| **Flujo principal** | 1. El usuario accede a `/mensajes/chat/{otroId}/{otroRol}`<br>2. El sistema carga todos los mensajes de la conversación ordenados cronológicamente<br>3. Marca los mensajes entrantes como leídos<br>4. El usuario puede enviar nuevos mensajes vía AJAX (`POST /mensajes/enviar`)<br>5. El frontend hace polling para nuevos mensajes (`GET /mensajes/nuevos/{otroId}/{otroRol}`) |
| **Esquema de mensajes** | Patrón polimórfico: `remitente_id` + `remitente_rol` → `CLIENTE` o `CONTRATISTA` |
| **Endpoints** | `GET /mensajes/chat/{id}/{rol}`, `POST /mensajes/enviar`, `GET /mensajes/nuevos/{id}/{rol}` |
| **Tablas** | `MENSAJE` |

#### CU-MSG-03: Enviar Mensaje

| Campo | Detalle |
|-------|---------|
| **ID** | CU-MSG-03 |
| **Nombre** | Enviar mensaje |
| **Actor principal** | Cliente / Contratista |
| **Descripción** | El usuario envía un mensaje de texto a otro usuario |
| **Flujo principal** | 1. El usuario escribe el contenido del mensaje<br>2. Envía vía AJAX con: `destinatario_id`, `destinatario_rol`, `contenido`<br>3. El sistema crea el registro en `MENSAJE` con `leido = 0`<br>4. Retorna `{status: "success"}` |
| **Endpoints** | `POST /mensajes/enviar` (JSON) |
| **Tablas** | `MENSAJE` |

---

### 4.7 Descubrimiento de Contratistas

#### CU-MAP-01: Explorar Mapa de Contratistas

| Campo | Detalle |
|-------|---------|
| **ID** | CU-MAP-01 |
| **Nombre** | Ver mapa interactivo de contratistas |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario explora un mapa interactivo con los contratistas geolocalizados |
| **Flujo principal** | 1. El usuario accede a `/map` o `/mapa`<br>2. El sistema consulta todos los contratistas con `ubicacion_mapa` definido<br>3. Para cada contratista: parsea coordenadas ("lat,lng"), carga calificación promedio, cuenta total de reseñas, obtiene lista de servicios ofrecidos, resuelve foto de perfil (S3, local, o avatar generado)<br>4. Serializa como JSON y envía a la vista del mapa<br>5. El frontend renderiza los pins en el mapa interactivo |
| **Datos por pin** | id, nombre, ciudad, fotoPerfil, latitud, longitud, calificaciónPromedio, totalReseñas, servicios[] |
| **Endpoints** | `GET /map`, `GET /mapa` |
| **Tablas** | `CONTRATISTA`, `CONTRATISTA_SERVICIO`, `SERVICIO`, `RESENA`, `CONTRATO` |

#### CU-ESP-01: Explorar Especialidades/Categorías

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ESP-01 |
| **Nombre** | Navegar categorías de servicios |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario explora las categorías de servicios disponibles |
| **Flujo principal** | 1. El usuario accede a `/especialidades`<br>2. El sistema muestra todas las categorías con hasta 4 servicios cada una<br>3. El usuario puede seleccionar una categoría para ver todos sus servicios |
| **Endpoints** | `GET /especialidades`, `GET /especialidades/categoria/{id}` |
| **Tablas** | `CATEGORIA`, `SERVICIO` |

---

### 4.8 Catálogo de Servicios

#### CU-SRV-01: Navegar Catálogo de Servicios

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SRV-01 |
| **Nombre** | Explorar catálogo de servicios |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario navega el catálogo completo de servicios, con opción de filtrar por categoría |
| **Flujo principal** | 1. El usuario accede a `/servicios`<br>2. Opcionalmente filtra por categoría (`?categoriaId={id}`)<br>3. El sistema muestra la lista de servicios con nombre, descripción, precio estimado e imagen |
| **Endpoints** | `GET /servicios` |
| **Tablas** | `SERVICIO`, `CATEGORIA` |

#### CU-SRV-02: Ver Detalle de Servicio

| Campo | Detalle |
|-------|---------|
| **ID** | CU-SRV-02 |
| **Nombre** | Ver detalle de un servicio |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario ve el detalle completo de un servicio y servicios relacionados |
| **Flujo principal** | 1. El usuario accede a `/servicios/{id}`<br>2. El sistema carga el servicio con su categoría<br>3. Muestra hasta 3 servicios relacionados de la misma categoría |
| **Endpoints** | `GET /servicios/{id}` |
| **Tablas** | `SERVICIO`, `CATEGORIA` |

---

### 4.9 Administración

#### CU-ADM-01: Ver Dashboard Administrativo

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ADM-01 |
| **Nombre** | Acceder al panel de administración |
| **Actor principal** | Administrador |
| **Descripción** | El administrador accede a un dashboard con métricas generales de la plataforma |
| **Flujo principal** | 1. El administrador accede a `/admin`<br>2. El sistema muestra: conteo de clientes, contratistas, admins, solicitudes, eventos de analytics (últimas 24h), los 5 usuarios más recientes |
| **Endpoints** | `GET /admin` |
| **Tablas** | `CLIENTE`, `CONTRATISTA`, `ADMIN`, `SOLICITUD`, `analytics_events` |

#### CU-ADM-02: Listar Usuarios

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ADM-02 |
| **Nombre** | Listar y buscar usuarios |
| **Actor principal** | Administrador |
| **Descripción** | El administrador lista todos los usuarios con opciones de filtrado y búsqueda |
| **Flujo principal** | 1. El administrador accede a `/admin/usuarios`<br>2. Filtra por tipo (`?tipo=clientes|contratistas|admins|todos`)<br>3. Busca por nombre o correo (`?q=texto`)<br>4. El sistema muestra lista unificada de usuarios |
| **Endpoints** | `GET /admin/usuarios` |

#### CU-ADM-03: Crear Usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ADM-03 |
| **Nombre** | Crear nuevo usuario desde admin |
| **Actor principal** | Administrador |
| **Descripción** | El administrador crea manualmente un usuario de cualquier tipo |
| **Flujo principal** | 1. Accede a `/admin/usuarios/crear`<br>2. Completa formulario: nombre, correo, contraseña, tipo (cliente/contratista/admin), teléfono, ciudad<br>3. El sistema valida unicidad de correo en las 3 tablas<br>4. Hashea la contraseña<br>5. Crea el registro en la tabla correspondiente |
| **Endpoints** | `GET /admin/usuarios/crear`, `POST /admin/usuarios/guardar` |
| **Tablas** | `CLIENTE`, `CONTRATISTA` o `ADMIN` |

#### CU-ADM-04: Editar Usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ADM-04 |
| **Nombre** | Editar usuario existente |
| **Actor principal** | Administrador |
| **Descripción** | El administrador modifica los datos de cualquier usuario |
| **Flujo principal** | 1. Accede a `/admin/usuarios/editar/{tipo}/{id}`<br>2. Modifica los campos deseados<br>3. El cambio de contraseña es opcional (solo si se proporciona)<br>4. El sistema actualiza en BD |
| **Endpoints** | `GET /admin/usuarios/editar/{tipo}/{id}`, `POST /admin/usuarios/actualizar` |

#### CU-ADM-05: Eliminar Usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ADM-05 |
| **Nombre** | Eliminar usuario |
| **Actor principal** | Administrador |
| **Descripción** | El administrador elimina un usuario de la plataforma |
| **Flujo principal** | 1. El administrador solicita eliminar vía `/admin/usuarios/eliminar/{tipo}/{id}`<br>2. El sistema verifica que el admin no se esté eliminando a sí mismo<br>3. Elimina el registro de la BD<br>4. Redirige con mensaje de éxito |
| **Flujos alternativos** | - Intento de auto-eliminación → error con protección |
| **Endpoints** | `GET /admin/usuarios/eliminar/{tipo}/{id}` |

---

### 4.10 Analytics

#### CU-ANA-01: Rastrear Eventos de Usuario

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ANA-01 |
| **Nombre** | Registrar evento de analytics |
| **Actor principal** | Sistema (JavaScript del navegador) |
| **Descripción** | El sistema de analytics del frontend envía eventos de usuario de forma transparente |
| **Flujo principal** | 1. El JavaScript del navegador detecta un evento (pageview, click, etc.)<br>2. Envía payload JSON vía `navigator.sendBeacon` a `POST /api/v1/track`<br>3. El sistema valida el payload y filtra bots<br>4. Anonimiza la IP (SHA-256 + salt, truncado a 16 hex, GDPR-compliant)<br>5. Persiste en tabla `analytics_events`<br>6. Retorna `204 No Content` |
| **Eventos soportados** | `pageview`, `engagement`, `click_cta`, `signup_click`, `cotizador_start`, `cotizador_complete`, `solicitud_created`, `search`, `error` |
| **Endpoints** | `POST /api/v1/track` (público, CSRF deshabilitado) |
| **Tablas** | `analytics_events` |

#### CU-ANA-02: Ver Dashboard de Analytics

| Campo | Detalle |
|-------|---------|
| **ID** | CU-ANA-02 |
| **Nombre** | Consultar dashboard de analytics |
| **Actor principal** | Administrador |
| **Descripción** | El administrador visualiza métricas analíticas de la plataforma |
| **Flujo principal** | 1. El administrador accede a `/analytics/dashboard`<br>2. Selecciona período (`?days=30`, rango 1-365)<br>3. El sistema muestra: visitantes únicos, sesiones, pageviews, duración promedio, páginas populares, desglose por dispositivo, estadísticas de navegador, eventos personalizados |
| **Endpoints** | `GET /analytics/dashboard` |
| **Tablas** | `analytics_events` |

---

### 4.11 Reportes

#### CU-REP-01: Exportar Reporte de Contratistas

| Campo | Detalle |
|-------|---------|
| **ID** | CU-REP-01 |
| **Nombre** | Descargar reporte de contratistas en Excel |
| **Actor principal** | Usuario autenticado (Admin/otros con acceso) |
| **Descripción** | El usuario descarga un archivo XLSX con el listado completo de contratistas |
| **Flujo principal** | 1. El usuario accede a `/reportes/contratistas`<br>2. El sistema genera un archivo XLSX con Apache POI<br>3. Columnas: ID, Nombre, Correo, Teléfono, Ciudad, Verificado, Fecha Registro<br>4. Retorna archivo binario con header `Content-Disposition: attachment` |
| **Postcondiciones** | Archivo XLSX descargado |
| **Endpoints** | `GET /reportes/contratistas` |
| **Tablas** | `CONTRATISTA` |

#### CU-REP-02: Exportar Reporte de Solicitudes

| Campo | Detalle |
|-------|---------|
| **ID** | CU-REP-02 |
| **Nombre** | Descargar reporte de solicitudes en Excel |
| **Actor principal** | Usuario autenticado (Admin/otros con acceso) |
| **Descripción** | El usuario descarga un archivo XLSX con todas las solicitudes |
| **Flujo principal** | 1. El usuario accede a `/reportes/solicitudes-xlsx`<br>2. El sistema genera archivo XLSX<br>3. Columnas: ID, Título, Descripción (truncada a 100 chars), Estado, Cliente ID, Contratista ID, Fecha Creación<br>4. Retorna archivo binario |
| **Endpoints** | `GET /reportes/solicitudes-xlsx` |
| **Tablas** | `SOLICITUD` |

---

### 4.12 Recuperación de Contraseña

#### CU-PWD-01: Solicitar Restablecimiento de Contraseña

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PWD-01 |
| **Nombre** | Solicitar enlace de restablecimiento |
| **Actor principal** | Visitante |
| **Descripción** | El usuario solicita un enlace para restablecer su contraseña |
| **Flujo principal** | 1. El usuario accede a `/password/forgot`<br>2. Ingresa su correo electrónico<br>3. El sistema genera un token UUID, lo hashea con SHA-256, establece expiración de 1 hora<br>4. Almacena en tabla `password_resets`<br>5. Envía correo HTML con enlace de restablecimiento<br>6. Muestra mensaje genérico (anti-enumeración de cuentas) |
| **Flujos alternativos** | - Correo no existe → muestra el mismo mensaje genérico (seguridad) |
| **Postcondiciones** | Token almacenado en BD. Correo enviado (simulado en dev) |
| **Endpoints** | `GET /password/forgot`, `POST /password/send-reset` |
| **Tablas** | `password_resets` |

#### CU-PWD-02: Restablecer Contraseña

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PWD-02 |
| **Nombre** | Restablecer contraseña con token |
| **Actor principal** | Visitante |
| **Descripción** | El usuario restablece su contraseña usando el enlace recibido por correo |
| **Flujo principal** | 1. El usuario accede a `/password/reset/{token}`<br>2. El sistema valida el token (hashea y busca, verifica expiración)<br>3. Muestra formulario de nueva contraseña<br>4. El usuario ingresa nueva contraseña y confirmación (mínimo 8 caracteres)<br>5. El sistema actualiza la contraseña en CLIENTE o CONTRATISTA<br>6. Elimina el token usado<br>7. Redirige a `/login` |
| **Flujos alternativos** | - Token inválido o expirado → redirige a `/password/forgot` con error<br>- Contraseñas no coinciden → error de validación |
| **Endpoints** | `GET /password/reset/{token}`, `POST /password/update` |
| **Tablas** | `password_resets`, `CLIENTE` o `CONTRATISTA` |

---

### 4.13 Páginas Informativas

#### CU-INFO-01: Consultar Páginas Estáticas

| Campo | Detalle |
|-------|---------|
| **ID** | CU-INFO-01 |
| **Nombre** | Navegar páginas informativas |
| **Actor principal** | Visitante / Usuario autenticado |
| **Descripción** | El usuario accede a páginas de contenido estático informativo |
| **Páginas disponibles** | |

| Ruta | Contenido |
|------|-----------|
| `/` | Página de inicio (landing page) |
| `/sobre-nosotros` | Acerca de la empresa |
| `/como-funciona` | Cómo funciona la plataforma |
| `/seguridad` | Políticas de seguridad |
| `/ayuda` | Centro de ayuda |
| `/unete-pro` | Invitación para contratistas |
| `/historias-exito` | Casos de éxito |
| `/recursos` | Recursos útiles |
| `/carreras` | Oportunidades laborales |
| `/prensa` | Notas de prensa |
| `/blog` | Blog de contenidos |
| `/politica-cookies` | Política de cookies |
| `/productos` | Página de productos |
| `/showcase` | Showcase de diseño del sistema |

---

### 4.14 Presentación Académica

#### CU-PRES-01: Sistema de Presentación con Control Remoto

| Campo | Detalle |
|-------|---------|
| **ID** | CU-PRES-01 |
| **Nombre** | Controlar presentación de diapositivas |
| **Actor principal** | Presentador (cualquier usuario) |
| **Descripción** | Sistema de presentación académica con múltiples vistas sincronizadas |
| **Componentes** | |

| Vista | Ruta | Descripción |
|-------|------|-------------|
| Proyector | `/slides` | Vista full-screen de diapositivas |
| Control remoto | `/remote` | Control desde dispositivo móvil |
| Presentador | `/presenter` | Vista con notas del presentador |
| Panel principal | `/main-panel` | Panel de control completo |
| Demo | `/demo` | Proyector con demos en vivo (iframe) |

| API | Ruta | Descripción |
|-----|------|-------------|
| Slide state | `GET/POST /api/slide` | Obtener/cambiar diapositiva actual (1-11) |
| Demo state | `GET/POST /api/demo` | Cambiar entre modo slides y URL de demo |

**Estado:** En memoria (`AtomicInteger`, `AtomicReference`). Total de 11 diapositivas.

---

## 5. Matriz de Trazabilidad

### Caso de Uso → Endpoints → Tablas

| ID | Caso de Uso | Endpoints | Tablas Afectadas | Actor |
|----|-------------|-----------|------------------|-------|
| CU-AUTH-01 | Registrar usuario | `POST /register` | CLIENTE / CONTRATISTA | Visitante |
| CU-AUTH-02 | Iniciar sesión | `GET/POST /login` | CLIENTE, CONTRATISTA, ADMIN | Visitante |
| CU-AUTH-03 | Cerrar sesión | `POST /logout` | (sesión) | Autenticado |
| CU-PERF-01 | Ver perfil público | `GET /perfil/ver/{id}` | CONTRATISTA, SERVICIO, CERTIFICACION, RESENA | Cualquiera |
| CU-PERF-02 | Editar perfil | `GET/POST /perfil/editar,actualizar` | CLIENTE / CONTRATISTA | Autenticado |
| CU-PANEL-01 | Dashboard cliente | `GET /panel` | CLIENTE, CONTRATO, RESENA, SOLICITUD | Cliente |
| CU-PANEL-02 | Dashboard contratista | `GET /panel` | CONTRATISTA, CONTRATO, RESENA, SOLICITUD | Contratista |
| CU-SOL-01 | Crear solicitud | `GET/POST /solicitud/nueva,guardar` | SOLICITUD | Cliente |
| CU-SOL-02 | Editar solicitud | `GET/POST /solicitud/editar,actualizar/{id}` | SOLICITUD | Cliente |
| CU-SOL-03 | Eliminar solicitud | `GET /solicitud/eliminar/{id}` | SOLICITUD | Cliente |
| CU-SOL-04 | Tablón de tareas | `GET /tablon-tareas` | SOLICITUD | Contratista |
| CU-SOL-05 | Solicitudes asignadas | `GET /solicitudes` | CONTRATO, CLIENTE | Contratista |
| CU-COT-01 | Generar cotización IA | `GET/POST /cotizador,generar` | (sesión, LLM externo) | Cualquiera |
| CU-COT-02 | Confirmar cotización | `POST /cotizador/confirmar` | COTIZACION_CONFIRMADA | Cliente |
| CU-MSG-01 | Ver conversaciones | `GET /mensajes` | MENSAJE | Autenticado |
| CU-MSG-02 | Chat entre usuarios | `GET /mensajes/chat/{id}/{rol}` | MENSAJE | Autenticado |
| CU-MSG-03 | Enviar mensaje | `POST /mensajes/enviar` | MENSAJE | Autenticado |
| CU-MAP-01 | Mapa de contratistas | `GET /map` | CONTRATISTA, SERVICIO, RESENA | Cualquiera |
| CU-ESP-01 | Explorar especialidades | `GET /especialidades/**` | CATEGORIA, SERVICIO | Cualquiera |
| CU-SRV-01 | Catálogo de servicios | `GET /servicios` | SERVICIO, CATEGORIA | Cualquiera |
| CU-SRV-02 | Detalle de servicio | `GET /servicios/{id}` | SERVICIO, CATEGORIA | Cualquiera |
| CU-ADM-01 | Dashboard admin | `GET /admin` | CLIENTE, CONTRATISTA, ADMIN, SOLICITUD | Admin |
| CU-ADM-02 | Listar usuarios | `GET /admin/usuarios` | CLIENTE, CONTRATISTA, ADMIN | Admin |
| CU-ADM-03 | Crear usuario | `GET/POST /admin/usuarios/crear,guardar` | CLIENTE / CONTRATISTA / ADMIN | Admin |
| CU-ADM-04 | Editar usuario | `GET/POST /admin/usuarios/editar,actualizar` | CLIENTE / CONTRATISTA / ADMIN | Admin |
| CU-ADM-05 | Eliminar usuario | `GET /admin/usuarios/eliminar/{tipo}/{id}` | CLIENTE / CONTRATISTA / ADMIN | Admin |
| CU-ANA-01 | Rastrear evento | `POST /api/v1/track` | analytics_events | Sistema (JS) |
| CU-ANA-02 | Dashboard analytics | `GET /analytics/dashboard` | analytics_events | Admin |
| CU-REP-01 | Reporte contratistas | `GET /reportes/contratistas` | CONTRATISTA | Autenticado |
| CU-REP-02 | Reporte solicitudes | `GET /reportes/solicitudes-xlsx` | SOLICITUD | Autenticado |
| CU-PWD-01 | Solicitar reset password | `GET/POST /password/forgot,send-reset` | password_resets | Visitante |
| CU-PWD-02 | Restablecer contraseña | `GET/POST /password/reset,update` | password_resets, CLIENTE/CONTRATISTA | Visitante |
| CU-INFO-01 | Páginas informativas | 14 rutas estáticas | — | Cualquiera |
| CU-PRES-01 | Presentación académica | `/slides,/remote,/presenter,/api/slide,/api/demo` | (memoria) | Cualquiera |

---

## 6. Modelo de Datos Relacionado

### Tablas del Sistema

```
┌─────────────────┐     ┌─────────────────┐     ┌──────────────────┐
│     CLIENTE      │     │   CONTRATISTA    │     │      ADMIN       │
├─────────────────┤     ├─────────────────┤     ├──────────────────┤
│ id_cliente (PK) │     │ id_contratista  │     │ id_admin (PK)    │
│ nombre          │     │ nombre          │     │ nombre           │
│ correo (UQ)     │     │ correo (UQ)     │     │ correo (UQ)      │
│ contrasena      │     │ contrasena      │     │ contrasena       │
│ telefono        │     │ telefono        │     │ foto_perfil      │
│ ciudad          │     │ ciudad          │     │ activo           │
│ foto_perfil     │     │ ubicacion_mapa  │     │ ultimo_acceso    │
│ creado_en       │     │ foto_perfil     │     │ creado_en        │
└────────┬────────┘     │ experiencia     │     └──────────────────┘
         │              │ portafolio      │
         │              │ descripcion     │
         │              │ verificado      │
         │              │ creado_en       │
         │              └───────┬─────────┘
         │                      │
    ┌────┴──────────────────────┴────┐
    │           CONTRATO              │
    ├─────────────────────────────────┤
    │ id_contrato (PK)               │
    │ fecha_inicio, fecha_fin        │
    │ costo_total                    │
    │ estado (ENUM)                  │
    │ id_contratista (FK)            │
    │ id_cliente (FK)                │
    └────────────┬────────────────────┘
                 │
    ┌────────────┴────────────────┐
    │          RESENA              │
    ├──────────────────────────────┤
    │ id_resena (PK)              │
    │ comentario, fecha           │
    │ calificacion (1-5)          │
    │ id_contrato (FK)            │
    │ id_cliente (FK)             │
    └──────────────────────────────┘

┌──────────────────┐     ┌──────────────────────────┐     ┌───────────────┐
│   CATEGORIA      │────▶│       SERVICIO            │◀────│ CONTRATISTA   │
├──────────────────┤     ├──────────────────────────┤     │  _SERVICIO    │
│ id_categoria(PK) │     │ id_servicio (PK)         │     ├───────────────┤
│ nombre           │     │ nombre, descripcion      │     │ id_contratista│
│ descripcion      │     │ precio_estimado          │     │ id_servicio   │
│ imagen_url       │     │ imagen_url               │     │ precio_pers.  │
└──────────────────┘     │ id_categoria (FK)        │     │ desc_pers.    │
                         └──────────────────────────┘     └───────────────┘

┌──────────────────┐     ┌──────────────────────────┐
│   UBICACION      │◀────│  CONTRATISTA_UBICACION   │
├──────────────────┤     ├──────────────────────────┤
│ id_ubicacion(PK) │     │ id_contratista (FK)      │
│ ciudad           │     │ id_ubicacion (FK)        │
│ departamento     │     └──────────────────────────┘
│ direccion        │
│ latitud          │
│ longitud         │
└──────────────────┘

┌──────────────────────┐   ┌──────────────────────┐   ┌────────────────────┐
│      SOLICITUD       │   │       MENSAJE        │   │   CERTIFICACION    │
├──────────────────────┤   ├──────────────────────┤   ├────────────────────┤
│ id_solicitud (PK)    │   │ id_mensaje (PK)      │   │ id_certificado(PK) │
│ titulo, descripcion  │   │ remitente_id/rol     │   │ nombre             │
│ presupuesto          │   │ destinatario_id/rol  │   │ entidad_emisora    │
│ ubicacion            │   │ contenido            │   │ fecha_obtenida     │
│ estado (ENUM)        │   │ leido (0/1)          │   │ id_contratista(FK) │
│ creado_en            │   │ creado_en            │   └────────────────────┘
│ id_cliente (FK)      │   └──────────────────────┘
│ id_contratista (FK)  │
└──────────────────────┘

┌──────────────────────────┐   ┌──────────────────────┐
│  COTIZACION_CONFIRMADA   │   │    password_resets    │
├──────────────────────────┤   ├──────────────────────┤
│ id (PK)                  │   │ id (PK)              │
│ id_cliente               │   │ email                │
│ descripcion              │   │ token (SHA-256)      │
│ servicio_principal       │   │ created_at           │
│ materiales_json (JSON)   │   │ expires_at           │
│ personal_json (JSON)     │   └──────────────────────┘
│ complejidad (ENUM)       │
│ estado (ENUM)            │   ┌──────────────────────┐
│ creado_en                │   │   analytics_events   │
│ confirmado_en            │   ├──────────────────────┤
└──────────────────────────┘   │ id (PK)              │
                               │ visitor_id           │
                               │ session_id           │
                               │ event_type           │
                               │ url, path, referrer  │
                               │ title, screen        │
                               │ viewport, device_type│
                               │ language, browser    │
                               │ platform, ip_anon    │
                               │ extra_json (JSON)    │
                               │ created_at           │
                               └──────────────────────┘
```

---

## Resumen Cuantitativo

| Métrica | Valor |
|---------|-------|
| **Total de casos de uso** | 34 |
| **Endpoints únicos** | ~60 |
| **Tablas de base de datos** | 15 |
| **Actores del sistema** | 4 humanos + 3 sistemas |
| **Rutas públicas** | ~30 |
| **Rutas autenticadas** | ~25 |
| **Rutas solo admin** | 6 |
| **Endpoints AJAX/JSON** | 7 |
| **Descargas de archivos** | 2 (XLSX) |
| **Integraciones externas** | LLM (3 proveedores), AWS S3, SMTP Email |
