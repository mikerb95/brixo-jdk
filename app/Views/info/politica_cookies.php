<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Política de Cookies - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Información sobre el uso de cookies en Brixo y cómo gestionarlas">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        .policy-hero {
            background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
            padding: 4rem 0;
        }
        .policy-content {
            max-width: 900px;
            margin: 0 auto;
        }
        .policy-section {
            margin-bottom: 2.5rem;
        }
        .policy-section h2 {
            color: #485166;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #485166;
        }
        .policy-section h3 {
            color: #2c3444;
            font-size: 1.35rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .cookie-table {
            font-size: 0.95rem;
        }
        .cookie-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .badge-cookie {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .cookie-type-necesarias { background: #28a745; }
        .cookie-type-analiticas { background: #17a2b8; }
        .cookie-type-marketing { background: #ffc107; }
    </style>
</head>
<body class="bg-light">
    <?= view('partials/navbar') ?>

    <section class="policy-hero text-white text-center">
        <div class="container">
            <i class="fas fa-cookie-bite fa-4x mb-3 opacity-75"></i>
            <h1 class="display-4 fw-bold mb-3">Política de Cookies</h1>
            <p class="lead mb-0">Información sobre cómo utilizamos las cookies en Brixo</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="policy-content">
            
            <!-- Introducción -->
            <div class="policy-section">
                <h2><i class="fas fa-info-circle me-2"></i>¿Qué son las cookies?</h2>
                <p>
                    Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo (ordenador, tablet o móvil) 
                    cuando visitas un sitio web. Las cookies permiten que el sitio web recuerde tus acciones y preferencias 
                    (como inicio de sesión, idioma, tamaño de fuente y otras preferencias de visualización) durante un período 
                    de tiempo.
                </p>
                <p>
                    En <strong>Brixo</strong>, utilizamos cookies para mejorar tu experiencia de navegación, analizar el uso 
                    del sitio y personalizar el contenido que te mostramos.
                </p>
            </div>

            <!-- Tipos de cookies -->
            <div class="policy-section">
                <h2><i class="fas fa-th-list me-2"></i>Tipos de cookies que utilizamos</h2>
                
                <h3>🔒 Cookies Necesarias</h3>
                <p>
                    Estas cookies son esenciales para que el sitio funcione correctamente. No se pueden desactivar y 
                    no almacenan información personal identificable.
                </p>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered cookie-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Propósito</th>
                                <th>Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>ci_session</code></td>
                                <td>Gestiona la sesión del usuario y mantiene el estado de login</td>
                                <td>2 horas</td>
                            </tr>
                            <tr>
                                <td><code>csrf_cookie_name</code></td>
                                <td>Protección contra ataques CSRF (Cross-Site Request Forgery)</td>
                                <td>2 horas</td>
                            </tr>
                            <tr>
                                <td><code>brixo_cookie_consent</code></td>
                                <td>Almacena tu preferencia sobre el uso de cookies</td>
                                <td>1 año</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3>📊 Cookies Analíticas</h3>
                <p>
                    Nos ayudan a entender cómo los visitantes interactúan con nuestro sitio web, recopilando y reportando 
                    información de forma anónima. Utilizamos un sistema de analítica propio (first-party), sin depender de terceros.
                    Solo se activan si das tu consentimiento.
                </p>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered cookie-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Propósito</th>
                                <th>Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>bx_vid</code></td>
                                <td>Brixo Analytics – Identificador anónimo de visitante (UUID aleatorio, no vinculado a datos personales)</td>
                                <td>1 año</td>
                            </tr>
                            <tr>
                                <td><code>bx_sid (sessionStorage)</code></td>
                                <td>Brixo Analytics – Identificador de sesión para agrupar actividad</td>
                                <td>Sesión del navegador</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-success border-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Analítica 100% propia.</strong> No compartimos datos con terceros. Las IPs se anonimizan 
                    (se elimina el último octeto) antes de guardarse. Los datos se procesan únicamente en nuestros servidores.
                </div>

                <h3>🎯 Cookies de Marketing</h3>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>No utilizamos cookies de marketing ni de terceros.</strong> Brixo opera con un sistema de 
                    analítica completamente propio (first-party). Tus datos nunca se comparten con redes publicitarias, 
                    plataformas externas ni servicios de tracking.
                </div>
            </div>

            <!-- Gestión de cookies -->
            <div class="policy-section">
                <h2><i class="fas fa-cog me-2"></i>Gestión y control de cookies</h2>
                
                <h3>A través de tu navegador</h3>
                <p>
                    Puedes configurar tu navegador para que rechace todas las cookies o para que te avise cuando se envíe 
                    una cookie. Sin embargo, algunas funciones del sitio pueden no funcionar correctamente si las cookies 
                    están deshabilitadas.
                </p>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="fab fa-chrome fa-2x text-primary mb-2"></i>
                                <h5 class="fw-bold">Google Chrome</h5>
                                <p class="small text-muted mb-0">
                                    Configuración → Privacidad y seguridad → Cookies y otros datos de sitios
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="fab fa-firefox fa-2x text-danger mb-2"></i>
                                <h5 class="fw-bold">Mozilla Firefox</h5>
                                <p class="small text-muted mb-0">
                                    Opciones → Privacidad y seguridad → Cookies y datos del sitio
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="fab fa-safari fa-2x text-info mb-2"></i>
                                <h5 class="fw-bold">Safari</h5>
                                <p class="small text-muted mb-0">
                                    Preferencias → Privacidad → Gestionar datos del sitio web
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <i class="fab fa-edge fa-2x text-success mb-2"></i>
                                <h5 class="fw-bold">Microsoft Edge</h5>
                                <p class="small text-muted mb-0">
                                    Configuración → Privacidad, búsqueda y servicios → Cookies
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>A través de Brixo</h3>
                <p>
                    Puedes cambiar tus preferencias de cookies en cualquier momento haciendo clic en el siguiente botón:
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="showCookieSettings">
                    <i class="fas fa-cookie-bite me-2"></i>Gestionar preferencias de cookies
                </button>
            </div>

            <!-- Más información -->
            <div class="policy-section">
                <h2><i class="fas fa-question-circle me-2"></i>Más información</h2>
                <p>
                    Si tienes dudas sobre nuestra política de cookies o sobre cómo gestionamos tu información, 
                    puedes contactarnos:
                </p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:privacidad@brixo.com">mike95@duck.com</a></li>
                    <li><strong>Sección de ayuda:</strong> <a href="/ayuda">Centro de ayuda</a></li>
                </ul>
            </div>

        </div>
    </div>

    <?= view('partials/footer') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Permitir que el usuario vuelva a mostrar el banner de cookies
        document.getElementById('showCookieSettings').addEventListener('click', function() {
            // Borrar preferencia guardada
            localStorage.removeItem('brixo_cookie_consent');
            document.cookie = 'brixo_cookie_consent=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            
            // Mostrar banner
            if (window.BrixoCookieConsent) {
                window.BrixoCookieConsent.showBanner();
            }
        });
    </script>
</body>
</html>
