<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Solicitudes de clientes - Brixo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Solicitudes recibidas</h1>
                <div class="d-flex gap-2">
                    <a href="/panel" class="btn btn-outline-primary btn-sm">Mi Panel</a>
                    <a href="/map" class="btn btn-outline-secondary btn-sm">Mapa</a>
                    <a href="/reportes/solicitudes-xlsx" class="btn btn-success btn-sm" title="Descargar XLSX">
                        Descargar XLSX
                    </a>
                </div>
            </div>

            <?php if (empty($solicitudes)): ?>
                <div class="alert alert-info">No hay solicitudes recientes.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $s): ?>
                                <tr>
                                    <td><?= esc($s['id_contrato']) ?></td>
                                    <td><?= esc($s['cliente']) ?></td>
                                    <td><?= esc($s['cliente_telefono'] ?? '') ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($s['estado']) ?></span></td>
                                    <td><?= esc($s['fecha_inicio'] ?? '') ?></td>
                                    <td><?= esc($s['fecha_fin'] ?? '') ?></td>
                                    <td>$<?= esc(number_format((float) ($s['costo_total'] ?? 0), 0, ',', '.')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?= view('partials/footer') ?>
</body>

</html>