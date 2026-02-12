# Brixo
Conectando necesidades con soluciones locales.

Brixo es una plataforma web que conecta a usuarios con profesionales locales (contratistas) para servicios del hogar como obra, carpintería, plomería y más. Permite publicar solicitudes de servicio, buscar profesionales en un mapa interactivo y gestionar contrataciones.

## Stack Tecnológico

### Backend
- **Java 21** con **Spring Boot 3.3.5**
- Spring Security 6 (autenticación basada en roles)
- Spring Data JPA + MySQL 8.0
- Thymeleaf (server-side rendering)
- OpenAI API integration (LLM service)

### Frontend
- HTML5 con Thymeleaf templates
- CSS3 (Bootstrap 5)
- JavaScript (Vanilla + Leaflet.js)
- Mapas interactivos: Leaflet.js + OpenStreetMap

### Testing
- JUnit 5
- Mockito
- Spring MockMvc
- H2 in-memory database para tests

### Infraestructura
- Docker + Docker Compose
- Maven 3.9+
- Compatible con despliegue en Render/Railway/AWS

## Estructura del Proyecto

```
brixo-api/           # Aplicación Spring Boot principal
├── src/main/java    # Código fuente Java
├── src/main/resources/templates  # Vistas Thymeleaf
├── src/test/java    # Tests unitarios e integración
└── pom.xml          # Dependencias Maven

public/              # Assets estáticos (CSS, JS, imágenes)
docs/                # Documentación técnica
```

## Inicio Rápido

```bash
# Clonar el repositorio
git clone <repository-url>
cd brixo-jdk

# Levantar base de datos con Docker
cd brixo-api
docker-compose up -d

# Compilar y ejecutar
./mvnw spring-boot:run
```

La aplicación estará disponible en `http://localhost:8080`

## Migración desde PHP

El código legacy de PHP (CodeIgniter 4) está preservado en el branch `php-legacy` para referencia histórica. La versión actual es una reescritura completa en Spring Boot.

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.
