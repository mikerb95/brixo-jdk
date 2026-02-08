<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguridad en Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Tu seguridad es nuestra prioridad</h1>
                <p class="lead text-muted">Trabajamos día a día para crear un entorno confiable para clientes y
                    profesionales.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-user-check fa-2x text-success"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold">Profesionales Verificados</h3>
                                <p class="text-muted">Revisamos antecedentes y certificaciones de cada profesional para
                                    asegurar que estás en buenas manos.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-lock fa-2x text-success"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold">Pagos Protegidos</h3>
                                <p class="text-muted">Tu dinero se mantiene seguro hasta que confirmas que el trabajo se
                                    ha realizado satisfactoriamente.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-star fa-2x text-success"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold">Reseñas Reales</h3>
                                <p class="text-muted">Solo los clientes que han contratado un servicio pueden dejar una
                                    reseña, garantizando opiniones auténticas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-headset fa-2x text-success"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold">Soporte Dedicado</h3>
                                <p class="text-muted">Nuestro equipo está listo para ayudarte a resolver cualquier
                                    inconveniente que pueda surgir.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4 p-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Consejos de Seguridad</h4>
                    <ul class="mb-0">
                        <li class="mb-2">Mantén todas las comunicaciones dentro de la plataforma de Brixo.</li>
                        <li class="mb-2">Nunca realices pagos en efectivo o fuera de la aplicación.</li>
                        <li class="mb-2">Verifica la identidad del profesional al llegar a tu domicilio.</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>