<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cómo funciona Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Así de fácil es usar Brixo</h1>
                <p class="lead text-muted">Encuentra al profesional perfecto en minutos, sin complicaciones.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-5">
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">1</div>
                        <h3 class="h4 fw-bold mb-3">Busca</h3>
                        <p class="text-muted">Explora perfiles de profesionales verificados en tu zona. Filtra por
                            especialidad, precio y calificaciones.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">2</div>
                        <h3 class="h4 fw-bold mb-3">Contacta</h3>
                        <p class="text-muted">Chatea directamente con los expertos, pide cotizaciones y resuelve tus
                            dudas antes de contratar.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4"
                            style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">3</div>
                        <h3 class="h4 fw-bold mb-3">Contrata</h3>
                        <p class="text-muted">Acuerda el servicio y paga de forma segura a través de la plataforma.
                            ¡Disfruta del resultado!</p>
                    </div>
                </div>

                <div class="mt-5 p-5 bg-light rounded-4 text-center">
                    <h3 class="fw-bold mb-3">¿Eres un profesional?</h3>
                    <p class="mb-4">Únete a Brixo y haz crecer tu negocio llegando a miles de clientes potenciales.</p>
                    <a href="/unete-pro" class="btn btn-outline-primary btn-lg rounded-pill px-4">Empezar como
                        Profesional</a>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>