<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recursos para profesionales | Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Centro de Recursos</h1>
                <p class="lead text-muted">Herramientas y guías para ayudarte a tener éxito en Brixo.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3 text-primary"><i class="fas fa-book fa-2x"></i></div>
                                <h3 class="h5 fw-bold">Guía de Inicio Rápido</h3>
                                <p class="text-muted">Todo lo que necesitas saber para configurar tu perfil y empezar a
                                    recibir trabajos hoy mismo.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Descargar PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3 text-primary"><i class="fas fa-camera fa-2x"></i></div>
                                <h3 class="h5 fw-bold">Tips para Fotos de Perfil</h3>
                                <p class="text-muted">Aprende a tomar fotos que destaquen tu trabajo y generen confianza
                                    en los clientes.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Leer artículo</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3 text-primary"><i class="fas fa-calculator fa-2x"></i></div>
                                <h3 class="h5 fw-bold">Calculadora de Presupuestos</h3>
                                <p class="text-muted">Una herramienta sencilla para ayudarte a cotizar tus servicios de
                                    manera justa y competitiva.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Usar herramienta</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3 text-primary"><i class="fas fa-certificate fa-2x"></i></div>
                                <h3 class="h5 fw-bold">Cursos y Certificaciones</h3>
                                <p class="text-muted">Mejora tus habilidades con cursos recomendados por nuestros socios
                                    educativos.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Ver catálogo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>