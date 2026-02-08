<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Usuario') ?> - Brixo Admin</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="/admin/usuarios" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Usuarios
                </a>
                <h2 class="fw-bold mt-2 mb-0">
                    <?= ($editando ?? false) ? 'Editar' : 'Crear' ?> Usuario
                </h2>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card card-admin">
                    <div class="card-body p-4">
                        <form method="post" action="<?= ($editando ?? false) ? '/admin/usuarios/actualizar' : '/admin/usuarios/guardar' ?>">
                            <?= csrf_field() ?>

                            <?php if ($editando ?? false): ?>
                                <input type="hidden" name="id" value="<?= esc($usuario[$tipo === 'cliente' ? 'id_cliente' : ($tipo === 'contratista' ? 'id_contratista' : 'id_admin')]) ?>">
                            <?php endif; ?>

                            <!-- Tipo de usuario -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-1"></i>Tipo de Usuario
                                </label>
                                <?php if ($editando ?? false): ?>
                                    <input type="hidden" name="tipo" value="<?= esc($tipo) ?>">
                                    <div>
                                        <?php if ($tipo === 'cliente'): ?>
                                            <span class="badge bg-primary badge-role fs-6"><i class="fas fa-user me-1"></i>Cliente</span>
                                        <?php elseif ($tipo === 'contratista'): ?>
                                            <span class="badge bg-success badge-role fs-6"><i class="fas fa-hard-hat me-1"></i>Contratista</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark badge-role fs-6"><i class="fas fa-user-shield me-1"></i>Admin</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <select name="tipo" id="tipoUsuario" class="form-select" onchange="toggleFields()">
                                        <option value="cliente" <?= $tipo === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                        <option value="contratista" <?= $tipo === 'contratista' ? 'selected' : '' ?>>Contratista</option>
                                        <option value="admin" <?= $tipo === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <hr class="my-4">

                            <!-- Datos básicos -->
                            <h6 class="fw-bold text-muted mb-3"><i class="fas fa-id-card me-1"></i>Datos Básicos</h6>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Nombre completo *</label>
                                    <input type="text" name="nombre" class="form-control" required
                                           value="<?= esc(old('nombre', ($usuario['nombre'] ?? ''))) ?>"
                                           placeholder="Ej: Juan Pérez">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">Correo electrónico *</label>
                                    <input type="email" name="correo" class="form-control" required
                                           value="<?= esc(old('correo', ($usuario['correo'] ?? ''))) ?>"
                                           placeholder="correo@ejemplo.com">
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold">
                                        Contraseña <?= ($editando ?? false) ? '(dejar vacío para mantener)' : '*' ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="contrasena" id="passwordField" class="form-control"
                                               <?= ($editando ?? false) ? '' : 'required' ?>
                                               placeholder="<?= ($editando ?? false) ? '••••••••' : 'Min. 6 caracteres' ?>"
                                               minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos de Cliente/Contratista -->
                            <div id="fieldsCommon" class="mt-3" style="display: <?= ($tipo ?? 'cliente') !== 'admin' ? 'block' : 'none' ?>;">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control"
                                               value="<?= esc(old('telefono', ($usuario['telefono'] ?? ''))) ?>"
                                               placeholder="3001234567">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold">Ciudad</label>
                                        <input type="text" name="ciudad" class="form-control"
                                               value="<?= esc(old('ciudad', ($usuario['ciudad'] ?? ''))) ?>"
                                               placeholder="Bogotá">
                                    </div>
                                </div>
                            </div>

                            <!-- Campos exclusivos de contratista -->
                            <div id="fieldsContratista" class="mt-3" style="display: <?= ($tipo ?? 'cliente') === 'contratista' ? 'block' : 'none' ?>;">
                                <hr class="my-4">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-hard-hat me-1"></i>Datos de Contratista</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Experiencia</label>
                                    <textarea name="experiencia" class="form-control" rows="2"
                                              placeholder="Ej: 10 años en plomería residencial"><?= esc(old('experiencia', ($usuario['experiencia'] ?? ''))) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Descripción del perfil</label>
                                    <textarea name="descripcion_perfil" class="form-control" rows="3"
                                              placeholder="Descripción profesional..."><?= esc(old('descripcion_perfil', ($usuario['descripcion_perfil'] ?? ''))) ?></textarea>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="verificado" value="1" id="verificadoCheck"
                                           <?= ($usuario['verificado'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="verificadoCheck">
                                        <i class="fas fa-check-circle text-success me-1"></i>Contratista Verificado
                                    </label>
                                </div>
                            </div>

                            <!-- Campos exclusivos de admin -->
                            <div id="fieldsAdmin" style="display: <?= ($tipo ?? 'cliente') === 'admin' && ($editando ?? false) ? 'block' : 'none' ?>;">
                                <hr class="my-4">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-user-shield me-1"></i>Configuración Admin</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activoCheck"
                                           <?= ($usuario['activo'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="activoCheck">
                                        Cuenta Activa
                                    </label>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/admin/usuarios" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fas fa-arrow-left me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-save me-1"></i>
                                    <?= ($editando ?? false) ? 'Actualizar Usuario' : 'Crear Usuario' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info sidebar -->
            <div class="col-12 col-lg-4 mt-4 mt-lg-0">
                <div class="card card-admin">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-info me-2"></i>Información</h6>

                        <?php if ($editando ?? false): ?>
                            <div class="mb-3">
                                <small class="text-muted d-block">Registrado</small>
                                <strong><?= date('d M Y, H:i', strtotime($usuario['creado_en'])) ?></strong>
                            </div>
                            <?php if ($tipo === 'admin' && !empty($usuario['ultimo_acceso'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Último acceso</small>
                                    <strong><?= date('d M Y, H:i', strtotime($usuario['ultimo_acceso'])) ?></strong>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="alert alert-info border-0 mb-0 mt-3">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Roles disponibles:</strong>
                            <ul class="mb-0 mt-2 list-unstyled">
                                <li class="mb-1"><i class="fas fa-user text-primary me-2"></i><strong>Cliente</strong> — Publica solicitudes</li>
                                <li class="mb-1"><i class="fas fa-hard-hat text-success me-2"></i><strong>Contratista</strong> — Ofrece servicios</li>
                                <li><i class="fas fa-user-shield text-warning me-2"></i><strong>Admin</strong> — Gestiona todo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFields() {
            const tipo = document.getElementById('tipoUsuario')?.value ?? '<?= esc($tipo) ?>';
            const common = document.getElementById('fieldsCommon');
            const contratista = document.getElementById('fieldsContratista');
            const admin = document.getElementById('fieldsAdmin');

            if (common) common.style.display = tipo !== 'admin' ? 'block' : 'none';
            if (contratista) contratista.style.display = tipo === 'contratista' ? 'block' : 'none';
            if (admin) admin.style.display = tipo === 'admin' ? 'block' : 'none';
        }

        function togglePassword() {
            const field = document.getElementById('passwordField');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
