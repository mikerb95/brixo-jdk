<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tablón de Tareas - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="bg-light">
    <?= view('partials/navbar') ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">📋 Tablón de Tareas Disponibles</h2>
                <p class="text-muted mb-0 mt-2">Aquí encontrarás solicitudes de clientes que buscan profesionales como
                    tú.</p>
            </div>
            <a href="/reportes/solicitudes-xlsx" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Descargar Reporte
            </a>
        </div>

        <?php if (empty($solicitudes)): ?>
            <div class="alert alert-info text-center p-5">
                <h4>No hay tareas abiertas en este momento</h4>
                <p>Vuelve más tarde para encontrar nuevas oportunidades.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($solicitudes as $solicitud): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-success rounded-pill">Abierta</span>
                                    <small class="text-muted"><?= date('d M Y', strtotime($solicitud['creado_en'])) ?></small>
                                </div>
                                <h5 class="card-title fw-bold"><?= esc($solicitud['titulo']) ?></h5>
                                <p class="card-text text-secondary text-truncate-3"><?= esc($solicitud['descripcion']) ?></p>

                                <div class="mb-3">
                                    <small class="d-block text-muted"><i class="fas fa-map-marker-alt me-1"></i>
                                        <?= esc($solicitud['ubicacion'] ?: 'Ubicación no especificada') ?></small>
                                    <small class="d-block text-muted"><i class="fas fa-user me-1"></i>
                                        <?= esc($solicitud['nombre_cliente']) ?></small>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="fw-bold text-primary fs-5">
                                        <?= $solicitud['presupuesto'] > 0 ? '$' . number_format($solicitud['presupuesto'], 0) : 'A convenir' ?>
                                    </span>
                                    <!-- Aquí podrías agregar lógica para "Aplicar" o "Contactar" -->
                                    <button class="btn btn-outline-primary btn-sm rounded-pill">Ver Detalles</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?= view('partials/footer') ?>
</body>

</html>