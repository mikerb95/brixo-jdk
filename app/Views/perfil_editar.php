<?php
/** @var array $user */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="d-flex flex-column min-vh-100">
    <?= view('partials/navbar') ?>

    <main class="flex-grow-1">
        <div class="container my-5" style="max-width:800px;">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 fw-bold">Editar Perfil</h1>
                <a href="/panel" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-3 shadow-sm">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger rounded-3 shadow-sm">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="/perfil/actualizar" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Foto de Perfil -->
                        <div class="text-center mb-4">
                            <?php
                            $fotoUrl = !empty($user['foto_perfil'])
                                ? (strpos($user['foto_perfil'], 'http') === 0 ? $user['foto_perfil'] : '/images/profiles/' . $user['foto_perfil'])
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user['nombre']) . '&background=random';
                            ?>
                            <img src="<?= esc($fotoUrl) ?>" class="rounded-circle mb-3 object-fit-cover" width="120"
                                height="120" alt="Avatar">
                            <div class="mt-2">
                                <label for="foto_perfil" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-camera me-2"></i>Cambiar Foto
                                </label>
                                <input type="file" id="foto_perfil" name="foto_perfil" class="d-none" accept="image/*">
                            </div>
                            <small class="text-muted d-block mt-2">JPG, PNG o WebP. Máx 5MB.</small>
                        </div>

                        <div class="row g-3">
                            <!-- Campos Comunes -->
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" class="form-control rounded-3 p-3" id="nombre" name="nombre"
                                    value="<?= esc($user['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                <input type="tel" class="form-control rounded-3 p-3" id="telefono" name="telefono"
                                    value="<?= esc($user['telefono']) ?>">
                            </div>

                            <div class="col-12">
                                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                                <input type="text" class="form-control rounded-3 p-3" id="direccion" name="direccion"
                                    value="<?= esc($user['direccion'] ?? '') ?>" required>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-6">
                                <label for="registro_departamento" class="form-label fw-semibold">Departamento</label>
                                <select id="registro_departamento" class="form-select rounded-3 p-3"></select>
                            </div>
                            <div class="col-md-6">
                                <label for="registro_ciudad" class="form-label fw-semibold">Ciudad</label>
                                <select id="registro_ciudad" name="ciudad" class="form-select rounded-3 p-3"
                                    required></select>
                            </div>

                            <!-- Campos Específicos de Contratista -->
                            <?php if ($user['rol'] === 'contratista'): ?>
                                <div class="col-12">
                                    <label for="descripcion_perfil" class="form-label fw-semibold">Descripción del
                                        Perfil</label>
                                    <textarea class="form-control rounded-3 p-3" id="descripcion_perfil"
                                        name="descripcion_perfil" rows="4"
                                        placeholder="Cuéntanos sobre tus servicios y experiencia..."><?= esc($user['descripcion_perfil'] ?? '') ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="experiencia" class="form-label fw-semibold">Años de Experiencia</label>
                                    <input type="text" class="form-control rounded-3 p-3" id="experiencia"
                                        name="experiencia" value="<?= esc($user['experiencia'] ?? '') ?>"
                                        placeholder="Ej: 5 años en plomería residencial">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ubicación en el Mapa</label>
                                    <div id="mapaRegistro"
                                        style="height: 300px; width: 100%; border-radius: 10px; margin-bottom: 10px;"></div>
                                    <input id="registro_ubicacion" name="ubicacion_mapa" type="text"
                                        class="form-control rounded-3 p-3" placeholder="Selecciona en el mapa"
                                        value="<?= esc($user['ubicacion_mapa'] ?? '') ?>" readonly>
                                    <small class="text-muted">Arrastra el marcador para actualizar tu ubicación.</small>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?= view('partials/footer') ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/js/colombia-locations.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar selects de Colombia
            const oldCity = "<?= esc($user['ciudad'] ?? '') ?>";
            if (typeof initColombiaSelects === 'function') {
                initColombiaSelects('registro_departamento', 'registro_ciudad', oldCity);
            }

            // Preview y Compresión de imagen (Client-side)
            const fileInput = document.getElementById('foto_perfil');
            const imgPreview = document.querySelector('.rounded-circle');

            fileInput.addEventListener('change', function (e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];

                    // 1. Preview inmediato
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imgPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);

                    // 2. Compresión
                    const maxWidth = 800; // Max width for profile
                    const maxHeight = 800;
                    const quality = 0.7; // JPEG quality

                    const img = new Image();
                    img.src = URL.createObjectURL(file);
                    img.onload = function () {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        // Calculate new dimensions
                        if (width > height) {
                            if (width > maxWidth) {
                                height *= maxWidth / width;
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width *= maxHeight / height;
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // Convert to Blob (JPEG for compression)
                        canvas.toBlob(function (blob) {
                            // Create new File object
                            const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: "image/jpeg",
                                lastModified: Date.now()
                            });

                            // Replace input file with compressed one
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(newFile);
                            fileInput.files = dataTransfer.files;

                            console.log(`Imagen comprimida: ${(blob.size / 1024).toFixed(2)} KB (Original: ${(file.size / 1024).toFixed(2)} KB)`);
                        }, 'image/jpeg', quality);
                    }
                }
            });

            // Mapa para contratistas
            <?php if ($user['rol'] === 'contratista'): ?>
                const mapInput = document.getElementById('registro_ubicacion');
                let map = null;
                let marker = null;

                const initMap = () => {
                    // Coordenadas iniciales (guardadas o default Bogotá)
                    let initialLat = 4.6097;
                    let initialLng = -74.0817;

                    if (mapInput.value) {
                        const parts = mapInput.value.split(',');
                        if (parts.length === 2) {
                            initialLat = parseFloat(parts[0]);
                            initialLng = parseFloat(parts[1]);
                        }
                    }

                    map = L.map('mapaRegistro').setView([initialLat, initialLng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = marker.getLatLng();
                        mapInput.value = `${position.lat},${position.lng}`;
                    });

                    map.on('click', function (e) {
                        marker.setLatLng(e.latlng);
                        mapInput.value = `${e.latlng.lat},${e.latlng.lng}`;
                    });
                };

                initMap();
            <?php endif; ?>
        });
    </script>
</body>

</html>