<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Únete como profesional | Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-dark text-white">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Haz crecer tu negocio con Brixo</h1>
                <p class="lead mb-4">Conecta con clientes que buscan tus servicios y gestiona tu trabajo de forma
                    profesional.</p>
                <a href="#" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold" data-bs-toggle="modal"
                    data-bs-target="#registerModal">Regístrate Gratis</a>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row text-center mb-5">
                    <div class="col-md-4">
                        <div class="mb-3 text-primary"><i class="fas fa-bullhorn fa-3x"></i></div>
                        <h3 class="h5 fw-bold">Más Clientes</h3>
                        <p class="text-muted">Recibe solicitudes de trabajo directamente en tu celular.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 text-primary"><i class="fas fa-calendar-check fa-3x"></i></div>
                        <h3 class="h5 fw-bold">Gestiona tu Agenda</h3>
                        <p class="text-muted">Organiza tus citas y trabajos desde un solo lugar.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3 text-primary"><i class="fas fa-wallet fa-3x"></i></div>
                        <h3 class="h5 fw-bold">Pagos Seguros</h3>
                        <p class="text-muted">Recibe tus pagos de forma rápida y segura, sin complicaciones.</p>
                    </div>
                </div>

                <div class="row align-items-center bg-light rounded-4 overflow-hidden g-0">
                    <div class="col-md-6 p-5">
                        <h2 class="fw-bold mb-4">¿Cómo empezar?</h2>
                        <ol class="list-group list-group-numbered list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent border-0 ps-0">Regístrate como Contratista.</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Completa tu perfil con fotos y
                                experiencia.</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Verifica tu identidad para ganar
                                confianza.</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">¡Empieza a recibir ofertas!</li>
                        </ol>
                    </div>
                    <div class="col-md-6 h-100">
                        <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Profesional trabajando" class="img-fluid h-100 object-fit-cover"
                            style="min-height: 300px;" loading="lazy">
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>