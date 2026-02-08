<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Página no encontrada - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <!-- 404 Content -->
    <section class="d-flex align-items-center justify-content-center flex-grow-1 py-5 text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h1 class="display-1 fw-bold text-primary mb-3">404</h1>
                    <h2 class="h3 fw-bold mb-4">¡Ups! Página no encontrada</h2>
                    <p class="lead text-secondary mb-5">
                        Lo sentimos, la página que estás buscando no existe o ha sido movida.
                        Tal vez quieras volver al inicio o buscar un profesional.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                            <i class="fas fa-home me-2"></i>Volver al Inicio
                        </a>
                        <a href="/map" class="btn btn-outline-dark btn-lg rounded-pill px-4 fw-bold">
                            <i class="fas fa-map-marked-alt me-2"></i>Ver Mapa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Partial -->
    <?= view('partials/footer') ?>

</body>

</html>