<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pro['nombre'] ?> - Perfil Profesional | Brixo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/brixo.css">

    <style>
        .profile-header-bg {
            background-color: #f8f9fa;
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .profile-img-large {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .verified-badge {
            color: #009fd9;
        }

        .rating-star {
            color: #ffc107;
        }

        .service-card {
            transition: transform 0.2s;
        }

        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .sticky-sidebar {
            position: sticky;
            top: 90px;
            /* Height of navbar + some gap */
        }
    </style>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <!-- Profile Header -->
        <div class="profile-header-bg border-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-auto text-center text-md-start">
                        <img src="<?= $pro['imagen'] ?>" alt="<?= $pro['nombre'] ?>"
                            class="rounded-circle profile-img-large">
                    </div>
                    <div class="col-md text-center text-md-start mt-3 mt-md-0">
                        <h1 class="fw-bold mb-1">
                            <?= esc($pro['nombre']) ?>
                            <?php if (!empty($pro['verificado'])): ?>
                                <i class="fas fa-check-circle verified-badge fs-4" title="Identidad Verificada"></i>
                            <?php endif; ?>
                        </h1>
                        <h4 class="text-muted mb-2"><?= esc($pro['profesion'] ?? 'Profesional') ?></h4>
                        <div
                            class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-2">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-1"><?= $pro['rating'] ?></span>
                                <div class="text-warning me-1">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php if ($i < floor($pro['rating'])): ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif ($i < $pro['rating']): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-muted text-decoration-underline">(<?= $pro['reviews_count'] ?>
                                    reseñas)</span>
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> <?= $pro['ubicacion'] ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                            <span class="badge bg-light text-dark border"><i
                                    class="fas fa-trophy text-warning me-1"></i> Súper Pro</span>
                            <span class="badge bg-light text-dark border"><i class="fas fa-clock me-1"></i> 10 trabajos
                                este mes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">

                    <!-- About Section -->
                    <section class="mb-5">
                        <h3 class="fw-bold mb-3">Sobre mí</h3>
                        <p class="lead fs-6 text-secondary"><?= esc($pro['descripcion'] ?? 'Sin descripción disponible.') ?></p>
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-3 me-3">
                                        <i class="fas fa-briefcase text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Experiencia</div>
                                        <div class="text-muted"><?= esc($pro['experiencia'] ?? 'Profesional') ?> en el sector</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-3 me-3">
                                        <i class="fas fa-user-shield text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Garantía</div>
                                        <div class="text-muted">Trabajo asegurado y garantizado</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <hr class="my-5">

                    <!-- Services Section -->
                    <section class="mb-5">
                        <h3 class="fw-bold mb-4">Servicios Ofrecidos</h3>
                        <div class="row g-3">
                            <?php if (empty($servicios)): ?>
                                <div class="col-12">
                                    <p class="text-muted"><i class="fas fa-info-circle me-2"></i>Este profesional aún no ha registrado servicios.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($servicios as $servicio): ?>
                                    <div class="col-md-6">
                                        <div class="card h-100 border shadow-sm service-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title fw-bold mb-0"><?= esc($servicio['nombre'] ?? 'Servicio') ?></h5>
                                                    <?php $precio = $servicio['precio_estimado'] ?? $servicio['precio'] ?? 0; ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">Desde
                                                        $<?= number_format($precio, 0, ',', '.') ?></span>
                                                </div>
                                                <p class="card-text text-muted small"><?= esc($servicio['descripcion'] ?? 'Sin descripción') ?></p>
                                                <button class="btn btn-outline-primary btn-sm w-100 mt-2">Cotizar este
                                                    servicio</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>

                    <hr class="my-5">

                    <!-- Reviews Section -->
                    <section class="mb-5">
                        <h3 class="fw-bold mb-4">Reseñas de clientes</h3>

                        <?php if (empty($resenas)): ?>
                            <p class="text-muted"><i class="fas fa-info-circle me-2"></i>Este profesional aún no tiene reseñas.</p>
                        <?php else: ?>
                            <?php foreach ($resenas as $resena): ?>
                                <div class="card border-0 mb-4">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center fw-bold text-secondary"
                                                style="width: 50px; height: 50px;">
                                                <?= strtoupper(substr($resena['autor'] ?? 'U', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold mb-0"><?= esc($resena['autor'] ?? 'Usuario') ?></h6>
                                                <small class="text-muted"><?= esc($resena['fecha'] ?? '') ?></small>
                                            </div>
                                            <div class="text-warning mb-2 rating-star">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i class="<?= $i < ($resena['calificacion'] ?? 0) ? 'fas' : 'far' ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <p class="text-secondary mb-0"><?= esc($resena['comentario'] ?? '') ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (count($resenas ?? []) > 3): ?>
                            <button class="btn btn-outline-secondary w-100">Ver todas las reseñas</button>
                        <?php endif; ?>
                    </section>

                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-sidebar">
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-4">Contactar a <?= explode(' ', $pro['nombre'])[0] ?></h4>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-lg fw-bold">Solicitar Cotización</button>
                                    <button class="btn btn-outline-dark btn-lg fw-bold">Enviar Mensaje</button>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-muted"><i class="fas fa-bolt text-warning"></i> Responde en menos
                                        de 1 hora</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light p-3 text-center border-top-0 rounded-bottom-4">
                                <small class="text-muted">Contratación segura a través de Brixo</small>
                            </div>
                        </div>

                        <div class="card border shadow-sm rounded-4 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Certificaciones</h5>
                                <ul class="list-unstyled mb-0">
                                    <?php if (empty($certificaciones)): ?>
                                        <li class="text-muted"><i class="fas fa-info-circle me-2"></i>Sin certificaciones registradas</li>
                                    <?php else: ?>
                                        <?php foreach ($certificaciones as $cert): ?>
                                            <li class="mb-2 d-flex align-items-start">
                                                <i class="fas fa-certificate text-success mt-1 me-2"></i>
                                                <div>
                                                    <span class="fw-semibold"><?= esc($cert['nombre'] ?? 'Certificación') ?></span>
                                                    <?php if (!empty($cert['entidad_emisora'])): ?>
                                                        <small class="text-muted d-block"><?= esc($cert['entidad_emisora']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="card border shadow-sm rounded-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Disponibilidad</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Lunes - Viernes</span>
                                    <span class="fw-bold">8:00 - 18:00</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Sábados</span>
                                    <span class="fw-bold">9:00 - 14:00</span>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Card -->
                        <div class="card border shadow-sm rounded-4 mt-4">
                            <div class="card-body text-center">
                                <h5 class="fw-bold mb-3">Compartir Perfil</h5>
                                <div id="qrcode" class="d-flex justify-content-center mb-3"></div>
                                <small class="text-muted d-block">Escanea para ver en móvil</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?= view('partials/footer') ?>

    <!-- QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrcodeDiv = document.getElementById('qrcode');
            if (typeof QRCode !== 'undefined' && qrcodeDiv) {
                new QRCode(qrcodeDiv, {
                    text: window.location.href,
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        });
    </script>
</body>

</html>