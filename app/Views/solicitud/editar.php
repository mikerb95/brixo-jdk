<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Solicitud - Brixo</title>
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
                        <h2 class="fw-bold mb-4">Editar Solicitud</h2>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <form action="/solicitud/actualizar/<?= esc($solicitud['id_solicitud']) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="titulo" class="form-label fw-semibold">Título de la solicitud</label>
                                <input type="text" class="form-control p-3" id="titulo" name="titulo"
                                    value="<?= esc($solicitud['titulo']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label fw-semibold">Descripción detallada</label>
                                <textarea class="form-control p-3" id="descripcion" name="descripcion" rows="5"
                                    required><?= esc($solicitud['descripcion']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="presupuesto" class="form-label fw-semibold">Presupuesto estimado
                                        (Opcional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control p-3" id="presupuesto"
                                            name="presupuesto" value="<?= esc($solicitud['presupuesto']) ?>"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ubicacion" class="form-label fw-semibold">Ubicación / Barrio</label>
                                    <input type="text" class="form-control p-3" id="ubicacion" name="ubicacion"
                                        value="<?= esc($solicitud['ubicacion']) ?>" placeholder="Ej: Chapinero, Bogotá">
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Actualizar
                                    Solicitud</button>
                                <a href="/panel" class="btn btn-link text-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('partials/footer') ?>
</body>

</html>