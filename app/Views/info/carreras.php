<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carreras en Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Únete a nuestro equipo</h1>
                <p class="lead text-muted">Estamos construyendo el futuro de los servicios profesionales. ¿Quieres ser
                    parte?</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <h2 class="fw-bold mb-4">Posiciones abiertas</h2>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title fw-bold">Desarrollador Backend (PHP/CodeIgniter)</h5>
                                <p class="card-text text-muted">Remoto - Tiempo Completo</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary">Aplicar</a>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title fw-bold">Especialista en Marketing Digital</h5>
                                <p class="card-text text-muted">Bogotá - Híbrido</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary">Aplicar</a>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title fw-bold">Soporte al Cliente</h5>
                                <p class="card-text text-muted">Remoto - Medio Tiempo</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary">Aplicar</a>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <p>¿No ves una posición para ti? Envíanos tu CV a <a
                            href="mailto:talento@brixo.com">talento@brixo.com</a></p>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>