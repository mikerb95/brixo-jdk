# 🔍 Auditoría de Navbar - Análisis de Impacto en el Proyecto Brixo

## 📊 Resumen Ejecutivo

**Total de páginas afectadas:** 31 vistas
**Páginas con comportamiento especial:** 4 (map.php, mapa.php, index.php, especialidades.php)
**Riesgo de rotura:** MEDIO-ALTO

---

## 🚨 PÁGINAS CRÍTICAS (Requieren atención especial)

### 1. **`map.php`** - ⚠️ CRÍTICO

**Ruta:** `/map`
**Body class:** `map-page`
**Problema principal:** Layout full-screen con sidebar + mapa

#### Estilos activos que dependen de navbar:

```css
body.map-page {
  padding-top: 0 !important;
  overflow: hidden;
}
body.map-page #mapApp {
  height: 100vh;
}
.left-sidebar {
  padding-top: 80px; /* Space for floating navbar overlay */
}
```

#### Arquitectura actual:

- Navbar debe ser **position: absolute** o **fixed** (overlay, no pushea contenido)
- Sidebar izquierdo tiene `padding-top: 80px` para compensar navbar
- Contenido usa `height: 100vh` — navbar NO debe restar altura
- `overflow: hidden` en body

#### ⚠️ Riesgos:

- Si navbar es `position: relative` → rompe layout full-screen
- Si altura navbar ≠ 80px → sidebar cortará el navbar o dejará espacio en blanco
- Si navbar pushea contenido → se perderá parte del mapa

---

### 2. **`mapa.php`** - ⚠️ CRÍTICO (página alternativa de mapa)

**Ruta:** `/mapa` (legacy)
**Problema:** Similar a map.php

#### Estilos activos:

```css
.navbar {
  z-index: 1030;
}
.main-container {
  height: calc(100vh - 80px);
}
```

#### ⚠️ Riesgos:

- Calcula altura asumiendo navbar de 80px
- Si navbar cambia de altura → layout roto
- Si z-index navbar < 1030 → elementos pueden cubrir navbar

---

### 3. **`index.php`** (Home) - ⚠️ MODERADO

**Body class:** `home-page`
**Problema:** Hero full-bleed de 85vh

#### Estilos activos:

```css
body.home-page {
  padding-top: 0;
}
.brixo-hero {
  position: relative;
  min-height: 85vh;
}
.brixo-hero__content {
  position: relative;
  z-index: 2;
}
```

#### Arquitectura actual:

- Hero sin padding-top (navbar debe overlay o ser parte del diseño)
- Hero tiene elementos con z-index específicos
- Navbar anterior era overlay transparente sobre hero

#### ⚠️ Riesgos:

- Si navbar es opaca + relative → tapará parte del hero
- Si navbar z-index > hero content → tapará alerts/mensajes
- Navbar debe ser transparente o overlay para no cortar hero

---

### 4. **`especialidades.php`** + **`categoria_detalle.php`** - ⚠️ MODERADO

**Rutas:** `/especialidades`, `/especialidades/categoria/:id`

#### Estilos activos:

```css
main {
  margin-top: var(--navbar-offset); /* 84px */
}
```

#### ⚠️ Riesgos:

- Asume navbar fixed de 84px
- Si navbar es relative → doble espacio (margin + navbar height)
- Si navbar height ≠ 84px → espacio incorrecto

---

## 📋 PÁGINAS ESTÁNDAR (Menor riesgo)

### Con body.floating-offset (teóricamente preparadas)

Estas páginas están preparadas para navbar fija usando clase `floating-offset`:

```css
body.floating-offset main {
  padding-top: calc(var(--navbar-offset) + var(--spacing-lg));
}
```

**Páginas:**

- `panel_cliente.php`
- `panel_contratista.php`
- `perfil_editar.php`
- `mensajes/index.php`
- `solicitudes.php`
- `solicitud/*.php`

⚠️ **Problema:** Actualmente NO se está aplicando la clase `floating-offset` en ninguna página

---

### Sin compensación de altura (Asumen navbar relative)

**Páginas:**

- `productos.php`
- `cotizador.php`
- `cotizacion_exito.php`
- `servicios.php`
- `servicio_detalle.php`
- `perfil.php`
- `auth/login.php`
- `info/*.php` (10 páginas)
- `errors/*.php`

#### Layout típico:

```html
<body class="d-flex flex-column min-vh-100">
  <?= view('partials/navbar') ?>
  <main class="flex-grow-1">
    <!-- contenido -->
  </main>
</body>
```

✅ **Estas funcionarán bien si navbar es `position: relative`**

---

