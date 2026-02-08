<?php
/** @var array $user */
/** @var array $contracts */
/** @var array $reviews */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario - Brixo</title>
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
                        <h1 class="h2 fw-bold mb-1">Hola, <?= esc($user['nombre']) ?> 👋</h1>
                        <p class="mb-0 opacity-75">Bienvenido a tu panel de control</p>
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
                        <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($solicitudes ?? []) ?></h3>
                            <span class="text-muted small">Solicitudes Activas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon green"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($contracts ?? []) ?></h3>
                            <span class="text-muted small">Contratos Firmados</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                        <div>
                            <h3 class="h5 fw-bold mb-0"><?= count($reviews ?? []) ?></h3>
                            <span class="text-muted small">Reseñas Dadas</span>
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
                    <!-- Solicitudes Section -->
                    <div class="card card-dashboard mb-4" style="overflow: visible;">
                        <div
                            class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h2 class="h5 fw-bold mb-0 text-dark">Mis Solicitudes</h2>
                            <a href="/solicitud/nueva" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                <i class="fas fa-plus me-1"></i> Nueva Solicitud
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($solicitudes)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($solicitudes as $s): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="me-3">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <h6 class="fw-bold mb-0 text-dark"><?= esc($s['titulo']) ?></h6>
                                                        <span class="badge badge-soft-info"><?= esc($s['estado']) ?></span>
                                                    </div>
                                                    <p class="text-muted small mb-2 text-truncate" style="max-width: 400px;">
                                                        <?= esc($s['descripcion']) ?>
                                                    </p>
                                                    <div class="d-flex align-items-center gap-3 text-secondary small">
                                                        <span><i class="far fa-calendar me-1"></i>
                                                            <?= date('d M, Y', strtotime($s['creado_en'])) ?></span>
                                                        <span class="fw-semibold text-dark">
                                                            <i class="fas fa-tag me-1 text-muted"></i>
                                                            $<?= esc(number_format((float) $s['presupuesto'], 0, ',', '.')) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm rounded-circle" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                                        <li><a class="dropdown-item"
                                                                href="/solicitud/editar/<?= $s['id_solicitud'] ?>"><i
                                                                    class="fas fa-edit me-2 text-primary"></i>Editar</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item text-danger"
                                                                href="/solicitud/eliminar/<?= $s['id_solicitud'] ?>"
                                                                onclick="return confirm('¿Estás seguro?');"><i
                                                                    class="fas fa-trash-alt me-2"></i>Eliminar</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-clipboard"></i></div>
                                    <h5 class="fw-bold text-dark">Sin solicitudes activas</h5>
                                    <p class="text-muted mb-3">Publica tu primera solicitud para encontrar profesionales.
                                    </p>
                                    <a href="/solicitud/nueva" class="btn btn-outline-primary rounded-pill">Crear
                                        Solicitud</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contratos Section -->
                    <div class="card card-dashboard">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h2 class="h5 fw-bold mb-0 text-dark">Mis Contratos</h2>
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
                                                        <i class="fas fa-user-hard-hat me-1"></i> <?= esc($c['contratista']) ?>
                                                    </div>
                                                    <span class="badge badge-soft-primary"><?= esc($c['estado']) ?></span>
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
                                    <h5 class="fw-bold text-dark">No tienes contratos</h5>
                                    <p class="text-muted">Cuando aceptes una propuesta, aparecerá aquí.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card card-dashboard">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h2 class="h5 fw-bold mb-0 text-dark">Mis Reseñas</h2>
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
                                            <small class="text-muted">Para: <?= esc($r['contratista']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state py-4">
                                    <div class="empty-state-icon fs-1 mb-2"><i class="far fa-comment-dots"></i></div>
                                    <p class="text-muted small mb-0">Aún no has escrito reseñas.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?= view('partials/footer') ?>
</body>

</html>