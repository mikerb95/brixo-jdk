<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prensa - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Sala de Prensa</h1>
                <p class="lead text-muted">Noticias, comunicados y recursos para medios sobre Brixo.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-4">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-4">Últimas Noticias</h2>

                        <div class="mb-4 pb-4 border-bottom">
                            <span class="text-muted small">1 de Diciembre, 2025</span>
                            <h3 class="h4 mt-2"><a href="#" class="text-decoration-none text-dark">Brixo alcanza los
                                    10,000 profesionales registrados</a></h3>
                            <p class="text-muted">Un hito importante en nuestra misión de conectar talento con
                                oportunidades.</p>
                        </div>

                        <div class="mb-4 pb-4 border-bottom">
                            <span class="text-muted small">15 de Noviembre, 2025</span>
                            <h3 class="h4 mt-2"><a href="#" class="text-decoration-none text-dark">Lanzamiento de la
                                    nueva función de Pagos Seguros</a></h3>
                            <p class="text-muted">Ahora puedes pagar a tus contratistas directamente desde la app con
                                total seguridad.</p>
                        </div>

                        <div class="mb-4 pb-4 border-bottom">
                            <span class="text-muted small">20 de Octubre, 2025</span>
                            <h3 class="h4 mt-2"><a href="#" class="text-decoration-none text-dark">Brixo se expande a 5
                                    nuevas ciudades</a></h3>
                            <p class="text-muted">Llevamos nuestros servicios a Medellín, Cali, Barranquilla y más.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-4 bg-light rounded-4">
                            <h4 class="fw-bold mb-3">Contacto de Prensa</h4>
                            <p class="mb-4">Para consultas de medios, entrevistas o material gráfico:</p>
                            <a href="mailto:prensa@brixo.com" class="btn btn-primary w-100">prensa@brixo.com</a>

                            <hr class="my-4">

                            <h4 class="fw-bold mb-3">Kit de Prensa</h4>
                            <p class="small text-muted">Descarga logos, fotos de alta resolución y biografía de los
                                fundadores.</p>
                            <a href="#" class="btn btn-outline-dark w-100"><i class="fas fa-download me-2"></i>
                                Descargar Kit</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>