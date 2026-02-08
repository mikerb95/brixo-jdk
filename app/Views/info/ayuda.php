<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ayuda Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-primary text-white">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">¿Cómo podemos ayudarte?</h1>
                <div class="input-group input-group-lg">
                    <span class="input-group-text border-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0" placeholder="Buscar en la ayuda...">
                    <button class="btn btn-light fw-bold">Buscar</button>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="text-primary mb-3"><i class="fas fa-user fa-3x"></i></div>
                            <h3 class="h5 fw-bold">Para Clientes</h3>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><a href="#" class="text-decoration-none">¿Cómo contratar un
                                        servicio?</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Métodos de pago</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Garantía de satisfacción</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="text-primary mb-3"><i class="fas fa-hard-hat fa-3x"></i></div>
                            <h3 class="h5 fw-bold">Para Profesionales</h3>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><a href="#" class="text-decoration-none">¿Cómo verificar mi perfil?</a>
                                </li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Comisiones y pagos</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Mejorar mi ranking</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-4">
                            <div class="text-primary mb-3"><i class="fas fa-shield-alt fa-3x"></i></div>
                            <h3 class="h5 fw-bold">Seguridad y Cuenta</h3>
                            <ul class="list-unstyled text-start mt-3">
                                <li class="mb-2"><a href="#" class="text-decoration-none">Restablecer contraseña</a>
                                </li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Reportar un problema</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none">Política de privacidad</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <h3 class="fw-bold mb-3">¿Aún necesitas ayuda?</h3>
                    <p class="text-muted mb-4">Nuestro equipo de soporte está disponible 24/7.</p>
                    <a href="mailto:soporte@brixo.com" class="btn btn-primary btn-lg rounded-pill px-5">Contáctanos</a>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>