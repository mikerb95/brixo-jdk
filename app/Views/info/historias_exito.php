<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historias de éxito | Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <section class="py-5 bg-light">
            <div class="container text-center" style="max-width: 800px;">
                <h1 class="display-4 fw-bold mb-4">Historias que inspiran</h1>
                <p class="lead text-muted">Descubre cómo Brixo está cambiando la vida de profesionales y clientes.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container" style="max-width: 960px;">
                <div class="row g-5 mb-5 align-items-center">
                    <div class="col-md-6">
                        <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Carlos, Carpintero" class="img-fluid rounded-4 shadow-sm" loading="lazy">
                    </div>
                    <div class="col-md-6">
                        <div class="ps-md-4">
                            <div class="text-warning mb-2"><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h3 class="fw-bold mb-3">"Dupliqué mis clientes en 3 meses"</h3>
                            <p class="text-muted mb-4">Carlos, carpintero con 15 años de experiencia, luchaba por
                                encontrar trabajos constantes. Desde que se unió a Brixo, tiene su agenda llena y ha
                                podido contratar a un ayudante.</p>
                            <p class="fw-bold">- Carlos M., Carpintero</p>
                        </div>
                    </div>
                </div>

                <div class="row g-5 align-items-center flex-md-row-reverse">
                    <div class="col-md-6">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Ana, Cliente" class="img-fluid rounded-4 shadow-sm" loading="lazy">
                    </div>
                    <div class="col-md-6">
                        <div class="pe-md-4">
                            <div class="text-warning mb-2"><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h3 class="fw-bold mb-3">"Encontré al plomero perfecto en una emergencia"</h3>
                            <p class="text-muted mb-4">Ana tuvo una fuga de agua un domingo por la noche. Gracias a
                                Brixo, encontró a un plomero verificado que llegó en menos de una hora y solucionó el
                                problema.</p>
                            <p class="fw-bold">- Ana R., Cliente Satisfecha</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?= view('info/partials/footer_static') ?>

</body>

</html>