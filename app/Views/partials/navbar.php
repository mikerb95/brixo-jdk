<?php
/**
 * ── Brixo Navbar v3 ──
 * Glassmorphism · Hybrid positioning · Zero external deps
 *
 * Lógica conservada:
 *   - session()->get('user')  → usuario logueado
 *   - Modales #loginModal / #registerModal  → guest
 *   - POST /logout con CSRF  → cerrar sesión
 *
 * Modos:
 *   - overlay  → position:fixed, transparente  (home & map)
 *   - solid    → position:relative, pushes content
 */

$_nav_uri  = $_SERVER['REQUEST_URI'] ?? '/';
$_nav_path = parse_url($_nav_uri, PHP_URL_PATH);

$_nav_is_home = ($_nav_path === '/' || $_nav_path === '');
$_nav_is_map  = (strpos($_nav_path, '/map') === 0);
$_nav_overlay  = $_nav_is_home || $_nav_is_map;

$_nav_user = session()->get('user');
?>

<nav class="bn <?= $_nav_overlay ? 'bn--overlay' : 'bn--solid' ?><?= $_nav_is_map ? ' bn--map' : '' ?>" id="brixoNav" role="navigation" aria-label="Navegación principal">
    <div class="bn__inner">

        <!-- ── Brand ── -->
        <a class="bn__brand" href="/" aria-label="Brixo inicio">
            <img src="/images/brixo-logo.png" alt="Brixo" width="110" height="32"
                 onerror="this.onerror=null;this.parentNode.innerHTML='<span class=\'bn__brand-text\'>Brixo</span>';">
        </a>

        <!-- ── Links centro ── -->
        <ul class="bn__menu" id="bnMenu">
            <li><a href="/especialidades" class="bn__link <?= strpos($_nav_path, '/especialidades') === 0 ? 'bn__link--active' : '' ?>">Especialidades</a></li>
            <li><a href="/map"             class="bn__link <?= $_nav_is_map ? 'bn__link--active' : '' ?>">Mapa</a></li>
            <li><a href="/cotizador"       class="bn__link <?= strpos($_nav_path, '/cotizador') === 0 ? 'bn__link--active' : '' ?>"><i class="fas fa-calculator me-1"></i>Cotizador</a></li>
        </ul>

        <!-- ── Acciones derecha ── -->
        <div class="bn__actions">
            <?php if (!empty($_nav_user)): ?>
                <!-- Logged-in -->
                <div class="bn__user" id="bnUser">
                    <button class="bn__user-toggle" id="bnUserToggle" aria-expanded="false" aria-haspopup="true">
                        <?php if (!empty($_nav_user['foto_perfil'])): ?>
                            <img src="<?= strpos($_nav_user['foto_perfil'], 'http') === 0
                                ? esc($_nav_user['foto_perfil'])
                                : '/images/profiles/' . esc($_nav_user['foto_perfil']) ?>"
                                 alt="" class="bn__avatar" width="32" height="32">
                        <?php else: ?>
                            <span class="bn__avatar bn__avatar--placeholder">
                                <i class="fas fa-user"></i>
                            </span>
                        <?php endif; ?>
                        <span class="bn__user-name"><?= esc($_nav_user['nombre'] ?? 'Mi Cuenta') ?></span>
                        <i class="fas fa-chevron-down bn__chevron"></i>
                    </button>

                    <div class="bn__dropdown" id="bnDropdown" role="menu">
                        <div class="bn__dropdown-header">
                            <span><?= esc($_nav_user['correo'] ?? '') ?></span>
                        </div>
                        <a href="/panel"         class="bn__dropdown-item" role="menuitem"><i class="fas fa-th-large"></i>    Mi Panel</a>
                        <a href="/mensajes"      class="bn__dropdown-item" role="menuitem"><i class="fas fa-comments"></i>    Mensajes</a>
                        <a href="/perfil/editar"  class="bn__dropdown-item" role="menuitem"><i class="fas fa-user-edit"></i>   Editar Perfil</a>
                        <div class="bn__dropdown-divider"></div>
                        <form action="/logout" method="post" class="bn__dropdown-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="bn__dropdown-item bn__dropdown-item--danger" role="menuitem">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest -->
                <a href="#" class="bn__link" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</a>
                <a href="#" class="bn__btn" data-bs-toggle="modal" data-bs-target="#loginModal">Ingresar</a>
            <?php endif; ?>
        </div>

        <!-- ── Hamburger mobile ── -->
        <button class="bn__burger" id="bnBurger" aria-label="Abrir menú" aria-expanded="false" aria-controls="bnMobile">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- ── Mobile drawer ── -->
    <div class="bn__mobile" id="bnMobile" aria-hidden="true">
        <ul class="bn__mobile-links">
            <li><a href="/especialidades">Especialidades</a></li>
            <li><a href="/map">Mapa</a></li>
            <li><a href="/cotizador"><i class="fas fa-calculator me-1"></i>Cotizador</a></li>
        </ul>
        <div class="bn__mobile-divider"></div>
        <?php if (!empty($_nav_user)): ?>
            <div class="bn__mobile-user">
                <span class="bn__mobile-label"><?= esc($_nav_user['nombre'] ?? 'Mi Cuenta') ?></span>
            </div>
            <ul class="bn__mobile-links">
                <li><a href="/panel"><i class="fas fa-th-large"></i> Mi Panel</a></li>
                <li><a href="/mensajes"><i class="fas fa-comments"></i> Mensajes</a></li>
                <li><a href="/perfil/editar"><i class="fas fa-user-edit"></i> Editar Perfil</a></li>
            </ul>
            <div class="bn__mobile-divider"></div>
            <form action="/logout" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="bn__mobile-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>
            </form>
        <?php else: ?>
            <div class="bn__mobile-actions">
                <a href="#" class="bn__btn bn__btn--block" data-bs-toggle="modal" data-bs-target="#loginModal">Ingresar</a>
                <a href="#" class="bn__link" data-bs-toggle="modal" data-bs-target="#registerModal">Registrarse</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
