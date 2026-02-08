<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        .forgot-hero {
            background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
            min-height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-light">
    <?= view('partials/navbar') ?>

    <section class="forgot-hero text-white">
        <div class="container text-center">
            <i class="fas fa-key fa-3x mb-3 opacity-75"></i>
            <h1 class="display-5 fw-bold">Recuperar Contraseña</h1>
            <p class="lead opacity-75">Te enviaremos un enlace para restablecer tu contraseña</p>
        </div>
    </section>

    <div class="container py-5" style="max-width: 500px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success d-flex align-items-center rounded-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div><?= esc($message) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center rounded-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div><?= esc($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" action="/password/send-reset">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" class="form-control form-control-lg p-3 rounded-3" 
                               id="correo" name="correo" 
                               placeholder="tu-email@ejemplo.com" 
                               required autofocus>
                        <small class="text-muted">Ingresa el correo asociado a tu cuenta</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold mb-3">
                        <i class="fas fa-paper-plane me-2"></i>Enviar enlace de recuperación
                    </button>

                    <div class="text-center">
                        <a href="/login" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Volver al inicio de sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            El enlace de recuperación expirará en 1 hora
        </div>
    </div>

    <?= view('partials/footer') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
