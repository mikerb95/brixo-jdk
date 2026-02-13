# ═══════════════════════════════════════════════════════════════
# Brixo API — Multi-stage Docker build for Render deployment
# Stack:  Spring Boot 3.3.5 / Java 21 / Maven
# Context: Build from repository root, sources in brixo-api/
# ═══════════════════════════════════════════════════════════════

# ────────────────────────────────────────────────
# Stage 1 — Dependency cache
# ────────────────────────────────────────────────
FROM eclipse-temurin:21-jdk-alpine AS deps

WORKDIR /app

# Copy only pom + wrapper → cached layer while deps don't change
COPY brixo-api/pom.xml .
COPY brixo-api/.mvn .mvn
COPY brixo-api/mvnw .
RUN chmod +x mvnw

# Resolve dependencies offline (Maven cache in layer)
RUN --mount=type=cache,target=/root/.m2/repository \
    ./mvnw dependency:go-offline -B -q

# ────────────────────────────────────────────────
# Stage 2 — Build
# ────────────────────────────────────────────────
FROM deps AS builder

COPY brixo-api/src src

# Build without tests — reuses Maven cache
RUN --mount=type=cache,target=/root/.m2/repository \
    ./mvnw package -DskipTests -B -q \
    && mv target/*.jar target/app.jar

# Extract Spring Boot layered JAR to optimize Docker layers
RUN java -Djarmode=layertools -jar target/app.jar extract --destination /layers

# ────────────────────────────────────────────────
# Stage 3 — Runtime (JRE only, ~200 MB)
# ────────────────────────────────────────────────
FROM eclipse-temurin:21-jre-alpine AS runtime

LABEL maintainer="Brixo Team" \
      description="Brixo API — Spring Boot 3.3.5 / Java 21" \
      version="1.0.0"

WORKDIR /app

# Install curl for healthchecks
RUN apk add --no-cache curl

# Non-root user
RUN addgroup -S brixo && adduser -S brixo -G brixo

# Copy JAR layers (order = less → more volatile → better cache)
COPY --from=builder /layers/dependencies/        ./
COPY --from=builder /layers/spring-boot-loader/  ./
COPY --from=builder /layers/snapshot-dependencies/ ./
COPY --from=builder /layers/application/         ./

# Writable directories
RUN mkdir -p uploads/profiles logs \
    && chown -R brixo:brixo /app

USER brixo

# Render sets PORT dynamically; Spring Boot reads server.port from it
ENV PORT=8080
ENV SPRING_PROFILES_ACTIVE=prod

EXPOSE ${PORT}

# Integrated health-check
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=5 \
    CMD curl -f http://localhost:${PORT}/actuator/health || exit 1

# JVM tuning: ZGC generational, container-aware, Virtual Threads
ENTRYPOINT ["java", \
    "-XX:+UseZGC", \
    "-XX:+ZGenerational", \
    "-XX:MaxRAMPercentage=75.0", \
    "-XX:+UseContainerSupport", \
    "-Djava.security.egd=file:/dev/./urandom", \
    "org.springframework.boot.loader.launch.JarLauncher"]
