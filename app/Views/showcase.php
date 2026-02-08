<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showcase — Brixo</title>

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwindcss.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { inter: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        emerald: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                        },
                        surface: {
                            900: '#0b0f1a',
                            800: '#111827',
                            700: '#1e293b',
                            600: '#334155',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome for fallback icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ---- Base ---- */
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0b0f1a;
            color: #e2e8f0;
        }

        /* ---- Animated gradient mesh background ---- */
        .hero-mesh {
            position: relative;
            overflow: hidden;
        }

        .hero-mesh::before {
            content: '';
            position: absolute;
            inset: -40%;
            background:
                radial-gradient(ellipse 600px 400px at 20% 30%, rgba(16, 185, 129, 0.12) 0%, transparent 70%),
                radial-gradient(ellipse 500px 500px at 80% 60%, rgba(59, 130, 246, 0.10) 0%, transparent 70%),
                radial-gradient(ellipse 400px 300px at 50% 80%, rgba(139, 92, 246, 0.06) 0%, transparent 70%);
            animation: meshFloat 18s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes meshFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, 20px) scale(1.05); }
            100% { transform: translate(20px, -15px) scale(1); }
        }

        /* ---- Noise texture overlay ---- */
        .noise-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            pointer-events: none;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* ---- Card glow border ---- */
        .card-glow {
            position: relative;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(17, 24, 39, 0.9) 100%);
            border: 1px solid rgba(52, 211, 153, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-glow::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(52, 211, 153, 0), rgba(59, 130, 246, 0));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            transition: background 0.4s ease;
            pointer-events: none;
        }

        .card-glow:hover {
            transform: translateY(-6px);
            border-color: rgba(52, 211, 153, 0.25);
            box-shadow:
                0 0 30px rgba(52, 211, 153, 0.08),
                0 20px 60px -12px rgba(0, 0, 0, 0.5);
        }

        .card-glow:hover::before {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.4), rgba(59, 130, 246, 0.3));
        }

        /* ---- Icon containers ---- */
        .icon-ring {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-glow:hover .icon-ring {
            transform: scale(1.1) rotate(-3deg);
            box-shadow: 0 0 20px rgba(52, 211, 153, 0.2);
        }

        /* ---- Slide Previews ---- */
        .slide-thumb {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .slide-thumb:hover {
            border-color: rgba(52, 211, 153, 0.5);
            transform: scale(1.04);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        /* ---- Divider glow ---- */
        .divider-glow {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(52, 211, 153, 0.3) 50%, transparent 100%);
        }

        /* ---- Btn shine ---- */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }

        .btn-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            transform: skewX(-25deg);
            transition: left 0.6s ease;
        }

        .btn-shine:hover::after {
            left: 120%;
        }

        /* ---- Scrollbar ---- */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0b0f1a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>

