<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['get', 'post', 'head'], '/', 'Home::index');

// Auth (basic educational handlers)
$routes->get('/login', 'Auth::showLogin');
$routes->post('/login', 'Auth::login');
$routes->post('/logout', 'Auth::logout');

// Password Reset
$routes->get('/password/forgot', 'PasswordReset::forgot');
$routes->post('/password/send-reset', 'PasswordReset::sendResetLink');
$routes->get('/password/reset/(:any)', 'PasswordReset::reset/$1');
$routes->post('/password/update', 'PasswordReset::processReset');

// Registration
$routes->post('/register', 'Register::register');

$routes->get('/map', 'Mapa::index');
$routes->get('/panel', 'Panel::index');
$routes->get('/perfil', 'Panel::index');
$routes->get('/perfil/editar', 'Panel::editarPerfil');
$routes->post('/perfil/actualizar', 'Panel::actualizarPerfil');
$routes->get('/perfil/ver/(:num)', 'Perfil::ver/$1');
// Profile viewing
$routes->get('/perfil/ver/(:num)', 'Perfil::ver/$1');
$routes->get('/solicitudes', 'Solicitudes::index');

// Especialidades
$routes->get('/especialidades', 'Especialidades::index');
$routes->get('/especialidades/categoria/(:num)', 'Especialidades::categoria/$1');

$routes->get('reportes/contratistas', 'Reportes::contratistas');

$routes->get('reportes/solicitudes-xlsx', 'Reportes::solicitudesXlsx');
$routes->get('/debug-auth', 'DebugAuth::index');
$routes->get('/setup/solicitudes', 'Setup::solicitudes'); // Ruta de instalación
$routes->get('/setup/update-cliente', 'Setup::update_cliente'); // Ruta de actualización DB
$routes->get('/setup/update-fotos', 'Setup::update_fotos'); // Ruta de actualización DB Fotos
$routes->get('/setup/mensajes', 'Setup::mensajes'); // Ruta de instalación Mensajes

// Mensajes
$routes->get('/mensajes', 'Mensajes::index');
$routes->get('/mensajes/chat/(:num)/(:segment)', 'Mensajes::chat/$1/$2'); // id_otro_usuario, rol_otro_usuario
$routes->post('/mensajes/enviar', 'Mensajes::enviar');
$routes->get('/mensajes/nuevos/(:num)/(:segment)', 'Mensajes::nuevos/$1/$2'); // AJAX polling

// Solicitudes
$routes->get('/solicitud/nueva', 'Solicitud::nueva');
$routes->post('/solicitud/guardar', 'Solicitud::guardar');
$routes->get('/solicitud/editar/(:num)', 'Solicitud::editar/$1');
$routes->post('/solicitud/actualizar/(:num)', 'Solicitud::actualizar/$1');
$routes->get('/solicitud/eliminar/(:num)', 'Solicitud::eliminar/$1');
$routes->get('/tablon-tareas', 'Solicitud::index'); // Para contratistas

// Páginas estáticas del footer
$routes->get('sobre-nosotros', 'Info::sobreNosotros');
$routes->get('como-funciona', 'Info::comoFunciona');
$routes->get('seguridad', 'Info::seguridad');
$routes->get('ayuda', 'Info::ayuda');
$routes->get('unete-pro', 'Info::unetePro');
$routes->get('historias-exito', 'Info::historiasExito');
$routes->get('recursos', 'Info::recursos');
$routes->get('carreras', 'Info::carreras');
$routes->get('prensa', 'Info::prensa');
$routes->get('blog', 'Info::blog');
$routes->get('politica-cookies', 'Info::politicaCookies');

// Presentation routes
$routes->get('/slides', 'Presentation::slides');
$routes->get('/remote', 'Presentation::remote');
$routes->get('/presenter', 'Presentation::presenter');
$routes->get('/main-panel', 'Presentation::mainPanel');
$routes->get('/demo', 'Presentation::demo');
$routes->match(['get', 'post'], '/api/slide', 'Presentation::apiSlide');
$routes->match(['get', 'post'], '/api/demo', 'Presentation::apiDemo');

// Showcase
$routes->get('/showcase', 'Showcase::index');

// Cotizador inteligente (IA)
$routes->get('/cotizador', 'Cotizador::index');
$routes->post('/cotizador/generar', 'Cotizador::generar');
$routes->post('/cotizador/confirmar', 'Cotizador::confirmar');
$routes->get('/cotizador/exito', 'Cotizador::exito');

// Analytics API (First-Party)
$routes->post('/api/v1/track', 'Analytics::track');
$routes->get('/analytics/dashboard', 'Analytics::dashboard');

// Admin Panel
$routes->get('/admin', 'Admin::index');
$routes->get('/admin/usuarios', 'Admin::usuarios');
$routes->get('/admin/usuarios/crear', 'Admin::crear');
$routes->post('/admin/usuarios/guardar', 'Admin::guardar');
$routes->get('/admin/usuarios/editar/(:segment)/(:num)', 'Admin::editar/$1/$2');
$routes->post('/admin/usuarios/actualizar', 'Admin::actualizar');
$routes->get('/admin/usuarios/eliminar/(:segment)/(:num)', 'Admin::eliminar/$1/$2');
