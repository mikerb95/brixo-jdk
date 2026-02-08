# Sistema de Espaciado - Brixo Design System

## Introducción

El sistema de espaciado proporciona una escala consistente de valores para márgenes, padding, gaps y otros espacios en la aplicación. Está basado en una escala de múltiplos de **4px**, lo que garantiza coherencia visual y facilita el diseño responsivo.

## Escala de Espaciado

| Variable        | Valor | Uso Recomendado                                     |
| --------------- | ----- | --------------------------------------------------- |
| `--spacing-xs`  | 4px   | Detalles mínimos, separaciones muy pequeñas         |
| `--spacing-sm`  | 8px   | Separaciones internas pequeñas, padding de badges   |
| `--spacing-md`  | 16px  | Separación estándar entre elementos relacionados    |
| `--spacing-lg`  | 24px  | Separación entre secciones, padding de contenedores |
| `--spacing-xl`  | 32px  | Márgenes principales, padding grande                |
| `--spacing-2xl` | 48px  | Separaciones destacadas, secciones importantes      |

## Variables Semánticas

Para casos de uso comunes, existen aliases semánticos:

```css
--spacing-component-gap: var(
  --spacing-md
); /* Gap entre componentes relacionados */
--spacing-section-gap: var(--spacing-xl); /* Gap entre secciones principales */
--spacing-container-padding: var(
  --spacing-lg
); /* Padding interno de contenedores */
--spacing-card-padding: var(--spacing-md); /* Padding interno de tarjetas */
--spacing-button-padding-x: var(
  --spacing-md
); /* Padding horizontal de botones */
--spacing-button-padding-y: var(--spacing-sm); /* Padding vertical de botones */
--spacing-form-gap: var(--spacing-md); /* Gap entre campos de formulario */
```

## Uso en CSS

### Usando variables directamente

```css
.mi-componente {
  padding: var(--spacing-lg);
  margin-bottom: var(--spacing-xl);
  gap: var(--spacing-md);
}
```

### Usando clases utilitarias

El sistema incluye clases utilitarias predefinidas:

#### Márgenes

```html
<!-- Todos los lados -->
<div class="m-md">Margen medio en todos los lados</div>

<!-- Lados individuales -->
<div class="mt-lg">Margen superior grande</div>
<div class="mb-xl">Margen inferior extra grande</div>
<div class="ml-sm">Margen izquierdo pequeño</div>
<div class="mr-md">Margen derecho medio</div>

<!-- Ejes -->
<div class="mx-lg">Margen horizontal grande</div>
<div class="my-xl">Margen vertical extra grande</div>
```

#### Padding

```html
<!-- Todos los lados -->
<div class="p-lg">Padding grande en todos los lados</div>

<!-- Lados individuales -->
<div class="pt-md">Padding superior medio</div>
<div class="pb-xl">Padding inferior extra grande</div>
<div class="pl-sm">Padding izquierdo pequeño</div>
<div class="pr-lg">Padding derecho grande</div>

<!-- Ejes -->
<div class="px-md">Padding horizontal medio</div>
<div class="py-lg">Padding vertical grande</div>
```

#### Gap (para Flexbox y Grid)

```html
<div class="d-flex gap-md">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

## Otros Sistemas de Diseño

### Border Radius

```css
--radius-sm: 4px; /* Pequeño - inputs, badges */
--radius-md: 8px; /* Medio - botones, tags */
--radius-lg: 12px; /* Grande - tarjetas pequeñas */
--radius-xl: 16px; /* Extra grande - tarjetas principales */
--radius-2xl: 24px; /* 2X Extra grande - contenedores destacados */
--radius-full: 9999px; /* Circular - avatares, pills */
```

### Shadows

```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
--shadow-md: 0 2px 12px rgba(0, 0, 0, 0.04);
--shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.08);
--shadow-xl: 0 12px 30px rgba(0, 0, 0, 0.1);
```

### Tipografía

```css
/* Tamaños de fuente */
--font-size-xs: 0.75rem; /* 12px */
--font-size-sm: 0.875rem; /* 14px */
--font-size-base: 1rem; /* 16px */
--font-size-lg: 1.125rem; /* 18px */
--font-size-xl: 1.25rem; /* 20px */
--font-size-2xl: 1.5rem; /* 24px */
--font-size-3xl: 1.875rem; /* 30px */
--font-size-4xl: 2.25rem; /* 36px */

/* Line heights */
--line-height-tight: 1.25;
--line-height-normal: 1.5;
--line-height-relaxed: 1.75;
```

### Transiciones

```css
--transition-fast: 150ms ease;
--transition-base: 200ms ease;
--transition-slow: 300ms ease;
```

## Ejemplos de Uso

### Tarjeta de Contenido

```css
.content-card {
  padding: var(--spacing-lg);
  margin-bottom: var(--spacing-xl);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-md);
  transition: box-shadow var(--transition-base);
}

.content-card:hover {
  box-shadow: var(--shadow-lg);
}
```

### Formulario

```css
.form-container {
  padding: var(--spacing-xl);
  display: flex;
  flex-direction: column;
  gap: var(--spacing-form-gap);
}

.form-input {
  padding: var(--spacing-sm) var(--spacing-md);
  border-radius: var(--radius-md);
}

.form-button {
  padding: var(--spacing-button-padding-y) var(--spacing-button-padding-x);
  border-radius: var(--radius-md);
}
```

### Grid de Tarjetas

```css
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--spacing-lg);
  padding: var(--spacing-section-gap);
}
```

## Mejores Prácticas

1. **Usa siempre las variables del sistema** en lugar de valores hardcoded
2. **Prefiere las variables semánticas** cuando estén disponibles (ej: `--spacing-card-padding` en lugar de `--spacing-md`)
3. **Mantén la consistencia** usando la misma escala en toda la aplicación
4. **No inventes nuevos valores** - si necesitas un espaciado que no existe, considera si puedes usar uno existente o propón agregarlo al sistema
5. **Usa clases utilitarias** para prototipos rápidos y ajustes pequeños
6. **Usa variables CSS** para componentes personalizados y estilos complejos

## Integración

Para usar el sistema de espaciado, asegúrate de importar el archivo CSS principal:

```html
<link rel="stylesheet" href="/css/design-system.css" />
```

O importarlo en tu archivo CSS:

```css
@import url("./design-system.css");
```

El sistema está integrado automáticamente en:

- [brixo.css](../public/css/brixo.css)
- [styles.css](../public/css/styles.css)
- [dashboard.css](../public/css/dashboard.css)
