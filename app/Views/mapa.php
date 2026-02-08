<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Profesionales - Brixo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/brixo.css">

    <style>
        body {
            height: 100vh;
            overflow: hidden;
            /* Prevent body scroll, handle in columns */
        }

        .navbar {
            height: auto;
            z-index: 1030;
        }

        .main-container {
            height: calc(100vh - 72px);
            /* Subtract navbar height */
            display: flex;
        }

        .list-column {
            width: 55%;
            /* Adjust width as needed */
            overflow-y: auto;
            padding: 20px;
            background-color: #fff;
        }

        .map-column {
            width: 45%;
            position: relative;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        .pro-card {
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #e0e0e0;
        }

        .pro-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .pro-card.active {
            border-color: #000;
            background-color: #f8f9fa;
        }

        .rating-star {
            color: #ffc107;
            font-size: 0.8rem;
        }

        /* Custom Scrollbar for list */
        .list-column::-webkit-scrollbar {
            width: 8px;
        }

        .list-column::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .list-column::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .list-column::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column-reverse;
                height: auto;
                overflow: auto;
            }

            body {
                overflow: auto;
            }

            .list-column,
            .map-column {
                width: 100%;
                height: 50vh;
            }

            .map-column {
                position: sticky;
                top: 70px;
                z-index: 1020;
            }
        }
    </style>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
</head>

<body class="">
    <?= view('partials/navbar') ?>

    <div class="main-container">
        <!-- List Column -->
        <div class="list-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><?= count($professionals) ?> profesionales encontrados</h5>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill" type="button"
                        data-bs-toggle="dropdown">
                        Filtrar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Precio: Menor a Mayor</a></li>
                        <li><a class="dropdown-item" href="#">Mejor calificados</a></li>
                    </ul>
                </div>
            </div>

            <div class="row row-cols-1 g-3" id="cards-container">
                <?php foreach ($professionals as $pro): ?>
                    <div class="col">
                        <div class="card pro-card rounded-3 p-3" data-id="<?= $pro['id'] ?>" data-lat="<?= $pro['lat'] ?>"
                            data-lng="<?= $pro['lng'] ?>">
                            <div class="d-flex gap-3">
                                <img src="<?= $pro['imagen'] ?>" alt="<?= $pro['nombre'] ?>"
                                    class="rounded-3 object-fit-cover" width="120" height="120" loading="lazy">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?= $pro['profesion'] ?></h6>
                                            <h5 class="fw-bold mb-1"><?= $pro['nombre'] ?></h5>
                                        </div>
                                        <i class="far fa-heart text-muted cursor-pointer"></i>
                                    </div>
                                    <div class="mb-2">
                                        <span class="fw-bold"><i class="fas fa-star rating-star"></i>
                                            <?= $pro['rating'] ?></span>
                                        <span class="text-muted small">(<?= $pro['reviews'] ?> reseñas)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end mt-2">
                                        <div>
                                            <span class="badge bg-light text-dark border">Súper Pro</span>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">Desde $<?= number_format($pro['precio'], 0, ',', '.') ?>
                                            </div>
                                            <div class="small text-muted mb-2">por hora</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-5">
                <?= view('partials/footer') ?>
            </div>
        </div>

        <!-- Map Column -->
        <div class="map-column">
            <div id="map"></div>
        </div>
    </div>

    <script>
        // Initialize Map
        // Default center (Bogota)
        var map = L.map('map').setView([4.6097, -74.0817], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Data from PHP
        var professionals = <?= json_encode($professionals) ?>;
        var markers = {};

        // Custom Icon
        var customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color: white; border-radius: 50%; padding: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;'><i class='fas fa-user text-primary'></i></div>",
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        var activeIcon = L.divIcon({
            className: 'custom-div-icon-active',
            html: "<div style='background-color: #009fd9; color: white; border-radius: 50%; padding: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); width: 40px; height: 40px; display: flex; justify-content: center; align-items: center; transform: scale(1.1);'><i class='fas fa-user'></i></div>",
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        // Add Markers
        professionals.forEach(function (pro) {
            var marker = L.marker([pro.lat, pro.lng], {
                icon: customIcon
            }).addTo(map);

            // Popup content
            var popupContent = `
                <div class="text-center">
                    <h6 class="fw-bold mb-1">${pro.nombre}</h6>
                    <p class="mb-1 small">${pro.profesion}</p>
                    <p class="fw-bold mb-2">$${new Intl.NumberFormat('es-CO').format(pro.precio)}</p>
                </div>
            `;

            marker.bindPopup(popupContent);

            // Store marker reference
            markers[pro.id] = marker;

            // Marker click event
            marker.on('click', function () {
                highlightCard(pro.id);
            });
        });

        // Fit bounds to show all markers
        if (professionals.length > 0) {
            var group = new L.featureGroup(Object.values(markers));
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Card Interaction
        document.querySelectorAll('.pro-card').forEach(card => {
            card.addEventListener('mouseenter', function () {
                var id = this.getAttribute('data-id');
                if (markers[id]) {
                    markers[id].setIcon(activeIcon);
                    markers[id].setZIndexOffset(1000);
                }
            });

            card.addEventListener('mouseleave', function () {
                var id = this.getAttribute('data-id');
                if (markers[id]) {
                    markers[id].setIcon(customIcon);
                    markers[id].setZIndexOffset(0);
                }
            });

            card.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                var lat = this.getAttribute('data-lat');
                var lng = this.getAttribute('data-lng');

                map.flyTo([lat, lng], 15);
                markers[id].openPopup();

                // Highlight card UI
                document.querySelectorAll('.pro-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function highlightCard(id) {
            var card = document.querySelector(`.pro-card[data-id="${id}"]`);
            if (card) {
                card.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                document.querySelectorAll('.pro-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            }
        }
    </script>
</body>

</html>