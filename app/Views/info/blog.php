<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Blog de Brixo</h1>
                <p class="lead text-muted">Consejos, tutoriales e historias para mejorar tu hogar y tu negocio.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 1200px;">
                <div class="row g-4">
                    <!-- Artículo Destacado -->
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <img src="https://images.unsplash.com/photo-1581578731117-104f8a746956?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                                        class="img-fluid h-100 object-fit-cover" alt="Renovación"
                                        style="min-height: 300px;" loading="lazy">
                                </div>
                                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                                    <div class="mb-2 text-primary fw-bold text-uppercase small">Hogar</div>
                                    <h2 class="card-title fw-bold mb-3">Guía completa para renovar tu cocina sin gastar
                                        una fortuna</h2>
                                    <p class="card-text text-muted mb-4">Descubre los secretos de los expertos para
                                        darle un nuevo aire a tu cocina con un presupuesto ajustado.</p>
                                    <a href="#" class="btn btn-outline-primary w-auto align-self-start">Leer más</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Artículos Recientes -->
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
                                class="card-img-top" alt="Electricidad" loading="lazy">
                            <div class="card-body">
                                <div class="mb-2 text-primary fw-bold text-uppercase small">Seguridad</div>
                                <h5 class="card-title fw-bold">5 señales de que tu instalación eléctrica necesita
                                    revisión</h5>
                                <p class="card-text text-muted">No ignores estas advertencias que podrían poner en
                                    riesgo tu hogar.</p>
                                <a href="#" class="text-decoration-none fw-bold">Leer artículo <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
                                class="card-img-top" alt="Plomería" loading="lazy">
                            <div class="card-body">
                                <div class="mb-2 text-primary fw-bold text-uppercase small">Mantenimiento</div>
                                <h5 class="card-title fw-bold">Cómo solucionar fugas de agua comunes tú mismo</h5>
                                <p class="card-text text-muted">Ahorra dinero con estas reparaciones sencillas antes de
                                    llamar a un experto.</p>
                                <a href="#" class="text-decoration-none fw-bold">Leer artículo <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
                                class="card-img-top" alt="Negocios" loading="lazy">
                            <div class="card-body">
                                <div class="mb-2 text-primary fw-bold text-uppercase small">Para Profesionales</div>
                                <h5 class="card-title fw-bold">Tips para conseguir más clientes en Brixo</h5>
                                <p class="card-text text-muted">Mejora tu perfil y tus reseñas para destacar entre la
                                    competencia.</p>
                                <a href="#" class="text-decoration-none fw-bold">Leer artículo <i
                                        class="fas fa-arrow-right ms-1"></i></a>
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