<body class="min-h-screen antialiased">
    <!-- Noise texture -->
    <div class="noise-overlay"></div>

    <!-- ═══════════════════════════════════════════
         HERO SECTION
         ═══════════════════════════════════════════ -->
    <section class="hero-mesh min-h-[85vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="relative z-10 max-w-5xl mx-auto text-center">

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-8 rounded-full border border-emerald-500/20 bg-emerald-500/5 text-emerald-400 text-xs font-semibold tracking-widest uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Proyecto Activo
            </div>

            <!-- Title -->
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold tracking-tight leading-[1.1] mb-6">
                <span class="text-white">Documentación y</span><br>
                <span class="bg-gradient-to-r from-emerald-400 via-cyan-400 to-blue-500 bg-clip-text text-transparent">
                    Recursos del Proyecto
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 leading-relaxed mb-10">
                Centro de referencia para la arquitectura, stack tecnológico y presentaciones del ecosistema
                <span class="text-emerald-400 font-semibold">Brixo</span>.
                Accede a la documentación técnica, diapositivas y recursos de despliegue.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">

                <!-- Primary CTA -->
                <a href="/presentation/Slide1.PNG"
                    target="_blank"
                    class="btn-shine group inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-bold text-sm tracking-wide shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 hover:scale-[1.03] transition-all duration-300">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver Presentación PPTX
                </a>

                <!-- Secondary Buttons -->
                <div class="flex gap-3">
                    <a href="/slides"
                        class="btn-shine inline-flex items-center gap-2 px-6 py-4 rounded-xl bg-white/5 border border-white/10 text-white font-semibold text-sm hover:bg-white/10 hover:border-emerald-500/30 transition-all duration-300">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Ver en Vivo
                    </a>
                    <a href="/slides"
                        class="btn-shine inline-flex items-center gap-2 px-6 py-4 rounded-xl bg-white/5 border border-white/10 text-white font-semibold text-sm hover:bg-white/10 hover:border-blue-500/30 transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Exploración Profunda
                    </a>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="mt-16 animate-bounce">
                <svg class="w-5 h-5 mx-auto text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="divider-glow max-w-4xl mx-auto"></div>

    <!-- ═══════════════════════════════════════════
         STACK TECNOLÓGICO
         ═══════════════════════════════════════════ -->
    <section class="py-20 sm:py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="text-emerald-400 text-xs font-bold tracking-[0.25em] uppercase">Infraestructura</span>
                <h2 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight">
                    Stack Tecnológico
                </h2>
                <p class="mt-4 max-w-xl mx-auto text-slate-400 text-base">
                    Las herramientas y servicios que potencian el ecosistema Brixo, desde la base de datos hasta el despliegue en producción.
                </p>
            </div>

            <!-- Cards Grid: 3 + 2 centered -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-5">

                <!-- ─── Aiven ─── -->
                <div class="card-glow rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="icon-ring bg-gradient-to-br from-blue-600/20 to-blue-400/10 text-blue-400 border border-blue-500/10">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
                                Aiven
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-blue-500/10 text-blue-400 border border-blue-500/20">DBaaS</span>
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Base de datos MySQL administrada en la nube. Proporciona alta disponibilidad, backups automáticos y conexión SSL para datos en producción.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        MySQL 8.0 · Managed Cloud
                    </div>
                </div>

                <!-- ─── Render ─── -->
                <div class="card-glow rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="icon-ring bg-gradient-to-br from-emerald-600/20 to-emerald-400/10 text-emerald-400 border border-emerald-500/10">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
                                Render
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PaaS</span>
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Plataforma de despliegue continuo. Hosting del backend CodeIgniter con deploys automáticos desde GitHub vía Docker.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        Docker · Auto Deploy · HTTPS
                    </div>
                </div>

                <!-- ─── AWS ─── -->
                <div class="card-glow rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="icon-ring bg-gradient-to-br from-amber-600/20 to-amber-400/10 text-amber-400 border border-amber-500/10">
                            <i class="fab fa-aws"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
                                AWS
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Cloud</span>
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Amazon Web Services para almacenamiento S3, CDN con CloudFront y servicios auxiliares de infraestructura escalable.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        S3 · CloudFront · IAM
                    </div>
                </div>
            </div>

            <!-- Bottom row: 2 cards centered -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-[calc(66.666%+0.625rem)] mx-auto">

                <!-- ─── GitHub ─── -->
                <div class="card-glow rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="icon-ring bg-gradient-to-br from-purple-600/20 to-purple-400/10 text-purple-400 border border-purple-500/10">
                            <i class="fab fa-github"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
                                GitHub
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-purple-500/10 text-purple-400 border border-purple-500/20">SCM</span>
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Control de versiones y CI/CD. Repositorio central con GitHub Actions para pruebas automatizadas y despliegue continuo.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        Actions · Codespaces · PRs
                    </div>
                </div>

                <!-- ─── CodeIgniter ─── -->
                <div class="card-glow rounded-2xl p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="icon-ring bg-gradient-to-br from-red-600/20 to-orange-400/10 text-orange-400 border border-orange-500/10">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-white mb-1 flex items-center gap-2">
                                CodeIgniter 4
                                <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-orange-500/10 text-orange-400 border border-orange-500/20">Framework</span>
                            </h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Framework MVC de PHP. Motor del backend con routing, ORM, sesiones, filtros de seguridad y sistema de vistas.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        PHP 8.2 · MVC · Shield
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="divider-glow max-w-4xl mx-auto"></div>

    <!-- ═══════════════════════════════════════════
         SLIDE PREVIEW GALLERY
         ═══════════════════════════════════════════ -->
    <section class="py-20 sm:py-28 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-14">
                <span class="text-blue-400 text-xs font-bold tracking-[0.25em] uppercase">Presentación</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Vista Previa de Diapositivas
                </h2>
                <p class="mt-4 max-w-lg mx-auto text-slate-400 text-sm">
                    11 slides que cubren arquitectura, CI/CD pipeline, demo en vivo y próximos pasos del proyecto.
                </p>
            </div>

            <!-- Slides Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php for ($i = 1; $i <= 11; $i++): ?>
                    <a href="/presentation/Slide<?= $i ?>.PNG"
                        target="_blank"
                        class="slide-thumb group block rounded-xl overflow-hidden bg-surface-700/50">
                        <div class="relative aspect-[16/9]">
                            <img src="/presentation/Slide<?= $i ?>.PNG"
                                alt="Slide <?= $i ?>"
                                class="w-full h-full object-cover"
                                loading="<?= $i <= 4 ? 'eager' : 'lazy' ?>">
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/10 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                    Slide <?= $i ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endfor; ?>
            </div>

            <!-- CTA under slides -->
            <div class="text-center mt-12">
                <a href="/slides"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/5 border border-white/10 text-white text-sm font-semibold hover:bg-white/10 hover:border-emerald-500/30 transition-all duration-300">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    Ver presentación completa en pantalla
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         ARCHITECTURE SUMMARY
         ═══════════════════════════════════════════ -->
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="card-glow rounded-2xl p-8 sm:p-10">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 border border-emerald-500/10 flex items-center justify-center text-emerald-400 text-2xl">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-3">Flujo de Arquitectura</h3>
                        <div class="flex flex-wrap items-center gap-2 text-sm font-mono">
                            <span class="px-3 py-1.5 rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/15">GitHub Push</span>
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/15">Render Build</span>
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            <span class="px-3 py-1.5 rounded-lg bg-orange-500/10 text-orange-400 border border-orange-500/15">Docker / CI4</span>
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            <span class="px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/15">Aiven MySQL</span>
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            <span class="px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/15">AWS S3/CDN</span>
                        </div>
                        <p class="mt-4 text-sm text-slate-400 leading-relaxed">
                            Cada push a <code class="text-emerald-400/80 bg-emerald-500/5 px-1.5 py-0.5 rounded text-xs">master</code>
                            dispara un build Docker en Render que despliega el backend CI4 conectado a Aiven MySQL,
                            con assets estáticos servidos desde AWS CloudFront.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         FOOTER
         ═══════════════════════════════════════════ -->
    <footer class="py-10 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="/images/brixo-logo.png" alt="Brixo" class="h-6 opacity-50" onerror="this.style.display='none'">
                <span class="text-sm text-slate-500">Brixo Showcase · <?= date('Y') ?></span>
            </div>
            <div class="flex gap-6">
                <a href="/" class="text-sm text-slate-500 hover:text-emerald-400 transition-colors">Inicio</a>
                <a href="/map" class="text-sm text-slate-500 hover:text-emerald-400 transition-colors">Mapa</a>
                <a href="/slides" class="text-sm text-slate-500 hover:text-emerald-400 transition-colors">Slides</a>
                <a href="https://github.com/mikerb95/BrixoCI4" target="_blank" class="text-sm text-slate-500 hover:text-emerald-400 transition-colors">
                    <i class="fab fa-github"></i> Repo
                </a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS (for modals if needed from navbar.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