## 🎯 VARIABLES CRÍTICAS DEL SISTEMA

### En `design-system.css`:

```css
--navbar-offset: 84px;
```

### En `brixo.css`:

```css
body {
  padding-top: 0; /* Global: sin compensación por defecto */
}

body.home-page {
  padding-top: 0; /* Hero full-bleed */
}

body.map-page {
  padding-top: 0 !important; /* Layout full-screen */
  overflow: hidden;
}
```

---

## 🔧 RESTRICCIONES TÉCNICAS

### Z-index establecidos que la navbar debe respetar:

- `z-index: 1030` - mapa.php navbar actual
- `z-index: 1020` - mapa.php sidebar
- `z-index: 1050` - navbar anterior (brixo.css)
- `z-index: 1100` - dropdown anterior

### Alturas establecidas:

- **84px** - `--navbar-offset` en design-system
- **80px** - Compensación en map.php y mapa.php

---

## ⚡ RECOMENDACIONES PARA NUEVA NAVBAR

### Opción 1: Navbar Híbrida (RECOMENDADA)

```css
/* Por defecto: relative (pushea contenido) */
.navbar {
  position: relative;
  height: 84px; /* Mantener consistencia */
}

/* En páginas especiales: overlay */
body.map-page .navbar,
body.home-page .navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1050;
}
```

#### Ventajas:

- ✅ Funciona en 27/31 páginas sin cambios
- ✅ Map y home mantienen su diseño actual
- ✅ No requiere modificar `--navbar-offset`

#### Desventajas:

- ⚠️ Requiere detección de ruta (PHP o JS)

---

### Opción 2: Navbar Fixed Global

```css
.navbar {
  position: fixed;
  top: 0;
  height: 84px;
  z-index: 1050;
}

/* Todas las páginas necesitan compensación */
body:not(.map-page):not(.home-page) {
  padding-top: 84px;
}
```

#### Ventajas:

- ✅ Comportamiento consistente
- ✅ Navbar siempre visible

#### Desventajas:

- ❌ Requiere modificar 27 vistas para agregar padding/margin
- ❌ Hero de home necesita rediseño
- ⚠️ Map.php sidebar necesita ajuste de padding

---

### Opción 3: Navbar Relative Pura

```css
.navbar {
  position: relative;
  height: 84px;
}

/* Map y home necesitan overlay manual */
body.map-page .navbar {
  position: absolute;
  z-index: 1050;
}
```

#### Ventajas:

- ✅ Más simple, menos CSS
- ✅ Funciona out-of-the-box en 27 vistas

#### Desventajas:

- ❌ Map layout necesita ajuste (sidebar padding-top ya no sirve)
- ❌ Home hero se corta por navbar opaca

---

## 🛠️ PLAN DE ACCIÓN PROPUESTO

### Fase 1: Construcción segura

1. Crear navbar con height **exacto de 84px**
2. Usar **position: relative por defecto**
3. Agregar detección de rutas especiales en navbar.php:
   ```php
   $isMapPage = (strpos($_SERVER['REQUEST_URI'], '/map') === 0);
   $isHomePage = ($_SERVER['REQUEST_URI'] === '/');
   ```

### Fase 2: CSS condicional

```css
/* Default */
.navbar {
  position: relative;
  height: 84px;
}

/* Map overlay */
body.map-page .navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1050;
}

/* Home transparente */
body.home-page .navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1050;
  background: transparent;
}
```

### Fase 3: Testing obligatorio en:

1. ✅ `/` (home hero)
2. ✅ `/map` (sidebar + mapa)
3. ✅ `/mapa` (layout alternativo)
4. ✅ `/especialidades` (margin-top)
5. ✅ `/panel` (dashboard)
6. ✅ `/mensajes` (lista)
7. ✅ `/info/ayuda` (página estándar)

---

## 📌 CONCLUSIÓN

**La nueva navbar DEBE:**

1. ✅ Tener exactamente **84px de altura** (o actualizar `--navbar-offset`)
2. ✅ Ser **position: relative** por defecto
3. ✅ Tener modo **overlay (fixed)** para `/map` y `/` (home)
4. ✅ Respetar **z-index >= 1050**
5. ✅ NO usar `padding-top` en body global

**Si cambia la altura:**

- Actualizar `--navbar-offset` en design-system.css
- Actualizar `padding-top: 80px` en map.php sidebar
- Actualizar `calc(100vh - 80px)` en mapa.php

**Páginas que romperán si no se cumple:**

- ❌ map.php (layout full-screen)
- ❌ index.php (hero full-bleed)
- ⚠️ especialidades.php (espacio doble)
- ⚠️ mapa.php (altura incorrecta)
