<nav id="floating-nav" class="floating-navbar">
    <div class="floating-inner d-flex justify-content-between align-items-center w-100">
        <a href="/" class="brand fw-bold d-flex align-items-center">
            <img src="/images/brixo-logo.png" alt="Brixo" onerror="this.style.display='none'"
                style="transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'"
                onmouseout="this.style.transform='scale(1)'">
        </a>
        <ul class="d-flex list-unstyled mb-0 align-items-center gap-2 ms-3">
            <li><a href="/map" class="float-link fw-medium">Mapa</a></li>
            <?php $floatUser = session()->get('user'); ?>
            <?php if (!empty($floatUser)): ?>
                <li class="dropdown position-relative">
                    <a href="#" class="float-link dropdown-toggle d-flex align-items-center gap-2 text-decoration-none"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($floatUser['foto_perfil'])): ?>
                            <img src="<?= strpos($floatUser['foto_perfil'], 'http') === 0 ? esc($floatUser['foto_perfil']) : '/images/profiles/' . esc($floatUser['foto_perfil']) ?>"
                                alt="Foto" class="rounded-circle border border-2 border-white shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user-circle fs-4"></i>
                        <?php endif; ?>
                        <span class="fw-medium">Mi Cuenta</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3 overflow-hidden" style="backdrop-filter: blur(16px); background: rgba(255,255,255,0.92);">
                        <li>
                            <h6 class="dropdown-header fw-semibold"><?= esc($floatUser['nombre']) ?></h6>
                        </li>
                        <li>
                            <?php $role = $floatUser['rol'] ?? ''; ?>
                            <a href="<?= $role === 'admin' ? '/admin' : '/panel' ?>" class="dropdown-item rounded-2 mx-2 px-3">Mi Panel</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="/logout" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
                <style>
                    @media (min-width: 992px) {
                        .floating-navbar .dropdown:hover .dropdown-menu {
                            display: block;
                        }
                    }
                </style>
            <?php else: ?>
                <li><a href="#" class="float-link" data-bs-toggle="modal" data-bs-target="#loginModal">Ingresar</a></li>
                <li><a href="#" class="float-link" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>