<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1200, initial-scale=1.0, user-scalable=yes">
    <title>Panel Principal — Brixo Presentación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f1a;
            --bg-card: #111827;
            --bg-surface: #1e293b;
            --bg-hover: #263248;
            --border: rgba(52, 211, 153, 0.1);
            --border-active: rgba(52, 211, 153, 0.35);
            --emerald: #34d399;
            --emerald-dim: rgba(52, 211, 153, 0.12);
            --cyan: #22d3ee;
            --blue: #60a5fa;
            --purple: #a78bfa;
            --amber: #fbbf24;
            --red: #f87171;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --radius: 14px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            height: 100vh;
            overflow: hidden;
        }

        /* ═══════════════════════════════════════
           GRID LAYOUT — optimized for 11" tablet
           ═══════════════════════════════════════ */
        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            grid-template-rows: auto 1fr;
            gap: 0;
            height: 100vh;
            max-height: 100vh;
        }

        /* ── Top Bar ── */
        .top-bar {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1.2rem;
            background: rgba(11, 15, 26, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            z-index: 50;
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-mark {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--emerald), var(--cyan));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.85rem;
            color: #000;
        }

        .top-bar h1 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .top-bar h1 span {
            color: var(--emerald);
        }

        .status-pills {
            display: flex;
            gap: 0.6rem;
            align-items: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.7rem;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 600;
            border: 1px solid;
        }

        .pill-live {
            background: rgba(52, 211, 153, 0.08);
            border-color: rgba(52, 211, 153, 0.2);
            color: var(--emerald);
        }

        .pill-live .blink {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--emerald);
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        .pill-timer {
            background: rgba(96, 165, 250, 0.08);
            border-color: rgba(96, 165, 250, 0.2);
            color: var(--blue);
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            min-width: 70px;
            justify-content: center;
        }

        /* ── LEFT COLUMN: Slides + Controls ── */
        .left-col {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        /* Current slide preview */
        .current-slide-area {
            flex: 0 0 auto;
            padding: 1rem 1.2rem 0.6rem;
        }

        .slide-frame {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            border: 2px solid var(--border);
            background: #000;
            box-shadow: 0 8px 30px rgba(0,0,0,0.5);
        }

        .slide-frame img {
            width: 100%;
            display: block;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .slide-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px);
            color: #fff;
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* Slide controls */
        .slide-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            padding: 0.6rem 1.2rem;
        }

        .ctrl-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.55rem 1.4rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .ctrl-btn:hover { background: rgba(52,211,153,0.1); border-color: var(--border-active); }
        .ctrl-btn:active { transform: scale(0.96); }
        .ctrl-btn.disabled { opacity: 0.25; pointer-events: none; }

        .ctrl-btn-primary {
            background: linear-gradient(135deg, rgba(52,211,153,0.2), rgba(34,211,238,0.15));
            border-color: rgba(52,211,153,0.3);
        }

        .slide-counter-main {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary);
            min-width: 80px;
            text-align: center;
        }

        .slide-counter-main span { color: var(--text-muted); font-weight: 500; }

        /* ── Slide thumbnails strip ── */
        .thumb-strip {
            flex: 1;
            overflow-y: auto;
            padding: 0.4rem 1.2rem 1rem;
        }

        .thumb-strip::-webkit-scrollbar { width: 4px; }
        .thumb-strip::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }

        .thumb-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .thumb-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.25s ease;
            background: var(--bg-card);
        }

        .thumb-card img {
            width: 100%;
            display: block;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .thumb-card:hover {
            border-color: rgba(52,211,153,0.3);
            transform: scale(1.03);
        }

        .thumb-card.active {
            border-color: var(--emerald);
            box-shadow: 0 0 12px rgba(52,211,153,0.25);
        }

        .thumb-num {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: rgba(0,0,0,0.75);
            color: #fff;
            font-size: 0.55rem;
            font-weight: 700;
            padding: 0.1rem 0.35rem;
            border-radius: 5px;
        }

        /* ── RIGHT COLUMN: Quick Links ── */
        .right-col {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .right-col-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .right-col-header h2 {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
        }

        .links-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0.8rem 1rem;
        }

        .links-scroll::-webkit-scrollbar { width: 4px; }
        .links-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }

        /* Link group */
        .link-group {
            margin-bottom: 0.8rem;
        }

        .link-group-title {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            padding: 0.3rem 0.5rem;
        }

        .link-card {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 0.7rem;
            border-radius: 11px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
            -webkit-tap-highlight-color: transparent;
        }

        .link-card:hover {
            background: var(--bg-hover);
            border-color: var(--border);
        }

        .link-card:active {
            transform: scale(0.98);
        }

        .link-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .link-info {
            flex: 1;
            min-width: 0;
        }

        .link-info h4 {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.1rem;
        }

        .link-info p {
            font-size: 0.62rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link-arrow {
            color: var(--text-muted);
            font-size: 0.7rem;
            flex-shrink: 0;
            transition: transform 0.2s;
        }

        .link-card:hover .link-arrow {
            transform: translateX(3px);
            color: var(--emerald);
        }

        /* Project button at bottom */
        .project-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 0.5rem 0.8rem 0.8rem;
            padding: 0.65rem;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(52,211,153,0.15), rgba(34,211,238,0.1));
            border: 1px solid rgba(52,211,153,0.25);
            color: var(--emerald);
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .project-btn:hover {
            background: linear-gradient(135deg, rgba(52,211,153,0.25), rgba(34,211,238,0.18));
            border-color: rgba(52,211,153,0.4);
            color: var(--emerald);
        }

        /* ── Icon color helpers ── */
        .icon-emerald { background: rgba(52,211,153,0.12); color: var(--emerald); border: 1px solid rgba(52,211,153,0.15); }
        .icon-blue { background: rgba(96,165,250,0.12); color: var(--blue); border: 1px solid rgba(96,165,250,0.15); }
        .icon-purple { background: rgba(167,139,250,0.12); color: var(--purple); border: 1px solid rgba(167,139,250,0.15); }
        .icon-amber { background: rgba(251,191,36,0.12); color: var(--amber); border: 1px solid rgba(251,191,36,0.15); }
        .icon-red { background: rgba(248,113,113,0.12); color: var(--red); border: 1px solid rgba(248,113,113,0.15); }
        .icon-cyan { background: rgba(34,211,238,0.12); color: var(--cyan); border: 1px solid rgba(34,211,238,0.15); }

        /* ── Fullscreen button ── */
        .fs-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .fs-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }

        .pill-mode {
            background: rgba(167,139,250,0.08);
            border-color: rgba(167,139,250,0.2);
            color: var(--purple);
            transition: all 0.3s ease;
        }

        .pill-mode.mode-url {
            background: rgba(251,191,36,0.08);
            border-color: rgba(251,191,36,0.2);
            color: var(--amber);
        }

        .link-card.active-link {
            background: rgba(52,211,153,0.08);
            border-color: rgba(52,211,153,0.25);
        }

        .link-card.active-link .link-arrow {
            color: var(--emerald);
        }

        .back-slides-btn {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin: 0.4rem 0.8rem;
            padding: 0.55rem;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(167,139,250,0.15), rgba(96,165,250,0.1));
            border: 1px solid rgba(167,139,250,0.25);
            color: var(--purple);
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .back-slides-btn:hover {
            background: linear-gradient(135deg, rgba(167,139,250,0.25), rgba(96,165,250,0.18));
            border-color: rgba(167,139,250,0.4);
        }

        .back-slides-btn.visible {
            display: flex;
        }

        /* ── Projector overlay ── */
        .projector-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: rgba(251,191,36,0.1);
            border: 1px solid rgba(251,191,36,0.2);
            color: var(--amber);
        }
    </style>
