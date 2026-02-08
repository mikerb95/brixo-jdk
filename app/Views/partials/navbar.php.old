<?php
/**
 * ── Brixo Unified Navbar ──
 * Componente único de navegación para todo el sitio.
 * Detecta la ruta actual para aplicar position: absolute (mapa) o relative (resto).
 */
$_brixo_uri = $_SERVER['REQUEST_URI'] ?? '';
$_brixo_is_map = (strpos($_brixo_uri, '/map') === 0 || $_brixo_uri === '/map');
$_brixo_nav_class = $_brixo_is_map ? 'brixo-nav--overlay' : 'brixo-nav--standard';
$_brixo_user = session()->get('user');
?>
<nav class="brixo-nav <?= $_brixo_nav_class ?>" id="brixoUnifiedNav">
    <div class="brixo-nav__inner">
        <!-- Brand -->
        <a class="brixo-nav__brand" href="/">
            <img src="/images/brixo-logo.png" alt="Brixo" onerror="this.style.display='none'">
        </a>

        <!-- Mobile toggle -->
        <button class="brixo-nav__toggle" type="button" data-bs-toggle="collapse"
            data-bs-target="#brixoNavCollapse" aria-controls="brixoNavCollapse"
            aria-expanded="false" aria-label="Menú">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Nav links -->
        <div class="collapse navbar-collapse" id="brixoNavCollapse">
            <ul class="brixo-nav__links">
                <li><a href="/especialidades">Especialidades</a></li>
                <li><a href="/map">Mapa</a></li>
                <?php if (!empty($_brixo_user)): ?>
                    <!-- Logged-in: dropdown -->
                    <li class="brixo-nav__dropdown">
                        <a href="#" class="brixo-nav__dropdown-toggle"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            <span><?= esc($_brixo_user['nombre'] ?? 'Mi Cuenta') ?></span>
                            <i class="fas fa-chevron-down fa-xs"></i>
                        </a>
                        <ul class="brixo-nav__dropdown-menu">
                            <li class="brixo-nav__dropdown-header"><?= esc($_brixo_user['correo'] ?? '') ?></li>
                            <li><a href="/panel"><i class="fas fa-th-large"></i> Mi Panel</a></li>
                            <li><a href="/mensajes"><i class="fas fa-comments"></i> Mensajes</a></li>
                            <li><a href="/perfil/editar"><i class="fas fa-user-edit"></i> Editar Perfil</a></li>
                            <li class="brixo-nav__dropdown-divider"></li>
                            <li>
                                <form action="/logout" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest -->
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="brixo-nav__btn-login">Ingresar</a></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>