<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Servicios - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/brixo.css">

    <style>
        body {
            background-color: #f7f8fc;
        }

        main {
            margin-top: var(--navbar-offset, 4.5rem);
            padding: 2rem 0 3rem;
        }

        .filters-wrapper {
            position: sticky;
            top: calc(var(--navbar-offset, 4.5rem) + 24px);
            border: 1px solid #e9ecef;
            border-radius: 1rem;
            background-color: #fff;
            padding: 1.5rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .filter-title {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #4a5162;
            margin-bottom: 0.75rem;
        }

        .filter-link {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: #495057;
            text-decoration: none;
            padding: 0.35rem 0;
            border-radius: 0.75rem;
        }

        .filter-link:hover {
            color: #111827;
        }

        .service-card {
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.12);
        }

        .service-img {
            height: 160px;
            object-fit: cover;
        }

        .price-tag {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0d6efd;
        }

        @media (max-width: 991.98px) {
            .filters-wrapper {
                position: static;
                margin-bottom: 1.5rem;
            }
        }
    </style>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container-fluid px-4">
            <div class="row g-4">
                <div class="col-12 col-lg-4 col-xl-3 d-none d-lg-block">
                    <aside class="filters-wrapper">
                        <div class="mb-4">
                            <h6 class="filter-title">Categorías</h6>
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="cat-<?= md5($cat) ?>">
                                    <label class="form-check-label small" for="cat-<?= md5($cat) ?>">
                                        <?= $cat ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-4">
                            <h6 class="filter-title">Opinión de clientes</h6>
                            <a href="#" class="filter-link rating-filter">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="fas fa-star"></i><i class="far fa-star"></i>
                                <span class="ms-1 text-dark">o más</span>
                            </a>
                            <a href="#" class="filter-link rating-filter">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                    class="far fa-star"></i><i class="far fa-star"></i>
                                <span class="ms-1 text-dark">o más</span>
                            </a>
                            <a href="#" class="filter-link rating-filter">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i
                                    class="far fa-star"></i><i class="far fa-star"></i>
                                <span class="ms-1 text-dark">o más</span>
                            </a>
                        </div>

                        <div class="mb-4">
                            <h6 class="filter-title">Precio</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="number" class="form-control form-control-sm" placeholder="Mín">
                                <span class="text-muted">-</span>
                                <input type="number" class="form-control form-control-sm" placeholder="Máx">
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill w-100">Ir</button>
                        </div>

                        <div class="mb-0">
                            <h6 class="filter-title">Ubicación</h6>
                            <?php foreach ($locations as $loc): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="loc-<?= md5($loc) ?>">
                                    <label class="form-check-label small" for="loc-<?= md5($loc) ?>">
                                        <?= $loc ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                </div>

                <div class="col-12 col-lg-8 col-xl-9">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom pb-3 gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">Resultados para "Servicios"</h5>
                            <small class="text-muted"><?= count($services) ?> resultados encontrados</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted">Ordenar por:</label>
                            <select class="form-select form-select-sm rounded-pill" style="width: auto;">
                                <option selected>Destacados</option>
                                <option value="1">Precio: Bajo a Alto</option>
                                <option value="2">Precio: Alto a Bajo</option>
                                <option value="3">Calificación promedio</option>
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        <?php foreach ($services as $service): ?>
                            <div class="col">
                                <div class="card h-100 service-card rounded-3 overflow-hidden">
                                    <div class="position-relative">
                                        <img src="<?= $service['imagen'] ?>" class="card-img-top service-img"
                                            alt="<?= $service['titulo'] ?>" loading="lazy">
                                        <span
                                            class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 rounded-pill">
                                            <?= $service['categoria'] ?>
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <a href="/profesional/<?= $service['profesional']['id'] ?>"
                                            class="text-decoration-none text-dark">
                                            <h6 class="card-title fw-bold mb-1 text-truncate"
                                                title="<?= $service['titulo'] ?>"><?= $service['titulo'] ?></h6>
                                        </a>
                                        <div class="mb-2 small">
                                            <a href="/profesional/<?= $service['profesional']['id'] ?>"
                                                class="text-muted text-decoration-none hover-underline">
                                                Por <?= $service['profesional']['nombre'] ?>
                                            </a>
                                        </div>
                                        <div class="mb-2">
                                            <span class="text-warning small">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i
                                                        class="<?= $i < floor($service['rating']) ? 'fas' : 'far' ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </span>
                                            <span class="text-primary small ms-1"><?= $service['reviews'] ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <span
                                                class="price-tag">$<?= number_format($service['precio'], 0, ',', '.') ?></span>
                                            <span class="small text-muted">/ servicio</span>
                                        </div>
                                        <div class="d-grid">
                                            <a href="/profesional/<?= $service['profesional']['id'] ?>"
                                                class="btn btn-warning btn-sm rounded-pill fw-bold">Ver detalles</a>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>
                                            <?= $service['profesional']['ubicacion'] ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <nav aria-label="Page navigation" class="mt-5 d-flex justify-content-center">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <?= view('partials/footer') ?>
</body>

</html>