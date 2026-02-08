<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotizador Inteligente - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        .cotizador-hero {
            background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
        }
        .result-card { display: none; }
        .result-card.show { display: block; }
        .badge-complejidad-bajo  { background-color: #198754; }
        .badge-complejidad-medio { background-color: #fd7e14; }
        .badge-complejidad-alto  { background-color: #dc3545; }
        #spinner { display: none; }
        #spinner.active { display: inline-block; }
        .example-chip {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .example-chip:hover {
            background-color: #485166 !important;
            color: #fff !important;
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="bg-light">
    <?= view('partials/navbar') ?>

    <!-- Hero -->
    <section class="cotizador-hero text-white py-5">
        <div class="container text-center" style="max-width: 720px;">
            <h1 class="display-5 fw-bold mb-3"><i class="fas fa-robot me-2"></i>Cotizador Inteligente</h1>
            <p class="lead mb-0 opacity-75">Describe lo que necesitas y nuestra IA generará una cotización desglosada al instante.</p>
        </div>
    </section>

    <div class="container py-5" style="max-width: 800px;">

        <!-- Formulario -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <form id="cotizadorForm" method="post" action="/cotizador/generar">
                    <?= csrf_field() ?>

                    <label for="descripcion" class="form-label fw-bold fs-5 mb-3">
                        <i class="fas fa-pencil-alt text-primary me-2"></i>¿Qué servicio necesitas?
                    </label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="form-control form-control-lg rounded-3 mb-3"
                        rows="4"
                        placeholder="Ej: Quiero remodelar mi baño, cambiar 4 baldosas y arreglar el grifo que gotea"
                        required
                        minlength="10"
                        maxlength="2000"
                    ><?= esc($descripcion ?? '') ?></textarea>

                    <div class="mb-4">
                        <small class="text-muted">Ejemplos rápidos:</small>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge bg-light text-dark border example-chip" data-text="Quiero remodelar mi baño, cambiar 4 baldosas y arreglar el grifo que gotea">🛁 Remodelación baño</span>
                            <span class="badge bg-light text-dark border example-chip" data-text="Reparar una pared con humedad de 2 metros cuadrados">🧱 Pared con humedad</span>
                            <span class="badge bg-light text-dark border example-chip" data-text="Pintar 3 habitaciones de 12 metros cuadrados cada una">🎨 Pintar habitaciones</span>
                            <span class="badge bg-light text-dark border example-chip" data-text="Instalar 6 puntos de luz LED en el techo del salón">💡 Instalación eléctrica</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 w-100" id="btnGenerar">
                        <span id="spinner" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="fas fa-magic me-2" id="iconMagic"></i>Generar Cotización
                    </button>
                </form>
            </div>
        </div>

        <!-- Error -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-3 d-flex align-items-center" id="alertError">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="errorText"><?= esc($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Resultado (server-side render si hay datos) -->
        <?php if (!empty($cotizacion)): ?>
            <div class="result-card show" id="resultCard">
                <?= $this->include('cotizador_resultado') ?>
            </div>
        <?php else: ?>
            <div class="result-card" id="resultCard"></div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?= view('partials/footer') ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form       = document.getElementById('cotizadorForm');
        const textarea   = document.getElementById('descripcion');
        const btnGenerar = document.getElementById('btnGenerar');
        const spinner    = document.getElementById('spinner');
        const iconMagic  = document.getElementById('iconMagic');
        const resultCard = document.getElementById('resultCard');
        const alertError = document.getElementById('alertError');

        // CSRF token para forms dinámicos
        const csrfName  = '<?= csrf_token() ?>';
        const csrfValue = '<?= csrf_hash() ?>';
        const isLoggedIn = <?= !empty(session()->get('user')) ? 'true' : 'false' ?>;

        // Inyectar redirect_to al modal de login para que vuelva al cotizador
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            loginModal.addEventListener('show.bs.modal', function () {
                const loginForm = loginModal.querySelector('form');
                if (loginForm && !loginForm.querySelector('input[name="redirect_to"]')) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'redirect_to';
                    hidden.value = '/cotizador';
                    loginForm.appendChild(hidden);
                }
            });
        }

        // Chips de ejemplo
        document.querySelectorAll('.example-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                textarea.value = chip.dataset.text;
                textarea.focus();
            });
        });

        // Envío AJAX
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (alertError) alertError.style.display = 'none';

            // UI: loading
            btnGenerar.disabled = true;
            spinner.classList.add('active');
            iconMagic.style.display = 'none';
            btnGenerar.querySelector('.spinner-border').style.display = 'inline-block';

            const formData = new FormData(form);

            fetch('/cotizador/generar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
            .then(r => r.json())
            .then(json => {
                if (json.ok) {
                    resultCard.innerHTML = renderCotizacion(json.data);
                    resultCard.classList.add('show');
                    resultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    showError(json.error || 'Error desconocido.');
                }
            })
            .catch(() => showError('Error de conexión. Intenta de nuevo.'))
            .finally(() => {
                btnGenerar.disabled = false;
                spinner.classList.remove('active');
                spinner.style.display = 'none';
                iconMagic.style.display = 'inline-block';
            });
        });

        function showError(msg) {
            if (alertError) {
                document.getElementById('errorText').textContent = msg;
                alertError.style.display = 'flex';
            } else {
                const div = document.createElement('div');
                div.className = 'alert alert-danger rounded-3 d-flex align-items-center';
                div.id = 'alertError';
                div.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i><span id="errorText">${escHtml(msg)}</span>`;
                resultCard.parentElement.insertBefore(div, resultCard);
            }
        }

        function escHtml(s) {
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function badgeComplejidad(nivel) {
            const cls = { bajo: 'badge-complejidad-bajo', medio: 'badge-complejidad-medio', alto: 'badge-complejidad-alto' };
            const icons = { bajo: 'check-circle', medio: 'exclamation-circle', alto: 'exclamation-triangle' };
            return `<span class="badge ${cls[nivel] || 'bg-secondary'} rounded-pill px-3 py-2">
                        <i class="fas fa-${icons[nivel] || 'info-circle'} me-1"></i>${nivel.charAt(0).toUpperCase() + nivel.slice(1)}
                    </span>`;
        }

        function renderCotizacion(d) {
            let materialesRows = d.materiales.map(m =>
                `<tr><td><i class="fas fa-box text-muted me-2"></i>${escHtml(m.nombre)}</td>
                     <td class="text-end fw-semibold">${escHtml(m.cantidad_estimada)}</td></tr>`
            ).join('');

            let personalRows = d.personal.map(p =>
                `<tr><td><i class="fas fa-hard-hat text-muted me-2"></i>${escHtml(p.rol)}</td>
                     <td class="text-end fw-semibold">${p.horas_estimadas} hrs</td></tr>`
            ).join('');

            return `
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>Cotización Estimada</h5>
                    ${badgeComplejidad(d.complejidad)}
                </div>
                <div class="card-body p-4">
                    <!-- Servicio principal -->
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <small class="text-muted text-uppercase fw-bold">Servicio Principal</small>
                        <h4 class="fw-bold mb-0 mt-1">${escHtml(d.servicio_principal)}</h4>
                    </div>

                    <!-- Materiales -->
                    <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-tools me-2"></i>Materiales</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless mb-0">
                            <thead><tr><th>Material</th><th class="text-end">Cantidad</th></tr></thead>
                            <tbody>${materialesRows}</tbody>
                        </table>
                    </div>

                    <!-- Personal -->
                    <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-users me-2"></i>Personal</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead><tr><th>Rol</th><th class="text-end">Horas Est.</th></tr></thead>
                            <tbody>${personalRows}</tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <small class="text-muted d-block text-center mb-3"><i class="fas fa-info-circle me-1"></i>Esta es una estimación generada por IA. Los valores reales pueden variar.</small>
                    ${isLoggedIn
                        ? `<form action="/cotizador/confirmar" method="post" id="formConfirmar">
                            <input type="hidden" name="${csrfName}" value="${csrfValue}">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill w-100 fw-bold">
                                <i class="fas fa-clipboard-list me-2"></i>Crear Solicitud de Servicio
                            </button>
                           </form>`
                        : `<a href="#" class="btn btn-outline-primary btn-lg rounded-pill w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para continuar
                           </a>`
                    }
                </div>
            </div>`;
        }
    });
    </script>
</body>

</html>
