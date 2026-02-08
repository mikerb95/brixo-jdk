<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobre Brixo</title>
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
                <h1 class="display-4 fw-bold mb-4">Nuestra Misión</h1>
                <p class="lead text-muted">Conectar a personas con profesionales confiables para hacer realidad sus
                    proyectos, de manera rápida, segura y eficiente.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row align-items-center mb-5">
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-3">¿Qué es Brixo?</h2>
                        <p>Brixo es la plataforma líder en conexión de servicios profesionales. Nacimos con la idea de
                            simplificar la búsqueda de expertos en carpintería, plomería, electricidad y más.</p>
                        <p>Creemos en la transparencia y en dar oportunidades a profesionales talentosos para crecer sus
                            negocios.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-5 text-center text-muted">
                            <i class="fas fa-users fa-4x mb-3"></i>
                            <p>Imagen del Equipo</p>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center flex-md-row-reverse">
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-3">Nuestros Valores</h2>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>
                                <strong>Confianza:</strong> Verificamos a cada profesional.
                            </li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>
                                <strong>Calidad:</strong> Nos aseguramos de que recibas el mejor servicio.
                            </li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i>
                                <strong>Innovación:</strong> Usamos tecnología para facilitarte la vida.
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-4 p-5 text-center text-muted">
                            <i class="fas fa-heart fa-4x mb-3"></i>
                            <p>Imagen de Valores</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>