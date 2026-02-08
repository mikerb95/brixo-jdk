<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --admin-primary: #485166; --admin-dark: #3a4255; }
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; }

        /* Sidebar */
        .admin-sidebar {
            width: 260px; min-height: 100vh; background: linear-gradient(180deg, var(--admin-primary) 0%, var(--admin-dark) 100%);
            position: fixed; top: 0; left: 0; z-index: 1000; padding-top: 0; transition: transform 0.3s;
        }
        .admin-sidebar .brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .brand h4 { color: #fff; margin: 0; font-weight: 700; }
        .admin-sidebar .brand small { color: rgba(255,255,255,0.6); }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.75); padding: 12px 20px; border-radius: 8px; margin: 4px 12px;
            transition: all 0.2s; font-size: 0.95rem;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background: rgba(255,255,255,0.15); color: #fff;
        }
        .admin-sidebar .nav-link i { width: 24px; text-align: center; margin-right: 10px; }

        /* Main content */
        .admin-main { margin-left: 260px; padding: 30px; min-height: 100vh; }

        /* Cards */
        .admin-stat-card {
            border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s; overflow: hidden;
        }
        .admin-stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
        .stat-icon-lg { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .stat-value { font-size: 2rem; font-weight: 800; line-height: 1.1; }

        .card-admin { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }

        /* Responsive */
        @media (max-width: 991px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="brand">
            <h4><i class="fas fa-shield-alt me-2"></i>Brixo Admin</h4>
            <small>Panel de Administración</small>
        </div>
        <nav class="mt-3">
            <a href="/admin" class="nav-link <?= current_url() === site_url('/admin') ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="/admin/usuarios" class="nav-link <?= str_contains(current_url(), '/usuarios') ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a href="/analytics/dashboard" class="nav-link <?= str_contains(current_url(), '/analytics') ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Analíticas
            </a>
            <hr style="border-color: rgba(255,255,255,0.15); margin: 16px 20px;">
            <a href="/" class="nav-link">
                <i class="fas fa-home"></i> Ir al Sitio
            </a>
            <a href="/auth/logout" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </nav>
        <form id="logoutForm" action="/logout" method="post" style="display:none;">
            <?= csrf_field() ?>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Mobile toggle -->
        <div class="d-lg-none mb-3">
            <button class="btn btn-dark" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                <i class="fas fa-bars"></i> Menú
            </button>
        </div>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Bienvenido, <?= esc($user['nombre'] ?? 'Admin') ?></p>
            </div>
            <div class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                <?= date('d M, Y') ?>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card admin-stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon-lg" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="stat-value text-dark"><?= number_format($totalClientes) ?></div>
                            <small class="text-muted">Clientes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card admin-stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon-lg" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: #fff;">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <div class="stat-value text-dark"><?= number_format($totalContratistas) ?></div>
                            <small class="text-muted">Contratistas</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card admin-stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon-lg" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div class="stat-value text-dark"><?= number_format($totalSolicitudes) ?></div>
                            <small class="text-muted">Solicitudes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card admin-stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon-lg" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <div class="stat-value text-dark"><?= number_format($eventosHoy) ?></div>
                            <small class="text-muted">Eventos (24h)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <a href="/admin/usuarios/crear?tipo=cliente" class="card card-admin text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px;">
                            <i class="fas fa-user-plus text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Nuevo Cliente</h6>
                        <small class="text-muted">Crear cuenta de cliente</small>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="/admin/usuarios/crear?tipo=contratista" class="card card-admin text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px;">
                            <i class="fas fa-hard-hat text-success"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Nuevo Contratista</h6>
                        <small class="text-muted">Crear cuenta de profesional</small>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="/admin/usuarios/crear?tipo=admin" class="card card-admin text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px;">
                            <i class="fas fa-user-shield text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Nuevo Admin</h6>
                        <small class="text-muted">Crear administrador</small>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <a href="/analytics/dashboard" class="card card-admin text-decoration-none h-100">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px;">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Ver Analíticas</h6>
                        <small class="text-muted">Dashboard de analíticas</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card card-admin">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fas fa-user me-2 text-primary"></i>Últimos Clientes</h5>
                        <a href="/admin/usuarios?tipo=clientes" class="btn btn-sm btn-outline-primary rounded-pill">Ver todos</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($ultimosClientes)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay clientes registrados
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($ultimosClientes as $c): ?>
                                    <div class="list-group-item d-flex align-items-center gap-3 px-4">
                                        <?php if (!empty($c['foto_perfil'])): ?>
                                            <img src="<?= esc($c['foto_perfil']) ?>" alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <strong class="d-block"><?= esc($c['nombre']) ?></strong>
                                            <small class="text-muted"><?= esc($c['correo']) ?></small>
                                        </div>
                                        <small class="text-muted"><?= date('d M', strtotime($c['creado_en'])) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card card-admin">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fas fa-hard-hat me-2 text-success"></i>Últimos Contratistas</h5>
                        <a href="/admin/usuarios?tipo=contratistas" class="btn btn-sm btn-outline-success rounded-pill">Ver todos</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($ultimosContratistas)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay contratistas registrados
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($ultimosContratistas as $c): ?>
                                    <div class="list-group-item d-flex align-items-center gap-3 px-4">
                                        <?php if (!empty($c['foto_perfil'])): ?>
                                            <img src="<?= esc($c['foto_perfil']) ?>" alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="fas fa-hard-hat text-success"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <strong class="d-block"><?= esc($c['nombre']) ?></strong>
                                            <small class="text-muted"><?= esc($c['correo']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <?php if (!empty($c['verificado'])): ?>
                                                <span class="badge bg-success"><i class="fas fa-check"></i> Verificado</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Pendiente</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
