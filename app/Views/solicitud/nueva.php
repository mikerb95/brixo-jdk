<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Solicitud - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="bg-light">
    <?= view('partials/navbar') ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">Crear Solicitud de Servicio</h2>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <form action="/solicitud/guardar" method="post">
                            <?= csrf_field() ?>

                            <?php if (!empty($id_contratista)): ?>
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-user-check me-2"></i>
                                    <div>
                                        Esta solicitud será enviada directamente a:
                                        <strong><?= esc($nombre_contratista) ?></strong>
                                        <input type="hidden" name="id_contratista" value="<?= esc($id_contratista) ?>">
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-bullhorn me-2"></i>
                                    Esta solicitud será <strong>pública</strong> para todos los contratistas disponibles.
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($prefill)): ?>
                                <div class="alert alert-info d-flex align-items-start border-0 rounded-3 mb-4">
                                    <i class="fas fa-robot fs-5 me-3 mt-1"></i>
                                    <div>
                                        <strong>Datos pre-llenados desde el Cotizador IA</strong>
                                        <p class="mb-0 small text-muted">El título y la descripción se completaron automáticamente. Puedes editarlos antes de publicar. Completa la ubicación y presupuesto.</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="titulo" class="form-label fw-semibold">Título de la solicitud</label>
                                <input type="text" class="form-control p-3" id="titulo" name="titulo"
                                    value="<?= esc($prefill['titulo'] ?? '') ?>"
                                    placeholder="Ej: Reparación de tubería en cocina" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label fw-semibold">Descripción detallada</label>
                                <textarea class="form-control p-3" id="descripcion" name="descripcion" rows="<?= !empty($prefill['descripcion']) ? '10' : '5' ?>"
                                    placeholder="Describe el problema, qué necesitas y cualquier detalle relevante..."
                                    required><?= esc($prefill['descripcion'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="presupuesto" class="form-label fw-semibold">Presupuesto estimado
                                        (Opcional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control p-3" id="presupuesto"
                                            name="presupuesto" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="departamento" class="form-label fw-semibold">Departamento</label>
                                    <select id="departamento" name="departamento" class="form-select p-3"
                                        required></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ciudad" class="form-label fw-semibold">Ciudad</label>
                                    <select id="ciudad" name="ciudad" class="form-select p-3" disabled
                                        required></select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ubicacion" class="form-label fw-semibold">Barrio / Dirección</label>
                                <input type="text" class="form-control p-3" id="ubicacion" name="ubicacion"
                                    placeholder="Ej: Chapinero, Calle 123 # 45-67">
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Publicar Solicitud</button>
                                <a href="/panel" class="btn btn-link text-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('partials/footer') ?>

    <script src="/js/colombia-locations.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof initColombiaSelects === 'function') {
                initColombiaSelects('departamento', 'ciudad');
            }
        });
    </script>
</body>

</html>