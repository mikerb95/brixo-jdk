<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($categoria['nombre'] ?? 'Categoría') ?> - Especialidades - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="/css/design-system.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <style>
        body {
            background-color: var(--neutral-50);
        }

        main {
            margin-top: var(--navbar-offset);
            padding: var(--spacing-xl) 0 var(--spacing-2xl);
        }

        .breadcrumb {
            background: transparent;
            padding: var(--spacing-md) 0;
            margin-bottom: var(--spacing-lg);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-hover);
        }

        .category-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-2xl);
            margin-bottom: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
        }

        .category-icon-large {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-lg);
        }

        .service-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all var(--transition-base);
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .service-card-header {
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, var(--neutral-50) 0%, white 100%);
            border-bottom: 2px solid var(--primary-color);
        }

        .service-icon-circle {
            width: 64px;
            height: 64px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-2xl);
            margin-bottom: var(--spacing-md);
        }

        .service-card-body {
            padding: var(--spacing-lg);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .service-price-tag {
            background: var(--primary-color);
            color: white;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: var(--font-size-lg);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
        }

        .service-features {
            list-style: none;
            padding: 0;
            margin: var(--spacing-md) 0 0;
        }

        .service-features li {
            padding: var(--spacing-xs) 0;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: var(--text-color);
            font-size: var(--font-size-sm);
        }

        .service-features li i {
            color: var(--success-color);
            font-size: var(--font-size-xs);
        }

        .cta-button {
            margin-top: auto;
            padding: var(--spacing-sm) var(--spacing-md);
            background: var(--primary-color);
            color: white;
            border-radius: var(--radius-md);
            text-align: center;
            font-weight: 600;
            transition: all var(--transition-base);
            border: none;
            text-decoration: none;
            display: block;
        }

        .cta-button:hover {
            background: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-2xl);
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--neutral-100);
            margin-bottom: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .service-grid {
                grid-template-columns: 1fr;
            }

            .category-hero {
                padding: var(--spacing-lg);
            }

            .category-icon-large {
                width: 64px;
                height: 64px;
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <?= view('partials/navbar') ?>

    <main>
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/especialidades">Especialidades</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($categoria['nombre'] ?? 'Categoría') ?></li>
                </ol>
            </nav>

            <!-- Category Hero -->
            <div class="category-hero">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="category-icon-large">
                            <i class="fas fa-<?= getCategoryIcon($categoria['nombre']) ?>"></i>
                        </div>
                        <h1 class="display-4 fw-bold mb-3"><?= esc($categoria['nombre']) ?></h1>
                        <p class="fs-5 mb-0 opacity-90">
                            <?= esc($categoria['descripcion'] ?? 'Encuentra profesionales especializados en ' . $categoria['nombre']) ?>
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <div class="d-flex flex-column gap-2">
                            <div class="fs-3 fw-bold"><?= count($servicios ?? []) ?></div>
                            <div class="opacity-90">Servicios disponibles</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Grid -->
            <?php if (!empty($servicios)): ?>
                <div class="service-grid">
                    <?php foreach ($servicios as $servicio): ?>
                        <article class="service-card">
                            <div class="service-card-header">
                                <div class="service-icon-circle">
                                    <i class="fas fa-<?= getServiceIcon($servicio['nombre']) ?>"></i>
                                </div>
                                <h2 class="h5 fw-bold mb-2"><?= esc($servicio['nombre']) ?></h2>
                                <div class="service-price-tag">
                                    <i class="fas fa-tag"></i>
                                    $<?= number_format($servicio['precio_hora'] ?? 50, 0) ?>/hora
                                </div>
                            </div>
                            
                            <div class="service-card-body">
                                <p class="text-muted mb-3">
                                    <?= esc($servicio['descripcion'] ?? 'Servicio profesional de alta calidad realizado por expertos certificados.') ?>
                                </p>
                                
                                <ul class="service-features">
                                    <li><i class="fas fa-check-circle"></i> Profesionales verificados</li>
                                    <li><i class="fas fa-check-circle"></i> Garantía de servicio</li>
                                    <li><i class="fas fa-check-circle"></i> Precio por hora</li>
                                    <li><i class="fas fa-check-circle"></i> Disponibilidad inmediata</li>
                                </ul>
                                
                                <a href="/map?servicio=<?= $servicio['id_servicio'] ?>" class="cta-button mt-4">
                                    <i class="fas fa-search me-2"></i>Ver profesionales
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-toolbox"></i>
                    </div>
                    <h3 class="h4 fw-bold mb-3">No hay servicios disponibles</h3>
                    <p class="text-muted mb-4">
                        En este momento no tenemos servicios registrados en esta categoría.
                    </p>
                    <a href="/especialidades" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Ver otras especialidades
                    </a>
                </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="text-center mt-5">
                <a href="/especialidades" class="btn btn-outline-primary btn-lg px-4">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Especialidades
                </a>
            </div>
        </div>
    </main>

    <?= view('partials/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Helper function para iconos de categorías
function getCategoryIcon($nombre) {
    $iconMap = [
        'Construcción' => 'hard-hat',
        'Obra' => 'hard-hat',
        'Carpintería' => 'hammer',
        'Plomería' => 'wrench',
        'Electricidad' => 'bolt',
        'Pintura' => 'paint-roller',
        'Jardinería' => 'seedling',
        'Limpieza' => 'broom',
        'Refrigeración' => 'snowflake',
        'Cerrajería' => 'key',
        'Mudanzas' => 'truck-moving',
        'Informática' => 'laptop',
    ];
    
    foreach ($iconMap as $key => $icon) {
        if (stripos($nombre, $key) !== false) {
            return $icon;
        }
    }
    
    return 'tools';
}

// Helper function para iconos de servicios
function getServiceIcon($nombre) {
    $iconMap = [
        'albañil' => 'trowel-bricks',
        'carpintero' => 'hammer',
        'plomero' => 'wrench',
        'electricista' => 'bolt',
        'pintor' => 'paint-roller',
        'jardinero' => 'leaf',
        'limpieza' => 'spray-can',
        'refrigeración' => 'fan',
        'cerrajero' => 'key',
        'mudanza' => 'dolly',
        'técnico' => 'screwdriver-wrench',
        'instalación' => 'screwdriver',
        'reparación' => 'wrench',
        'mantenimiento' => 'gears',
    ];
    
    $nombreLower = strtolower($nombre);
    foreach ($iconMap as $key => $icon) {
        if (stripos($nombreLower, $key) !== false) {
            return $icon;
        }
    }
    
    return 'toolbox';
}
?>