</head>

<body>
    <div class="panel-grid">
        <!-- ═══ TOP BAR ═══ -->
        <div class="top-bar">
            <div class="top-bar-left">
                <div class="logo-mark">B</div>
                <h1>Panel <span>Principal</span></h1>
            </div>
            <div class="status-pills">
                <div class="pill pill-live"><span class="blink"></span> En vivo</div>
                <div class="pill pill-mode" id="mode-pill">
                    <i class="fas fa-image"></i> <span id="mode-label">Slides</span>
                </div>
                <div class="pill pill-timer" id="timer"><i class="fas fa-clock"></i> 00:00</div>
                <button class="fs-btn" onclick="toggleFullscreen()" title="Pantalla completa">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>

        <!-- ═══ LEFT COLUMN ═══ -->
        <div class="left-col">
            <!-- Current Slide -->
            <div class="current-slide-area">
                <div class="slide-frame">
                    <img id="main-slide-img" src="/presentation/Slide1.PNG" alt="Slide actual">
                    <div class="slide-label"><i class="fas fa-tv"></i> Proyección actual</div>
                </div>
            </div>

            <!-- Controls -->
            <div class="slide-controls">
                <button class="ctrl-btn" id="btn-first" onclick="goToSlide(1)">
                    <i class="fas fa-angles-left"></i>
                </button>
                <button class="ctrl-btn ctrl-btn-primary" id="btn-prev" onclick="changeSlide(-1)">
                    <i class="fas fa-chevron-left"></i> Anterior
                </button>
                <div class="slide-counter-main">
                    <span id="slide-num">1</span> <span>/ <?= $totalSlides ?></span>
                </div>
                <button class="ctrl-btn ctrl-btn-primary" id="btn-next" onclick="changeSlide(1)">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </button>
                <button class="ctrl-btn" id="btn-last" onclick="goToSlide(<?= $totalSlides ?>)">
                    <i class="fas fa-angles-right"></i>
                </button>
            </div>

            <!-- Thumbnail Strip -->
            <div class="thumb-strip">
                <div class="thumb-grid" id="thumb-grid">
                    <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
                        <div class="thumb-card <?= $i === 1 ? 'active' : '' ?>" data-slide="<?= $i ?>" onclick="goToSlide(<?= $i ?>)">
                            <img src="/presentation/Slide<?= $i ?>.PNG" alt="Slide <?= $i ?>" loading="lazy">
                            <div class="thumb-num"><?= $i ?></div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- ═══ RIGHT COLUMN ═══ -->
        <div class="right-col">
            <div class="right-col-header">
                <h2><i class="fas fa-tv"></i> &nbsp;Proyectar en /demo</h2>
            </div>

            <!-- Back to slides button -->
            <button class="back-slides-btn" id="btn-back-slides" onclick="backToSlides()">
                <i class="fas fa-images"></i> Volver a Diapositivas
            </button>

            <div class="links-scroll">
                <!-- Demo del proyecto -->
                <div class="link-group">
                    <div class="link-group-title">🖥️ Demo en vivo</div>

                    <a class="link-card" href="#" data-url="/" onclick="projectUrl('/', this); return false;">
                        <div class="link-icon icon-emerald"><i class="fas fa-home"></i></div>
                        <div class="link-info">
                            <h4>Página Principal</h4>
                            <p>Landing page de Brixo con hero y categorías</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/map" onclick="projectUrl('/map', this); return false;">
                        <div class="link-icon icon-blue"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="link-info">
                            <h4>Mapa Interactivo</h4>
                            <p>Geolocalización de contratistas</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/especialidades" onclick="projectUrl('/especialidades', this); return false;">
                        <div class="link-icon icon-purple"><i class="fas fa-tools"></i></div>
                        <div class="link-info">
                            <h4>Especialidades</h4>
                            <p>Catálogo de servicios y categorías</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/cotizador" onclick="projectUrl('/cotizador', this); return false;">
                        <div class="link-icon icon-amber"><i class="fas fa-robot"></i></div>
                        <div class="link-info">
                            <h4>Cotizador IA</h4>
                            <p>Cotización inteligente con LLM</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/login" onclick="projectUrl('/login', this); return false;">
                        <div class="link-icon icon-cyan"><i class="fas fa-sign-in-alt"></i></div>
                        <div class="link-info">
                            <h4>Login / Registro</h4>
                            <p>Flujo de autenticación</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>

                <!-- Paneles -->
                <div class="link-group">
                    <div class="link-group-title">👤 Paneles de usuario</div>

                    <a class="link-card" href="#" data-url="/panel" onclick="projectUrl('/panel', this); return false;">
                        <div class="link-icon icon-emerald"><i class="fas fa-columns"></i></div>
                        <div class="link-info">
                            <h4>Panel de Control</h4>
                            <p>Dashboard del usuario logueado</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/solicitudes" onclick="projectUrl('/solicitudes', this); return false;">
                        <div class="link-icon icon-blue"><i class="fas fa-clipboard-list"></i></div>
                        <div class="link-info">
                            <h4>Solicitudes</h4>
                            <p>Gestión de solicitudes de servicio</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/mensajes" onclick="projectUrl('/mensajes', this); return false;">
                        <div class="link-icon icon-purple"><i class="fas fa-comments"></i></div>
                        <div class="link-info">
                            <h4>Mensajería</h4>
                            <p>Chat entre clientes y contratistas</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/perfil" onclick="projectUrl('/perfil', this); return false;">
                        <div class="link-icon icon-cyan"><i class="fas fa-user-circle"></i></div>
                        <div class="link-info">
                            <h4>Perfil</h4>
                            <p>Edición de perfil de usuario</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>

                <!-- Presentación & Docs -->
                <div class="link-group">
                    <div class="link-group-title">📊 Presentación</div>

                    <a class="link-card" href="#" data-url="/showcase" onclick="projectUrl('/showcase', this); return false;">
                        <div class="link-icon icon-amber"><i class="fas fa-star"></i></div>
                        <div class="link-info">
                            <h4>Showcase</h4>
                            <p>Página de recursos y documentación</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>

                    <a class="link-card" href="#" data-url="/reportes/contratistas" onclick="projectUrl('/reportes/contratistas', this); return false;">
                        <div class="link-icon icon-blue"><i class="fas fa-chart-bar"></i></div>
                        <div class="link-info">
                            <h4>Reportes</h4>
                            <p>Estadísticas de contratistas</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                </div>

                <!-- Recursos externos (abren en nueva pestaña, no en iframe por seguridad cross-origin) -->
                <div class="link-group">
                    <div class="link-group-title">🔗 Recursos externos</div>

                    <a class="link-card" href="https://github.com/mikerb95/BrixoCI4" target="_blank" rel="noopener">
                        <div class="link-icon icon-purple"><i class="fab fa-github"></i></div>
                        <div class="link-info">
                            <h4>Repositorio GitHub</h4>
                            <p>Código fuente del proyecto</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-external-link-alt"></i></div>
                    </a>

                    <a class="link-card" href="https://dashboard.render.com" target="_blank" rel="noopener">
                        <div class="link-icon icon-emerald"><i class="fas fa-cloud"></i></div>
                        <div class="link-info">
                            <h4>Render Dashboard</h4>
                            <p>Panel de despliegue</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-external-link-alt"></i></div>
                    </a>

                    <a class="link-card" href="https://console.aiven.io" target="_blank" rel="noopener">
                        <div class="link-icon icon-blue"><i class="fas fa-database"></i></div>
                        <div class="link-info">
                            <h4>Aiven Console</h4>
                            <p>Base de datos MySQL</p>
                        </div>
                        <div class="link-arrow"><i class="fas fa-external-link-alt"></i></div>
                    </a>
                </div>
            </div>

            <!-- Bottom CTA -->
            <a class="project-btn" href="/demo" target="_blank" rel="noopener">
                <i class="fas fa-tv"></i> Abrir /demo en Proyector
            </a>
        </div>
    </div>

    <script>
        let currentSlide = 1;
        let currentDemoMode = 'slides'; // 'slides' or 'url'
        let currentProjectedUrl = '';
        const totalSlides = <?= $totalSlides ?>;
        let timerSeconds = 0;
        let timerRunning = false;

        const modePill  = document.getElementById('mode-pill');
        const modeLabel = document.getElementById('mode-label');
        const backBtn   = document.getElementById('btn-back-slides');

        // ── Timer ──
        function startTimer() {
            if (timerRunning) return;
            timerRunning = true;
            setInterval(() => {
                timerSeconds++;
                const m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
                const s = String(timerSeconds % 60).padStart(2, '0');
                document.getElementById('timer').innerHTML = `<i class="fas fa-clock"></i> ${m}:${s}`;
            }, 1000);
        }

        document.addEventListener('click', () => startTimer(), { once: true });
        document.addEventListener('touchstart', () => startTimer(), { once: true });

        function updateModeUI() {
            if (currentDemoMode === 'url') {
                modePill.classList.add('mode-url');
                modeLabel.textContent = currentProjectedUrl || 'URL';
                modePill.querySelector('i').className = 'fas fa-globe';
                backBtn.classList.add('visible');
            } else {
                modePill.classList.remove('mode-url');
                modeLabel.textContent = `Slide ${currentSlide}`;
                modePill.querySelector('i').className = 'fas fa-image';
                backBtn.classList.remove('visible');
            }

            // Highlight active link
            document.querySelectorAll('.link-card[data-url]').forEach(card => {
                card.classList.toggle('active-link',
                    currentDemoMode === 'url' && card.dataset.url === currentProjectedUrl);
            });
        }

        function updateUI() {
            // Main image
            document.getElementById('main-slide-img').src = `/presentation/Slide${currentSlide}.PNG`;
            document.getElementById('slide-num').textContent = currentSlide;

            // Thumbnails
            document.querySelectorAll('.thumb-card').forEach(card => {
                card.classList.toggle('active', parseInt(card.dataset.slide) === currentSlide);
            });

            // Scroll active thumb into view
            const activeThumb = document.querySelector('.thumb-card.active');
            if (activeThumb) {
                activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // Button states
            document.getElementById('btn-prev').classList.toggle('disabled', currentSlide <= 1);
            document.getElementById('btn-first').classList.toggle('disabled', currentSlide <= 1);
            document.getElementById('btn-next').classList.toggle('disabled', currentSlide >= totalSlides);
            document.getElementById('btn-last').classList.toggle('disabled', currentSlide >= totalSlides);

            updateModeUI();
        }

        function changeSlide(direction) {
            const newSlide = currentSlide + direction;
            if (newSlide < 1 || newSlide > totalSlides) return;
            goToSlide(newSlide);
        }

        function goToSlide(num) {
            num = Math.max(1, Math.min(totalSlides, num));
            if (navigator.vibrate) navigator.vibrate(25);

            // If we're in URL mode, switch back to slides first
            if (currentDemoMode === 'url') {
                fetch('/api/demo', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mode: 'slides' })
                });
                currentDemoMode = 'slides';
                currentProjectedUrl = '';
            }

            fetch('/api/slide', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ slide: num })
            })
            .then(r => r.json())
            .then(data => {
                currentSlide = data.slide;
                updateUI();
            });
        }

        // ── Project a URL into the /demo iframe ──
        function projectUrl(url, linkEl) {
            if (navigator.vibrate) navigator.vibrate(25);

            fetch('/api/demo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mode: 'url', url: url })
            })
            .then(r => r.json())
            .then(data => {
                currentDemoMode = 'url';
                currentProjectedUrl = url;
                updateModeUI();
            });
        }

        // ── Back to slides ──
        function backToSlides() {
            if (navigator.vibrate) navigator.vibrate(25);

            fetch('/api/demo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mode: 'slides' })
            })
            .then(r => r.json())
            .then(() => {
                currentDemoMode = 'slides';
                currentProjectedUrl = '';
                updateUI();
            });
        }

        // ── Initial state ──
        Promise.all([
            fetch('/api/slide').then(r => r.json()),
            fetch('/api/demo').then(r => r.json())
        ]).then(([slideData, demoData]) => {
            currentSlide = slideData.slide;
            currentDemoMode = demoData.mode || 'slides';
            currentProjectedUrl = demoData.url || '';
            updateUI();
        });

        // ── Poll ──
        setInterval(() => {
            Promise.all([
                fetch('/api/slide').then(r => r.json()),
                fetch('/api/demo').then(r => r.json())
            ]).then(([slideData, demoData]) => {
                let changed = false;
                if (slideData.slide !== currentSlide) {
                    currentSlide = slideData.slide;
                    changed = true;
                }
                const newMode = demoData.mode || 'slides';
                const newUrl = demoData.url || '';
                if (newMode !== currentDemoMode || newUrl !== currentProjectedUrl) {
                    currentDemoMode = newMode;
                    currentProjectedUrl = newUrl;
                    changed = true;
                }
                if (changed) updateUI();
            });
        }, 1200);

        // ── Keyboard shortcuts ──
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); changeSlide(1); }
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); changeSlide(-1); }
            if (e.key === 'Home') { e.preventDefault(); goToSlide(1); }
            if (e.key === 'End') { e.preventDefault(); goToSlide(totalSlides); }
            if (e.key === 'Escape' && currentDemoMode === 'url') { e.preventDefault(); backToSlides(); }
        });

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen();
            }
        }
    </script>
</body>

</html>
