<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialidades - Brixo</title>
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
            padding: var(--spacing-2xl) 0;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-2xl);
            margin-bottom: var(--spacing-2xl);
            position: relative;
            overflow: hidden;
        }

        .page-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.6;
        }

        .category-card {
            background: white;
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform var(--transition-base), box-shadow var(--transition-base);
            margin-bottom: var(--spacing-xl);
        }

        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .category-header {
            background: linear-gradient(135deg, var(--neutral-100) 0%, var(--neutral-50) 100%);
            padding: var(--spacing-lg);
            border-bottom: 2px solid var(--primary-color);
        }

        .category-icon {
            width: 56px;
            height: 56px;
            background: var(--primary-color);
            color: white;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-2xl);
            flex-shrink: 0;
        }

        .service-item {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--neutral-100);
            transition: background-color var(--transition-base);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            text-decoration: none;
            color: inherit;
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-item:hover {
            background-color: var(--neutral-50);
        }

        .service-icon {
            width: 48px;
            height: 48px;
            background: var(--neutral-100);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: var(--font-size-xl);
            flex-shrink: 0;
        }

        .service-price {
            font-size: var(--font-size-lg);
            font-weight: 700;
            color: var(--primary-color);
        }

        .badge-popular {
            background-color: var(--warning-color);
            color: white;
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-full);
            font-size: var(--font-size-xs);
            font-weight: 600;
        }

        .stats-section {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--spacing-2xl);
        }

        .stat-item {
            text-align: center;
            padding: var(--spacing-md);
        }

        .stat-number {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            color: var(--primary-color);
            line-height: var(--line-height-tight);
        }

        .stat-label {
            font-size: var(--font-size-sm);
            color: var(--text-color);
            margin-top: var(--spacing-xs);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-2xl);
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .view-all-btn {
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: var(--radius-md);
            font-size: var(--font-size-sm);
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-base);
        }

        @media (max-width: 768px) {
            .page-header {
                padding: var(--spacing-lg);
            }

            .category-icon {
                width: 48px;
                height: 48px;
                font-size: var(--font-size-xl);
            }

            .stat-number {
                font-size: var(--font-size-2xl);
            }
        }
    </style>
</head>

<body>
    <?= view('partials/navbar') ?>

    <main>
        <div class="container">
            <!-- Page Header -->
            <div class="page-header position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">Especialidades Profesionales</h1>
                        <p class="fs-5 mb-0 opacity-90">Encuentra expertos certificados en cada área. Más de 100 servicios profesionales a tu disposición.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="/map" class="btn btn-light btn-lg rounded-pill px-4">
                            <i class="fas fa-map-marked-alt me-2"></i>Ver en Mapa
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-briefcase me-2"></i><?= count($especialidades ?? []) ?>
                            </div>
                            <div class="stat-label">Especialidades</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-tools me-2"></i>100+
                            </div>
                            <div class="stat-label">Servicios</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-users me-2"></i>500+
                            </div>
                            <div class="stat-label">Profesionales</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">
                                <i class="fas fa-star me-2"></i>4.8
                            </div>
                            <div class="stat-label">Calificación</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories and Services -->
            <?php if (!empty($especialidades)): ?>
                <?php foreach ($especialidades as $esp): ?>
                    <div class="category-card">
                        <div class="category-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="category-icon">
                                        <i class="fas fa-<?= getCategoryIcon($esp['categoria']['nombre']) ?>"></i>
                                    </div>
                                    <div>
                                        <h2 class="h3 mb-1 fw-bold"><?= esc($esp['categoria']['nombre']) ?></h2>
                                        <p class="text-muted mb-0"><?= esc($esp['categoria']['descripcion'] ?? 'Servicios profesionales especializados') ?></p>
                                    </div>
                                </div>
                                <a href="/especialidades/categoria/<?= $esp['categoria']['id_categoria'] ?>" 
                                   class="view-all-btn btn btn-outline-primary d-none d-md-inline-flex align-items-center">
                                    Ver todos <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="services-list">
                            <?php if (!empty($esp['servicios'])): ?>
                                <?php foreach ($esp['servicios'] as $index => $servicio): ?>
                                    <a href="/map?servicio=<?= $servicio['id_servicio'] ?>" class="service-item">
                                        <div class="service-icon">
                                            <i class="fas fa-<?= getServiceIcon($servicio['nombre']) ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h3 class="h6 mb-0 fw-semibold"><?= esc($servicio['nombre']) ?></h3>
                                                <?php if ($index === 0): ?>
                                                    <span class="badge-popular">Popular</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-muted small mb-0"><?= esc(substr($servicio['descripcion'] ?? 'Servicio profesional de calidad', 0, 80)) ?>...</p>
                                        </div>
                                        <div class="text-end">
                                            <div class="service-price">$<?= number_format($servicio['precio_hora'] ?? 50, 0) ?></div>
                                            <small class="text-muted">por hora</small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-info-circle mb-2"></i>
                                    <p class="mb-0">No hay servicios disponibles en esta categoría</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-3 bg-light text-center d-md-none">
                            <a href="/especialidades/categoria/<?= $esp['categoria']['id_categoria'] ?>" 
                               class="btn btn-outline-primary btn-sm w-100">
                                Ver todos los servicios <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay especialidades disponibles en este momento.
                </div>
            <?php endif; ?>

            <!-- CTA Section -->
            <div class="cta-section">
                <h2 class="h3 fw-bold mb-3">¿Eres un profesional?</h2>
                <p class="mb-4 opacity-90">Únete a nuestra plataforma y conecta con cientos de clientes que necesitan tus servicios.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <?php if (empty($user)): ?>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Registrarme
                        </a>
                    <?php endif; ?>
                    <a href="/map" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-search me-2"></i>Buscar Servicios
                    </a>
                </div>
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
