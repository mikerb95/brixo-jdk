# ═══════════════════════════════════════════════════════════════
# Brixo API — Multi-stage Docker build
# Stack:  Spring Boot 3.3.5 / Java 21 / Maven
# Stages: (1) deps  →  (2) build  →  (3) runtime (JRE-only)
# ═══════════════════════════════════════════════════════════════

# ────────────────────────────────────────────────
# Stage 1 — Dependency cache
# ────────────────────────────────────────────────
FROM eclipse-temurin:21-jdk-alpine AS deps

WORKDIR /app

# Solo pom + wrapper → capa cacheada mientras no cambien deps
COPY pom.xml .
COPY .mvn .mvn
COPY mvnw .
RUN chmod +x mvnw

# Resolver dependencias offline (cache de Maven en la capa)
RUN --mount=type=cache,target=/root/.m2/repository \
    ./mvnw dependency:go-offline -B -q

# ────────────────────────────────────────────────
# Stage 2 — Compilación
# ────────────────────────────────────────────────
FROM deps AS builder

COPY src src

# Compilar sin tests — reutiliza cache de Maven
RUN --mount=type=cache,target=/root/.m2/repository \
    ./mvnw package -DskipTests -B -q \
    && mv target/*.jar target/app.jar

# Extraer Spring Boot layered JAR para optimizar capas Docker
RUN java -Djarmode=layertools -jar target/app.jar extract --destination /layers

# ────────────────────────────────────────────────
# Stage 3 — Runtime (solo JRE, ~200 MB)
# ────────────────────────────────────────────────
FROM eclipse-temurin:21-jre-alpine AS runtime

LABEL maintainer="Brixo Team" \
    description="Brixo API — Spring Boot 3.3.5 / Java 21" \
    version="1.0.0"

WORKDIR /app

# Instalar curl para healthchecks
RUN apk add --no-cache curl

# Usuario no-root
RUN addgroup -S brixo && adduser -S brixo -G brixo

# Copiar capas del JAR (orden = menos → más volátil → mejor cache)
COPY --from=builder /layers/dependencies/        ./
COPY --from=builder /layers/spring-boot-loader/  ./
COPY --from=builder /layers/snapshot-dependencies/ ./
COPY --from=builder /layers/application/         ./

# Directorios de escritura
RUN mkdir -p uploads/profiles logs \
    && chown -R brixo:brixo /app

USER brixo

EXPOSE 8080

# Health-check integrado
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8080/actuator/health || exit 1

# JVM tuning: ZGC generacional, contenedor-aware, Virtual Threads
ENTRYPOINT ["java", \
    "-XX:+UseZGC", \
    "-XX:+ZGenerational", \
    "-XX:MaxRAMPercentage=75.0", \
    "-XX:+UseContainerSupport", \
    "-Djava.security.egd=file:/dev/./urandom", \
    "org.springframework.boot.loader.launch.JarLauncher"]
