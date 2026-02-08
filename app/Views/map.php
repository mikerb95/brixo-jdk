<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="/css/brixo.css">
    <style>
        /* Full-screen map layout */
        html,
        body,
        #mapApp {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #mapApp {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main.content {
            flex: 1 1 auto;
            display: flex;
            gap: 0;
            padding: 0;
        }

        /* Sidebar on the left for filters and results */
        .left-sidebar {
            width: 400px;
            padding: 16px;
            padding-top: 72px; /* Space for floating navbar overlay */
            background: #fff;
            border-right: 1px solid #eef2f7;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Map area */
        #map {
            flex: 1 1 auto;
            height: 100vh;
        }

        .listing-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            align-items: center;
            cursor: pointer;
            transition: background .12s ease;
            border: 1px solid #e0e0e0;
            margin-bottom: 8px;
        }

        .listing-item:hover {
            background: #f7f9fb;
            border-color: #0b5ed7;
        }

        .listing-img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .listing-info .title {
            font-weight: 700;
            font-size: 1rem;
        }

        .listing-info .meta {
            font-size: 0.85rem;
            color: #667085;
            margin-top: 4px;
        }

        .filters-section {
            margin-bottom: 20px;
        }

        .results-section {
            flex: 1;
            overflow-y: auto;
        }

        @media (max-width: 992px) {
            .left-sidebar {
                width: 100%;
                height: auto;
                padding-top: 72px;
                border-right: none;
                border-bottom: 1px solid #eef2f7;
            }

            #map {
                height: 60vh;
            }

            main.content {
                flex-direction: column;
            }
        }
    </style>
</head>

<body class="map-page">
    <?= view('partials/navbar') ?>

    <div id="mapApp">
        <main class="content">
            <aside class="left-sidebar">
                <h5 class="fw-bold">Filtros</h5>
                <p class="text-muted">Filtra por categoría, calificación o precio (demo).</p>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Buscar</label>
                    <input id="searchBox" class="form-control" placeholder="Nombre o profesión">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mínimo estrellas</label>
                    <select id="minRating" class="form-select">
                        <option value="0">Cualquiera</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="4.5">4.5+</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ordenar</label>
                    <select id="sortBy" class="form-select">
                        <option value="distance">Cercanía</option>
                        <option value="rating">Mejor puntuados</option>
                    </select>
                </div>

                <hr>
                <h6 class="fw-bold">Resultados</h6>
                <div id="countResults" class="text-muted mb-2">0 contratistas</div>
                <div id="resultsList"></div>
            </aside>

            <div id="map"></div>
        </main>

        <!-- Modal for QR Code -->
        <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">Código QR del Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="qrcode"></div>
                        <p class="mt-3">Escanea este código QR para ver el perfil profesional.</p>
                    </div>
                </div>
            </div>
        </div>

        <?= view('partials/footer') ?>
    </div>

    <!-- QRCode.js for generating QR codes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // JavaScript: render map, markers and interactive listing (100% JS as requested)
        (function () {
            const professionals = <?= json_encode($professionals ?? []) ?>;

            const map = L.map('map').setView([4.6097, -74.0817], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            const markers = {};

            // Utility: create DOM element from HTML
            function el(html) {
                const div = document.createElement('div');
                div.innerHTML = html.trim();
                return div.firstChild;
            }

            function updateCount(n) {
                document.getElementById('countResults').textContent = `${n} contratistas`;
            }

            function renderList(items) {
                const panel = document.getElementById('resultsList');
                panel.innerHTML = '';
                items.forEach(p => {
                    const node = el(`
                        <div class="listing-item" data-id="${p.id}">
                            <img class="listing-img" src="${p.imagen}" alt="${p.nombre}">
                            <div class="listing-info">
                                <div class="title">${p.nombre}</div>
                                <div class="meta">${p.profesion} · ${p.ubicacion} · ${p.rating}★ (${p.reviews})</div>
                                <div class="mt-2">
                                    <a href="/perfil/ver/${p.id}" class="btn btn-sm btn-primary me-1">Ver Perfil</a>
                                    <button class="btn btn-sm btn-outline-primary qr-btn" data-url="${window.location.origin}/perfil/ver/${p.id}">Ver QR</button>
                                </div>
                            </div>
                        </div>
                    `);

                    node.addEventListener('click', (e) => {
                        if (e.target.tagName === 'A' || e.target.classList.contains('qr-btn')) return; // Don't trigger map click for buttons
                        // center map on marker and open popup
                        const m = markers[p.id];
                        if (m) {
                            map.setView(m.getLatLng(), 15, { animate: true });
                            m.openPopup();
                        }
                    });

                    // Handle QR button click
                    const qrBtn = node.querySelector('.qr-btn');
                    qrBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const url = e.target.dataset.url;
                        generateQR(url);
                    });

                    panel.appendChild(node);
                });
            }

            function filterAndRender() {
                const q = document.getElementById('searchBox').value.toLowerCase();
                const minR = parseFloat(document.getElementById('minRating').value);
                const sortBy = document.getElementById('sortBy').value;

                let items = professionals.filter(p => {
                    if (p.rating && parseFloat(p.rating) < minR) return false;
                    if (q && !(p.nombre.toLowerCase().includes(q) || p.profesion.toLowerCase().includes(q))) return false;
                    return true;
                });

                if (sortBy === 'rating') {
                    items.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
                }

                renderList(items);
                updateCount(items.length);
            }

            // Add markers
            professionals.forEach(p => {
                const m = L.marker([p.lat, p.lng]).addTo(map);
                m.bindPopup(`
                    <div class="text-center">
                        <strong>${p.nombre}</strong><br>
                        ${p.profesion}<br>
                        ${p.ubicacion}<br>
                        ${p.rating}★ (${p.reviews})<br>
                        <a href="/perfil/ver/${p.id}" class="btn btn-sm btn-primary mt-2 text-white">Ver Perfil</a>
                    </div>
                `);
                markers[p.id] = m;
            });

            // Initial render
            renderList(professionals);
            updateCount(professionals.length);

            // Hook filters
            document.getElementById('searchBox').addEventListener('input', filterAndRender);
            document.getElementById('minRating').addEventListener('change', filterAndRender);
            document.getElementById('sortBy').addEventListener('change', filterAndRender);

            // Function to generate and show QR code
            let qrModalInstance = null;

            function generateQR(url) {
                const qrcodeDiv = document.getElementById('qrcode');
                qrcodeDiv.innerHTML = ''; // Clear previous QR

                if (typeof QRCode !== 'undefined') {
                    new QRCode(qrcodeDiv, {
                        text: url,
                        width: 256,
                        height: 256
                    });
                } else {
                    console.error('QRCode library not loaded');
                    qrcodeDiv.innerHTML = '<p class="text-danger">Error: Librería QR no disponible.</p>';
                }

                // Show modal (reuse instance)
                const modalEl = document.getElementById('qrModal');
                if (!qrModalInstance) {
                    qrModalInstance = new bootstrap.Modal(modalEl);
                }
                qrModalInstance.show();
            }

        })();
    </script>
</body>

</html>