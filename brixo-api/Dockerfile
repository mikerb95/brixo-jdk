# ═══════════════════════════════════════════════════
# Brixo API — Multi-stage Docker build (Java 21)
# ═══════════════════════════════════════════════════

# ── Stage 1: Build ──
FROM eclipse-temurin:21-jdk-alpine AS builder

WORKDIR /app

# Copiar Maven wrapper y pom.xml primero (layer caching)
COPY pom.xml .
COPY .mvn .mvn
COPY mvnw .
RUN chmod +x mvnw

# Descargar dependencias (cacheado si pom.xml no cambia)
RUN ./mvnw dependency:go-offline -B

# Copiar código fuente y compilar
COPY src src
RUN ./mvnw package -DskipTests -B

# ── Stage 2: Runtime ──
FROM eclipse-temurin:21-jre-alpine

WORKDIR /app

# Copiar JAR desde la etapa de build
COPY --from=builder /app/target/*.jar app.jar

# Crear directorio de uploads
RUN mkdir -p uploads/profiles

# Usuario no-root
RUN addgroup -S brixo && adduser -S brixo -G brixo
RUN chown -R brixo:brixo /app
USER brixo

EXPOSE 8080

# Habilitar Virtual Threads + perfil de producción
ENTRYPOINT ["java", \
    "-XX:+UseZGC", \
    "-XX:+ZGenerational", \
    "-Dspring.profiles.active=prod", \
    "-jar", "app.jar"]
