<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Usuarios') ?> - Brixo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --admin-primary: #485166; --admin-dark: #3a4255; }
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; }
        .admin-sidebar {
            width: 260px; min-height: 100vh; background: linear-gradient(180deg, var(--admin-primary) 0%, var(--admin-dark) 100%);
            position: fixed; top: 0; left: 0; z-index: 1000; transition: transform 0.3s;
        }
        .admin-sidebar .brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-sidebar .brand h4 { color: #fff; margin: 0; font-weight: 700; }
        .admin-sidebar .brand small { color: rgba(255,255,255,0.6); }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.75); padding: 12px 20px; border-radius: 8px; margin: 4px 12px;
            transition: all 0.2s; font-size: 0.95rem;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: #fff; }
        .admin-sidebar .nav-link i { width: 24px; text-align: center; margin-right: 10px; }
        .admin-main { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .card-admin { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .user-row { transition: background 0.15s; }
        .user-row:hover { background: #f8f9fa; }
        .badge-role { font-size: 0.8rem; padding: 5px 12px; border-radius: 6px; }
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
            <a href="/admin" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="/admin/usuarios" class="nav-link active"><i class="fas fa-users"></i> Usuarios</a>
            <a href="/analytics/dashboard" class="nav-link"><i class="fas fa-chart-line"></i> Analíticas</a>
            <hr style="border-color: rgba(255,255,255,0.15); margin: 16px 20px;">
            <a href="/" class="nav-link"><i class="fas fa-home"></i> Ir al Sitio</a>
            <a href="/auth/logout" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </nav>
        <form id="logoutForm" action="/logout" method="post" style="display:none;"><?= csrf_field() ?></form>
    </aside>

    <main class="admin-main">
        <div class="d-lg-none mb-3">
            <button class="btn btn-dark" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                <i class="fas fa-bars"></i> Menú
            </button>
        </div>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Gestión de Usuarios</h2>
                <p class="text-muted mb-0">
                    <?= count($clientes) ?> clientes, <?= count($contratistas) ?> contratistas, <?= count($admins) ?> admins
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/usuarios/crear?tipo=cliente" class="btn btn-primary rounded-pill">
                    <i class="fas fa-plus me-1"></i> Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('message')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Filter -->
        <div class="card card-admin mb-4">
            <div class="card-body">
                <form method="get" action="/admin/usuarios" class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-bold"><i class="fas fa-search me-1"></i>Buscar</label>
                        <input type="text" name="q" class="form-control" placeholder="Nombre o correo..." value="<?= esc($busqueda) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i>Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="todos" <?= $filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="clientes" <?= $filtro === 'clientes' ? 'selected' : '' ?>>Clientes</option>
                            <option value="contratistas" <?= $filtro === 'contratistas' ? 'selected' : '' ?>>Contratistas</option>
                            <option value="admins" <?= $filtro === 'admins' ? 'selected' : '' ?>>Administradores</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-1"></i>Filtrar</button>
                        <a href="/admin/usuarios" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admins Table -->
        <?php if (!empty($admins)): ?>
        <div class="card card-admin mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-user-shield me-2 text-warning"></i>Administradores
                    <span class="badge bg-warning text-dark ms-2"><?= count($admins) ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th class="text-center">Estado</th>
                                <th>Último acceso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $a): ?>
                                <tr class="user-row">
                                    <td class="ps-4"><strong>#<?= $a['id_admin'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                <i class="fas fa-user-shield text-warning"></i>
                                            </div>
                                            <strong><?= esc($a['nombre']) ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?= esc($a['correo']) ?></td>
                                    <td class="text-center">
                                        <?php if ($a['activo']): ?>
                                            <span class="badge bg-success badge-role">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary badge-role">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= $a['ultimo_acceso'] ? date('d M Y, H:i', strtotime($a['ultimo_acceso'])) : '—' ?></td>
                                    <td class="text-center">
                                        <a href="/admin/usuarios/editar/admin/<?= $a['id_admin'] ?>" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($a['id_admin'] !== ($user['id'] ?? 0)): ?>
                                            <a href="/admin/usuarios/eliminar/admin/<?= $a['id_admin'] ?>" class="btn btn-sm btn-outline-danger rounded-pill"
                                               onclick="return confirm('¿Estás seguro de eliminar este administrador?');" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Clients Table -->
        <?php if (!empty($clientes)): ?>
        <div class="card card-admin mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-user me-2 text-primary"></i>Clientes
                    <span class="badge bg-primary ms-2"><?= count($clientes) ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Ciudad</th>
                                <th>Registrado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $c): ?>
                                <tr class="user-row">
                                    <td class="ps-4"><strong>#<?= $c['id_cliente'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($c['foto_perfil'])): ?>
                                                <img src="<?= esc($c['foto_perfil']) ?>" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                            <?php endif; ?>
                                            <strong><?= esc($c['nombre']) ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?= esc($c['correo']) ?></td>
                                    <td class="text-muted"><?= esc($c['telefono'] ?? '—') ?></td>
                                    <td class="text-muted"><?= esc($c['ciudad'] ?? '—') ?></td>
                                    <td class="text-muted"><?= date('d M Y', strtotime($c['creado_en'])) ?></td>
                                    <td class="text-center">
                                        <a href="/admin/usuarios/editar/cliente/<?= $c['id_cliente'] ?>" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/usuarios/eliminar/cliente/<?= $c['id_cliente'] ?>" class="btn btn-sm btn-outline-danger rounded-pill"
                                           onclick="return confirm('¿Estás seguro de eliminar al cliente <?= esc($c['nombre']) ?>?');" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contractors Table -->
        <?php if (!empty($contratistas)): ?>
        <div class="card card-admin mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-hard-hat me-2 text-success"></i>Contratistas
                    <span class="badge bg-success ms-2"><?= count($contratistas) ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Ciudad</th>
                                <th class="text-center">Verificado</th>
                                <th>Registrado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contratistas as $c): ?>
                                <tr class="user-row">
                                    <td class="ps-4"><strong>#<?= $c['id_contratista'] ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($c['foto_perfil'])): ?>
                                                <img src="<?= esc($c['foto_perfil']) ?>" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                    <i class="fas fa-hard-hat text-success"></i>
                                                </div>
                                            <?php endif; ?>
                                            <strong><?= esc($c['nombre']) ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?= esc($c['correo']) ?></td>
                                    <td class="text-muted"><?= esc($c['ciudad'] ?? '—') ?></td>
                                    <td class="text-center">
                                        <?php if ($c['verificado']): ?>
                                            <span class="badge bg-success badge-role"><i class="fas fa-check me-1"></i>Sí</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary badge-role">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= date('d M Y', strtotime($c['creado_en'])) ?></td>
                                    <td class="text-center">
                                        <a href="/admin/usuarios/editar/contratista/<?= $c['id_contratista'] ?>" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/admin/usuarios/eliminar/contratista/<?= $c['id_contratista'] ?>" class="btn btn-sm btn-outline-danger rounded-pill"
                                           onclick="return confirm('¿Estás seguro de eliminar al contratista <?= esc($c['nombre']) ?>?');" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Empty state -->
        <?php if (empty($clientes) && empty($contratistas) && empty($admins)): ?>
            <div class="card card-admin">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold">No se encontraron usuarios</h5>
                    <p class="text-muted mb-3">Intenta ajustar los filtros de búsqueda.</p>
                    <a href="/admin/usuarios" class="btn btn-outline-primary rounded-pill">Limpiar filtros</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
