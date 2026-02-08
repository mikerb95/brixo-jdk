<?php
/** @var array $user */
/** @var array $contracts */
/** @var array $reviews */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Contratista - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container my-5" style="max-width:1200px;">

            <!-- Dashboard Header -->
            <div class="dashboard-header d-flex justify-content-between align-items-center shadow-sm">
                <div class="position-relative z-1 d-flex align-items-center gap-3">
                    <?php if (!empty($user['foto_perfil'])): ?>
                        <img src="<?= strpos($user['foto_perfil'], 'http') === 0 ? esc($user['foto_perfil']) : '/images/profiles/' . esc($user['foto_perfil']) ?>"
                            alt="Perfil" class="rounded-circle shadow-sm border border-2 border-white"
                            style="width: 64px; height: 64px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center shadow-sm border border-2 border-white"
                            style="width: 64px; height: 64px;">
                            <i class="fas fa-user fa-2x text-secondary"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="h2 fw-bold mb-1">Hola, <?= esc($user['nombre']) ?> 🛠️</h1>
                        <p class="mb-0 opacity-75">Bienvenido a tu panel de profesional</p>
                    </div>
                </div>
                <div class="position-relative z-1 d-none d-md-block">
                    <a href="/mensajes" class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm me-2">
                        <i class="fas fa-envelope me-2"></i>Mensajes
                    </a>
                    <a href="/perfil/editar"
                        class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm me-2">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </a>
                    <a href="/" class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm">
                        <i class="fas fa-home me-2"></i>Ir al Inicio
                    </a>
                </div>
            </div>

            <!-- Quick Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon blue"><i class="fas fa-search-dollar"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($solicitudesDisponibles ?? []) ?></h3>
                            <span class="text-muted small">Oportunidades Nuevas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon green"><i class="fas fa-handshake"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($contracts ?? []) ?></h3>
                            <span class="text-muted small">Contratos Activos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($reviews ?? []) ?></h3>
                            <span class="text-muted small">Reseñas Recibidas</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= esc($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Sección de Oportunidades / Solicitudes Recientes -->
                    <div class="card card-dashboard mb-4">
                        <div
                            class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h2 class="h5 fw-bold mb-0 text-dark">Oportunidades Recientes</h2>
                            <a href="/tablon-tareas" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                Ver todas <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="card-body p-0">
                            <?php if (!empty($solicitudesDisponibles)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($solicitudesDisponibles as $s): ?>
                                        <a href="/tablon-tareas" class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-primary"><?= esc($s['titulo']) ?></h6>
                                                    <div class="small text-muted mb-1">
                                                        <i class="fas fa-user me-1"></i> <?= esc($s['nombre_cliente']) ?> &bull;
                                                        <i class="fas fa-map-marker-alt me-1 ms-2"></i>
                                                        <?= esc($s['ubicacion']) ?>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge badge-soft-success rounded-pill mb-1">
                                                        $<?= esc(number_format((float) $s['presupuesto'], 0, ',', '.')) ?>
                                                    </span>
                                                    <div class="small text-muted">
                                                        <?= date('d M', strtotime($s['creado_en'])) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                                    <h5 class="fw-bold text-dark">No hay solicitudes nuevas</h5>
                                    <p class="text-muted">Vuelve más tarde para ver nuevas oportunidades de trabajo.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contratos Activos -->
                    <div class="card card-dashboard">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h2 class="h5 fw-bold mb-0 text-dark">Contratos Activos</h2>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($contracts)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($contracts as $c): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark"><?= esc($c['detalle']) ?></h6>
                                                    <div class="small text-muted mb-1">
                                                        <i class="fas fa-user me-1"></i> Cliente: <?= esc($c['cliente']) ?>
                                                    </div>
                                                    <span class="badge badge-soft-dark"><?= esc($c['estado']) ?></span>
                                                </div>
                                                <div class="text-end">
                                                    <div class="small text-muted">Inicio:
                                                        <?= date('d M', strtotime($c['fecha_inicio'])) ?>
                                                    </div>
                                                    <div class="fw-bold text-dark mt-1">
                                                        $<?= esc(number_format((float) $c['costo_total'], 0, ',', '.')) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-file-contract"></i></div>
                                    <h5 class="fw-bold text-dark">No tienes contratos activos</h5>
                                    <p class="text-muted">Postúlate a trabajos para conseguir contratos.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card card-dashboard">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h2 class="h5 fw-bold mb-0 text-dark">Reseñas Recibidas</h2>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($reviews)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($reviews as $r): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div class="text-warning small">
                                                    <?php for ($i = 0; $i < $r['calificacion']; $i++)
                                                        echo '<i class="fas fa-star"></i>'; ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?= date('d M', strtotime($r['fecha_resena'])) ?></small>
                                            </div>
                                            <p class="mb-1 text-dark small">"<?= esc($r['comentario']) ?>"</p>
                                            <small class="text-muted">De: <?= esc($r['cliente']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon fs-1 mb-2"><i class="far fa-comment-dots"></i></div>
                                    <p class="text-muted small mb-0">Aún no has recibido reseñas.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Perfil Rápido y Subir Imagen -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <?php $fotoUrl = !empty($user['foto_perfil']) ? '/images/profiles/' . $user['foto_perfil'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre']) . '&background=random'; ?>
                    <img src="<?= esc($fotoUrl) ?>" class="rounded-circle mb-3" width="120" height="120" alt="Avatar">
                    <h5 class="fw-bold mb-1"><?= esc($user['nombre']) ?></h5>
                    <p class="text-muted small mb-3">Panel Profesional</p>

                    <form action="/perfil/subir-imagen" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <input class="form-control form-control-sm" type="file" name="imagen" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Subir imagen</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?= view('partials/footer') ?>
</body>

</html>