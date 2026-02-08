<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotización Confirmada - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="bg-light">
    <?= view('partials/navbar') ?>

    <div class="container py-5" style="max-width: 720px;">

        <!-- Éxito -->
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-4" style="width: 100px; height: 100px;">
                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-6 fw-bold mb-2">¡Cotización Confirmada!</h1>
            <p class="text-muted fs-5">Tu solicitud ha sido registrada exitosamente.</p>
            <?php if (!empty($cotizacion['id'])): ?>
                <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">Folio #<?= esc($cotizacion['id']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Resumen -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>Resumen de tu Cotización</h5>
                <?php
                    $nivel = $cotizacion['complejidad'] ?? 'medio';
                    $cls   = ['bajo' => 'bg-success', 'medio' => 'bg-warning text-dark', 'alto' => 'bg-danger'];
                ?>
                <span class="badge <?= $cls[$nivel] ?? 'bg-secondary' ?> rounded-pill px-3 py-2">
                    <?= ucfirst(esc($nivel)) ?>
                </span>
            </div>
            <div class="card-body p-4">

                <!-- Servicio principal -->
                <div class="mb-4 p-3 bg-light rounded-3">
                    <small class="text-muted text-uppercase fw-bold">Servicio Principal</small>
                    <h4 class="fw-bold mb-0 mt-1"><?= esc($cotizacion['servicio_principal']) ?></h4>
                </div>

                <!-- Descripción original -->
                <?php if (!empty($cotizacion['descripcion'])): ?>
                    <div class="mb-4 p-3 border rounded-3">
                        <small class="text-muted text-uppercase fw-bold">Tu solicitud</small>
                        <p class="mb-0 mt-1"><?= esc($cotizacion['descripcion']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Materiales -->
                <?php if (!empty($cotizacion['materiales'])): ?>
                    <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-tools me-2"></i>Materiales</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless mb-0">
                            <thead><tr><th>Material</th><th class="text-end">Cantidad</th></tr></thead>
                            <tbody>
                                <?php foreach ($cotizacion['materiales'] as $mat): ?>
                                    <tr>
                                        <td><i class="fas fa-box text-muted me-2"></i><?= esc($mat['nombre']) ?></td>
                                        <td class="text-end fw-semibold"><?= esc($mat['cantidad_estimada']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Personal -->
                <?php if (!empty($cotizacion['personal'])): ?>
                    <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-users me-2"></i>Personal</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead><tr><th>Rol</th><th class="text-end">Horas Est.</th></tr></thead>
                            <tbody>
                                <?php foreach ($cotizacion['personal'] as $per): ?>
                                    <tr>
                                        <td><i class="fas fa-hard-hat text-muted me-2"></i><?= esc($per['rol']) ?></td>
                                        <td class="text-end fw-semibold"><?= esc($per['horas_estimadas']) ?> hrs</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Solicitud creada -->
        <?php if (!empty($cotizacion['id_solicitud'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-clipboard-check fs-4 me-3"></i>
            <div>
                <strong>Solicitud #<?= esc($cotizacion['id_solicitud']) ?> creada automáticamente.</strong>
                <span class="d-block small text-muted">Puedes verla y editarla desde tu panel en <strong>Mis Solicitudes</strong>.</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Próximos pasos -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-route text-primary me-2"></i>¿Qué sigue?</h5>
            <div class="d-flex align-items-start mb-3">
                <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">1</span>
                <div>
                    <strong>Tu solicitud ya está publicada</strong>
                    <p class="text-muted mb-0 small">Fue agregada a <strong>Mis Solicitudes</strong> y al tablón de tareas para contratistas.</p>
                </div>
            </div>
            <div class="d-flex align-items-start mb-3">
                <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">2</span>
                <div>
                    <strong>Contratistas podrán contactarte</strong>
                    <p class="text-muted mb-0 small">Profesionales verificados en tu zona verán tu solicitud y te escribirán.</p>
                </div>
            </div>
            <div class="d-flex align-items-start">
                <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">3</span>
                <div>
                    <strong>Acepta y agenda</strong>
                    <p class="text-muted mb-0 small">Cuando elijas un contratista, la solicitud se convertirá en un contrato formal.</p>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="d-flex flex-column flex-sm-row gap-3 flex-wrap">
            <a href="/panel" class="btn btn-success btn-lg rounded-pill flex-fill text-center">
                <i class="fas fa-clipboard-list me-2"></i>Ver Mis Solicitudes
            </a>
            <a href="/cotizador" class="btn btn-primary btn-lg rounded-pill flex-fill text-center">
                <i class="fas fa-plus me-2"></i>Nueva Cotización
            </a>
            <a href="/map" class="btn btn-outline-dark btn-lg rounded-pill flex-fill text-center">
                <i class="fas fa-map-marked-alt me-2"></i>Explorar Mapa
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?= view('partials/footer') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
