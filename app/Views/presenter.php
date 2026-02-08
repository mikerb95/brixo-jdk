<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <title>Notas del Presentador — Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0b0f1a;
            --bg-card: #111827;
            --bg-surface: #1e293b;
            --border: rgba(52, 211, 153, 0.12);
            --emerald: #34d399;
            --cyan: #22d3ee;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }

        /* ── Sticky Header ── */
        .presenter-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(11, 15, 26, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0.6rem 1rem;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
        }

        .slide-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: linear-gradient(135deg, rgba(52,211,153,0.15), rgba(34,211,238,0.1));
            border: 1px solid rgba(52,211,153,0.2);
            border-radius: 20px;
            padding: 0.3rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--emerald);
        }

        .slide-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .header-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        /* ── Slide card (current) ── */
        .current-slide-card {
            max-width: 600px;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        .slide-img-wrapper {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }

        .slide-img-wrapper img {
            width: 100%;
            display: block;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .slide-number-overlay {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(6px);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 8px;
        }

        /* ── Notes section ── */
        .notes-section {
            max-width: 600px;
            margin: 0 auto;
            padding: 0.75rem 1rem 1rem;
        }

        .notes-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--emerald);
            margin-bottom: 0.5rem;
        }

        .notes-content {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.85rem 1rem;
        }

        .notes-content h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .notes-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .notes-content ul li {
            position: relative;
            padding-left: 1.1rem;
            margin-bottom: 0.45rem;
            font-size: 0.82rem;
            line-height: 1.45;
            color: var(--text-secondary);
        }

        .notes-content ul li::before {
            content: '▸';
            position: absolute;
            left: 0;
            color: var(--emerald);
            font-weight: 700;
        }

        .key-phrase {
            display: inline;
            color: var(--cyan);
            font-weight: 600;
        }

        .demo-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .time-hint {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.6rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .time-hint i { color: var(--emerald); }

        /* ── Mini nav controls at bottom ── */
        .nav-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(11,15,26,0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            padding: 0.5rem 1rem;
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 600px;
            margin: 0 auto;
            gap: 0.5rem;
        }

        .nav-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.7rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: var(--text-primary);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .nav-btn:active {
            transform: scale(0.96);
            background: rgba(52,211,153,0.12);
        }

        .nav-btn.disabled {
            opacity: 0.3;
            pointer-events: none;
        }

        .progress-bar-container {
            flex: 0.6;
            text-align: center;
        }

        .progress-dots {
            display: flex;
            justify-content: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .progress-dots .pdot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            transition: all 0.3s ease;
        }

        .progress-dots .pdot.active {
            background: var(--emerald);
            box-shadow: 0 0 6px rgba(52,211,153,0.4);
        }

        .progress-dots .pdot.past {
            background: rgba(52,211,153,0.35);
        }

        /* ── Bottom padding so content doesn't hide behind nav ── */
        .bottom-spacer { height: 5rem; }

        /* ── Next slide preview ── */
        .next-preview {
            max-width: 600px;
            margin: 0.75rem auto 0;
            padding: 0 1rem;
        }

        .next-preview-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .next-preview-thumb {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
            opacity: 0.5;
            transition: opacity 0.3s;
        }

        .next-preview-thumb img {
            width: 100%;
            display: block;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        /* Timer */
        .timer-display {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- ═══ STICKY HEADER ═══ -->
    <div class="presenter-header">
        <div class="header-inner">
            <div class="slide-badge">
                <span class="dot"></span>
                <span>Slide <span id="header-slide-num">1</span> / <?= $totalSlides ?></span>
            </div>
            <div class="timer-display" id="timer">00:00</div>
            <div class="header-title">📋 Notas</div>
        </div>
    </div>

    <!-- ═══ CURRENT SLIDE IMAGE ═══ -->
    <div class="current-slide-card">
        <div class="slide-img-wrapper">
            <img id="current-slide-img" src="/presentation/Slide1.PNG" alt="Slide actual">
            <div class="slide-number-overlay" id="slide-overlay-num">1 / <?= $totalSlides ?></div>
        </div>
    </div>

    <!-- ═══ PRESENTER NOTES ═══ -->
    <div class="notes-section">
        <div class="notes-label">
            <i class="fas fa-sticky-note"></i>
            Notas del presentador
        </div>
        <div class="notes-content" id="notes-container">
            <!-- Filled by JS -->
        </div>
    </div>

    <!-- ═══ NEXT SLIDE PREVIEW ═══ -->
    <div class="next-preview">
        <div class="next-preview-label">Siguiente diapositiva ▸</div>
        <div class="next-preview-thumb">
            <img id="next-slide-img" src="/presentation/Slide2.PNG" alt="Siguiente slide">
        </div>
    </div>

    <div class="bottom-spacer"></div>

    <!-- ═══ BOTTOM NAV ═══ -->
    <div class="nav-controls">
        <div class="nav-inner">
            <button class="nav-btn" id="btn-prev" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i> Ant
            </button>
            <div class="progress-bar-container">
                <div class="progress-dots" id="progress-dots">
                    <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
                        <span class="pdot <?= $i === 1 ? 'active' : '' ?>" data-slide="<?= $i ?>"></span>
                    <?php endfor; ?>
                </div>
            </div>
            <button class="nav-btn" id="btn-next" onclick="changeSlide(1)">
                Sig <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <script>
        // ── Presenter Notes Data ──
        const presenterNotes = {
            1: {
                title: "Portada — Bienvenida",
                points: [
                    "Saludar al público y presentarse con nombre completo",
                    "<span class='key-phrase'>Brixo</span> es una plataforma que conecta clientes con contratistas de servicios de construcción y remodelación",
                    "Mencionar que es un proyecto académico con <span class='key-phrase'>tecnología de nivel profesional</span>",
                    "Agradecer al profesor y compañeros por su tiempo"
                ],
                time: "~1 min"
            },
            2: {
                title: "Problema & Oportunidad",
                points: [
                    "Explicar el <span class='key-phrase'>problema real</span>: dificultad para encontrar contratistas confiables",
                    "Falta de transparencia en precios y calificaciones",
                    "Mercado fragmentado: el cliente no sabe a quién contratar",
                    "La oportunidad: una plataforma digital que centralice oferta y demanda"
                ],
                time: "~2 min"
            },
            3: {
                title: "Solución — ¿Qué es Brixo?",
                points: [
                    "Brixo = <span class='key-phrase'>marketplace digital</span> de servicios de construcción",
                    "Los clientes publican solicitudes de trabajo",
                    "Los contratistas verificados responden con cotizaciones",
                    "Sistema de mensajería interna para comunicación directa",
                    "Mencionar: registro dual (cliente / contratista)"
                ],
                time: "~2 min"
            },
            4: {
                title: "Arquitectura del Sistema",
                points: [
                    "Stack: <span class='key-phrase'>CodeIgniter 4</span> (PHP 8.2) como backend MVC",
                    "Base de datos: <span class='key-phrase'>MySQL en Aiven</span> (DBaaS en la nube)",
                    "Despliegue: <span class='key-phrase'>Render</span> con Docker, deploy automático desde GitHub",
                    "Almacenamiento de archivos: <span class='key-phrase'>AWS S3 + CloudFront</span>",
                    "Explicar que cada push a master dispara un deploy"
                ],
                time: "~2 min"
            },
            5: {
                title: "Funcionalidades Principales",
                points: [
                    "Registro e inicio de sesión con roles diferenciados",
                    "Panel de cliente: crear solicitudes, ver respuestas, chat",
                    "Panel de contratista: tablón de tareas, perfil profesional",
                    "<span class='key-phrase'>Cotizador inteligente con IA</span> (LLM integrado)",
                    "Sistema de mensajería en tiempo real con polling",
                    "Mapa interactivo de contratistas"
                ],
                time: "~2 min"
            },
            6: {
                title: "Base de Datos & Modelos",
                points: [
                    "Diagrama ER: tablas principales (clientes, contratistas, solicitudes, mensajes, categorías)",
                    "Uso de <span class='key-phrase'>CodeIgniter Models</span> con validación integrada",
                    "Relaciones: contratista ↔ servicios ↔ categorías",
                    "Migraciones y Seeds para desarrollo reproducible"
                ],
                time: "~1.5 min"
            },
            7: {
                title: "Seguridad & Filtros",
                points: [
                    "Autenticación basada en <span class='key-phrase'>sesiones PHP</span>",
                    "Filtro de autenticación (<code>AuthFilter</code>) para rutas protegidas",
                    "CSRF protection activado en formularios",
                    "Validación server-side en cada endpoint",
                    "Conexión SSL a la base de datos"
                ],
                time: "~1.5 min"
            },
            8: {
                title: "CI/CD Pipeline",
                points: [
                    "Flujo: <span class='key-phrase'>GitHub → Render → Docker Build → Deploy</span>",
                    "Dockerfile multi-stage con PHP + Apache",
                    "Variables de entorno seguras en Render dashboard",
                    "Zero-downtime deploys",
                    "<span class='demo-tag'><i class='fas fa-play-circle'></i> Demo</span> Mostrar un push en vivo si es posible"
                ],
                time: "~1.5 min"
            },
            9: {
                title: "Demo en Vivo",
                points: [
                    "<span class='demo-tag'><i class='fas fa-play-circle'></i> Demo en vivo</span>",
                    "Navegar la página principal y mostrar el mapa",
                    "Registrar un usuario de prueba o usar uno existente",
                    "Crear una solicitud de servicio",
                    "Mostrar el panel del contratista y el chat",
                    "Usar el <span class='key-phrase'>cotizador inteligente con IA</span>",
                    "Tener lista una cuenta de respaldo por si falla el registro"
                ],
                time: "~3 min"
            },
            10: {
                title: "Resultados & Aprendizajes",
                points: [
                    "Mencionar métricas: número de rutas, modelos, vistas creadas",
                    "Aprendizajes clave: despliegue en la nube, Docker, MVC real",
                    "Retos enfrentados: configuración de DB remota, CORS, sesiones",
                    "Importancia de la <span class='key-phrase'>automatización del despliegue</span>"
                ],
                time: "~1.5 min"
            },
            11: {
                title: "Cierre & Preguntas",
                points: [
                    "Resumir los 3 puntos clave del proyecto",
                    "Próximos pasos: pagos en línea, notificaciones push, app móvil",
                    "Agradecer al público por su atención",
                    "Abrir espacio para <span class='key-phrase'>preguntas y respuestas</span>",
                    "Tener preparadas respuestas para: ¿por qué CodeIgniter?, ¿por qué no Laravel?, ¿cómo escala?"
                ],
                time: "~2 min"
            }
        };

        let currentSlide = 1;
        const totalSlides = <?= $totalSlides ?>;
        let timerSeconds = 0;
        let timerInterval = null;

        // ── Timer ──
        function startTimer() {
            if (timerInterval) return;
            timerInterval = setInterval(() => {
                timerSeconds++;
                const m = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
                const s = String(timerSeconds % 60).padStart(2, '0');
                document.getElementById('timer').textContent = m + ':' + s;
            }, 1000);
        }

        // Start timer on first interaction
        document.addEventListener('click', () => startTimer(), { once: true });
        document.addEventListener('touchstart', () => startTimer(), { once: true });

        function renderNotes(slideNum) {
            const note = presenterNotes[slideNum];
            const container = document.getElementById('notes-container');
            if (!note) {
                container.innerHTML = '<p style="color:var(--text-muted);font-size:0.85rem;">Sin notas para esta diapositiva.</p>';
                return;
            }
            let html = `<h4>${note.title}</h4><ul>`;
            note.points.forEach(p => { html += `<li>${p}</li>`; });
            html += '</ul>';
            html += `<div class="time-hint"><i class="fas fa-clock"></i> Tiempo sugerido: ${note.time}</div>`;
            container.innerHTML = html;
        }

        function updateUI() {
            // Image
            document.getElementById('current-slide-img').src = `/presentation/Slide${currentSlide}.PNG`;
            document.getElementById('header-slide-num').textContent = currentSlide;
            document.getElementById('slide-overlay-num').textContent = `${currentSlide} / ${totalSlides}`;

            // Next preview
            const nextImg = document.getElementById('next-slide-img');
            if (currentSlide < totalSlides) {
                nextImg.parentElement.parentElement.style.display = '';
                nextImg.src = `/presentation/Slide${currentSlide + 1}.PNG`;
            } else {
                nextImg.parentElement.parentElement.style.display = 'none';
            }

            // Notes
            renderNotes(currentSlide);

            // Progress dots
            document.querySelectorAll('.pdot').forEach((dot, i) => {
                dot.classList.remove('active', 'past');
                if (i + 1 === currentSlide) dot.classList.add('active');
                else if (i + 1 < currentSlide) dot.classList.add('past');
            });

            // Button states
            document.getElementById('btn-prev').classList.toggle('disabled', currentSlide <= 1);
            document.getElementById('btn-next').classList.toggle('disabled', currentSlide >= totalSlides);
        }

        function changeSlide(direction) {
            const newSlide = currentSlide + direction;
            if (newSlide < 1 || newSlide > totalSlides) return;

            if (navigator.vibrate) navigator.vibrate(30);

            fetch('/api/slide', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ slide: newSlide })
            })
            .then(r => r.json())
            .then(data => {
                currentSlide = data.slide;
                updateUI();
            });
        }

        // ── Initial load ──
        fetch('/api/slide')
            .then(r => r.json())
            .then(data => { currentSlide = data.slide; updateUI(); });

        // ── Poll for external changes ──
        setInterval(() => {
            fetch('/api/slide')
                .then(r => r.json())
                .then(data => {
                    if (data.slide !== currentSlide) {
                        currentSlide = data.slide;
                        updateUI();
                    }
                });
        }, 1500);

        // ── Swipe gestures ──
        let touchStartX = 0;
        document.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        document.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 60) {
                changeSlide(diff > 0 ? 1 : -1);
            }
        });

        // Initial render
        renderNotes(1);
    </script>
</body>

</html>
