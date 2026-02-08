<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Mensajes - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/brixo.css">
    <style>
        .conversation-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .conversation-item:hover {
            background-color: #f8f9fa;
        }

        .unread {
            font-weight: bold;
            background-color: #eef2ff;
        }
    </style>
</head>

<body class="bg-light">
    <?= view('partials/navbar') ?>

    <div class="container mt-4 pt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3">
                        <h4 class="mb-0"><i class="fas fa-comments text-primary me-2"></i>Mis Conversaciones</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php if (empty($conversaciones)): ?>
                                <div class="p-4 text-center text-muted">
                                    <p>No tienes conversaciones activas.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($conversaciones as $conv): ?>
                                    <a href="<?= base_url('/mensajes/chat/' . $conv['id'] . '/' . $conv['rol']) ?>"
                                        class="list-group-item list-group-item-action conversation-item p-3 <?= !$conv['leido'] ? 'unread' : '' ?>">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h5 class="mb-1 text-dark"><?= esc($conv['nombre']) ?></h5>
                                            <small class="text-muted"><?= date('d/m H:i', strtotime($conv['fecha'])) ?></small>
                                        </div>
                                        <p class="mb-1 text-secondary text-truncate" style="max-width: 90%;">
                                            <?= esc($conv['ultimo_mensaje']) ?>
                                        </p>
                                        <small class="text-muted text-capitalize"><?= $conv['rol'] ?></small>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('partials/footer') ?>
</body>

</html>