# CI/CD con Docker, GitHub Actions y Render

Guion de la presentación y diapositivas listas para copiar a PowerPoint (.pptx).

Slide 1 — Título

- Título: CI/CD con Docker, GitHub Actions y Render
- Subtítulo: Pipeline automático para el proyecto `BrixoCI4`
- Speaker notes: Presentar objetivo: mostrar flujo desde push en GitHub hasta app en Render.
- Visual: Logo GitHub + Docker + Render conectados.

Slide 2 — Objetivos de la demo

- Objetivo 1: Validación continua (tests) en cada push
- Objetivo 2: Construcción reproducible con Docker
- Objetivo 3: Despliegue automático en Render (CD)
- Objetivo 4: Trazabilidad y opciones de rollback
- Speaker notes: Explicar por qué cada objetivo importa: calidad, repeatability y tiempo de recuperación.

Slide 3 — Arquitectura (diagrama)

- Texto slide: Flujo: GitHub → GitHub Actions (CI) → Registry (GHCR / Docker Hub) → Render (CD)
- Bullets (breve):
  - GitHub: control de versiones + Actions
  - Actions: ejecutar tests, build y push imagen
  - Registry: almacenar imagen (opcional)
  - Render: hosting y despliegue desde repo o imagen
- Visual: diagrama con 4 cajas y flechas.
- Speaker notes: Mostrar una ejecución real en logs cuando llegue a la parte demo.

Slide 4 — Archivos clave en el repo

- `Dockerfile` (raíz) — build de la imagen
- `.github/workflows/ci-cd.yml` — pipeline CI/CD
- `composer.json`, `phpunit.xml.dist` — dependencias y tests
- `public/` — punto de entrada de la app
- Speaker notes: Indicar ubicación en el repo y que vamos a mostrar cada archivo en el demo.

Slide 5 — Dockerfile (resumen)

- Título: Dockerfile recomendado (multi-stage)
- Bullets:
  - Build stage: instalar dependencias, composer install
  - Runtime stage: copiar artefactos, usuario no-root, exponer puerto (8080)
  - HEALTHCHECK (opcional)
- Speaker notes: Mostrar el Dockerfile (fragmento) en la diapositiva o adjuntarlo en notas.

Slide 6 — Workflow GitHub Actions (resumen)

- Nombre: `CI/CD`
- Triggers: `push` a `master`, `pull_request`
- Jobs (resumen):
  - Checkout, setup-php
  - Composer install
  - Ejecutar tests (`phpunit`)
  - Login en registry y build/push de imagen
  - Trigger deploy en Render (API) o auto-deploy desde Render
- Speaker notes: Explicar por qué hacer tests antes del push de imagen y dónde se colocan los secrets.

Slide 7 — Secrets y permisos (qué configurar)

- GitHub Secrets necesarios:
  - `CR_PAT` o token para GHCR/Docker Hub
  - `RENDER_API_KEY` (si usamos API deploy)
  - `RENDER_SERVICE_ID`
  - `DB_...` (si las pruebas integradas la requieren)
- Permisos mínimos para tokens: `write:packages`, `read:packages` (GHCR)
- Speaker notes: Reforzar no subir claves en el repo.

Slide 8 — Render: opciones de despliegue

- Opción A: Build from Dockerfile (Render construye desde repo)
- Opción B: Deploy from Docker image (Render extrae de GHCR/Docker Hub)
- Recomendación: para control total usar image en registry; para simplicidad usar Auto-Deploy desde Git
- Visual: Captura de pantalla del panel de Render (sugerida)

Slide 9 — Demo: pasos en vivo

- Paso 1: Crear una rama `feature/demo-ci`
- Paso 2: Cambiar un texto pequeño (p. ej. `README.md`) y push
- Paso 3: Mostrar GitHub Actions: tests verdes, build y push
- Paso 4: Mostrar Render: deploy iniciado → logs → URL pública
- Paso 5: Simular rollback: revert commit y push (o usar Manual Deploy en Render)

Speaker notes (comandos para demo local y ver logs):

```powershell
# Build local
docker build -t brixoci4:local .
# Run local
docker run --rm -p 8080:8080 brixoci4:local
# Tests
composer install
vendor/bin/phpunit --configuration phpunit.xml.dist
```

Slide 10 — Badges y status

- Incluir en `README.md`:
  - Badge GitHub Actions: `https://github.com/<owner>/<repo>/actions/workflows/ci-cd.yml/badge.svg`
  - Badge Render: usar el badge que Render provee en la página del servicio
- Speaker notes: Mostrar ejemplo visual de README con badges.

Slide 11 — Rollback y buenas prácticas

- Opciones de rollback:
  - Revertir commit en GitHub → despliegue automático
  - Manual Deploy en Render a commit/imagen anterior
- Buenas prácticas:
  - Tener branch `staging` para pruebas de integración
  - Ejecutar migraciones con cuidado (manual o job con confirmación)
  - Monitorización y alertas (Sentry, logs)

Slide 12 — Resumen y siguientes pasos

- Resumen corto: CI (tests) + Build (Docker) + CD (Render)
- Siguientes pasos sugeridos:
  - Implementar workflow en `.github/workflows/ci-cd.yml`
  - Añadir badges y sección en `README.md`
  - Probar deploy a staging antes de producción

Slide 13 — Recursos y links útiles

- Template workflow: `.github/workflows/ci-cd.yml` (ejemplo disponible)
- Documentación:
  - GitHub Actions: https://docs.github.com/actions
  - Render API: https://render.com/docs/api
  - GHCR: https://docs.github.com/packages

Slide 14 — Preguntas

- Texto: ¿Preguntas? — mostrar contacto y URL del repo

---

Notas de diseño y sugerencias para PowerPoint

- Mantén cada diapositiva simple: máximo 5 bullets
- Usa iconos: GitHub, Docker, Container Registry, Render
- Inserta pantallazos reales del pipeline y del Render dashboard durante la demo
- Si quieres, puedo generar un archivo `.pptx` con estas diapositivas (pregunta si lo deseas).

Archivo generado:

- `docs/CI-CD_Presentation.md` — (guion y notas) — puedes copiar/pegar cada slide en PowerPoint.

¿Quieres que genere también el `.pptx` automáticamente con este contenido? Si sí, indícame si prefieres:

- una versión simple (títulos + bullets), o
- una versión con speaker notes integradas y un diseño básico.
