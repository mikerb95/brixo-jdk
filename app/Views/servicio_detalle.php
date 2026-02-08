<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $service['titulo'] ?> - Brixo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/brixo.css">

    <style>
        .gallery-main-img {
            height: 400px;
            object-fit: cover;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
        }

        .gallery-thumb {
            height: 80px;
            object-fit: cover;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            border: 2px solid transparent;
        }

        .gallery-thumb:hover,
        .gallery-thumb.active {
            opacity: 1;
            border-color: #009fd9;
        }

        .sticky-booking-card {
            position: sticky;
            top: 90px;
            z-index: 100;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 0.9rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background-color: #e3f2fd;
            color: #009fd9;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 1rem;
        }
    </style>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container py-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/servicios"
                            class="text-decoration-none text-muted">Servicios</a></li>
                    <li class="breadcrumb-item"><a href="#"
                            class="text-decoration-none text-muted"><?= $service['categoria'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalles del servicio</li>
                </ol>

                <div class="row">
                    <div class="col-lg-8">

                        <!-- Image Gallery -->
                        <div class="mb-4">
                            <div class="mb-2">
                                <img src="<?= $service['imagenes'][0] ?>" id="mainImage"
                                    class="gallery-main-img shadow-sm" alt="Imagen Principal">
                            </div>
                            <div class="row g-2">
                                <?php foreach ($service['imagenes'] as $index => $img): ?>
                                    <div class="col-3 col-md-2">
                                        <img src="<?= $img ?>" class="gallery-thumb <?= $index === 0 ? 'active' : '' ?>"
                                            onclick="changeImage(this.src, this)" alt="Thumbnail" loading="lazy">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Title & Basic Info (Mobile only, hidden on desktop usually but keeping simple here) -->
                        <div class="d-lg-none mb-4">
                            <h1 class="fw-bold mb-2"><?= $service['titulo'] ?></h1>
                            <div class="d-flex align-items-center mb-3">
                                <div class="rating-stars me-2">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                        class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-muted small"><?= $service['reviews_count'] ?> calificaciones</span>
                            </div>
                            <h2 class="fw-bold text-primary mb-0">$<?= number_format($service['precio'], 0, ',', '.') ?>
                                <span class="fs-6 text-muted fw-normal">/ <?= $service['unidad'] ?></span></h2>
                        </div>

                        <!-- Description -->
                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">Descripción del servicio</h3>
                            <p class="lead fs-6 text-secondary"><?= $service['descripcion_larga'] ?></p>

                            <div class="row mt-4">
                                <h5 class="fw-bold mb-3">Lo que incluye:</h5>
                                <?php foreach ($service['caracteristicas'] as $feature): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="feature-icon">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <span><?= $feature ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <hr class="my-5">

                        <!-- Professional Info -->
                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-4">Sobre el profesional</h3>
                            <div class="card border-0 bg-light rounded-4 p-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $service['profesional']['imagen'] ?>" class="rounded-circle me-3"
                                        width="80" height="80" alt="Pro">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?= $service['profesional']['nombre'] ?></h5>
                                        <p class="text-muted mb-1"><?= $service['profesional']['titulo'] ?></p>
                                        <div class="d-flex align-items-center text-warning small">
                                            <i class="fas fa-star me-1"></i>
                                            <span
                                                class="fw-bold text-dark me-1"><?= $service['profesional']['rating'] ?></span>
                                            <span class="text-muted">(120 reseñas totales)</span>
                                        </div>
                                    </div>
                                    <div class="ms-auto d-none d-md-block">
                                        <a href="/profesional/<?= $service['profesional']['id'] ?>"
                                            class="btn btn-outline-dark rounded-pill">Ver perfil completo</a>
                                    </div>
                                </div>
                                <div class="mt-3 d-md-none">
                                    <a href="/profesional/<?= $service['profesional']['id'] ?>"
                                        class="btn btn-outline-dark rounded-pill w-100">Ver perfil completo</a>
                                </div>
                            </div>
                        </section>

                    </div>

                    <!-- Right Column: Booking Card (Sticky) -->
                    <div class="col-lg-4">
                        <div class="sticky-booking-card">
                            <div class="card shadow-sm border rounded-4">
                                <div class="card-body p-4">
                                    <div class="d-none d-lg-block">
                                        <h4 class="fw-bold mb-2"><?= $service['titulo'] ?></h4>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="rating-stars me-2">
                                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                    class="fas fa-star"></i><i class="fas fa-star"></i><i
                                                    class="fas fa-star-half-alt"></i>
                                            </div>
                                            <a href="#reviews"
                                                class="text-muted small text-decoration-underline"><?= $service['reviews_count'] ?>
                                                calificaciones</a>
                                        </div>
                                        <hr>
                                    </div>

                                    <div class="mb-4">
                                        <span class="text-muted small d-block mb-1">Precio estimado</span>
                                        <div class="d-flex align-items-baseline">
                                            <h2 class="fw-bold text-dark mb-0">
                                                $<?= number_format($service['precio'], 0, ',', '.') ?>
                                            </h2>
                                            <span class="text-muted ms-2">/ <?= $service['unidad'] ?></span>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary btn-lg fw-bold rounded-pill">Solicitar
                                            Cotización</button>
                                        <button class="btn btn-outline-secondary btn-lg fw-bold rounded-pill">Contactar
                                            Profesional</button>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <small class="text-muted"><i class="fas fa-shield-alt text-success me-1"></i>
                                            Garantía de
                                            satisfacción Brixo</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-light p-3 rounded-bottom-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-info-circle text-muted mt-1"></i>
                                        <small class="text-muted lh-sm">El precio final puede variar dependiendo de los
                                            detalles
                                            específicos de tu proyecto. Solicita una cotización para obtener un precio
                                            exacto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </main>

    <?= view('partials/footer') ?>

    <script>
        function changeImage(src, element) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.gallery-thumb').forEach(thumb => thumb.classList.remove('active'));
            element.classList.add('active');
        }
    </script>
</body>

</html>