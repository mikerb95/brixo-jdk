<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// FIX: Force HTTPS for Render.com (Load Balancer Termination)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

// Support HTTPS when running behind a reverse proxy (e.g., Render.com)
// Many platforms terminate TLS at the load balancer and forward requests
// to the application via HTTP while setting the X-Forwarded-Proto header.
// When that happens, tell PHP/CodeIgniter the request is secure so that
// cookies with the `Secure` flag will be sent and URL generation uses https.
if (
    empty($_SERVER['HTTPS'])
    && !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
    && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
    // Some libraries check REQUEST_SCHEME when building URLs
    $_SERVER['REQUEST_SCHEME'] = 'https';
    // Also set forwarded port if provided
    if (!empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
        $_SERVER['SERVER_PORT'] = $_SERVER['HTTP_X_FORWARDED_PORT'];
    } else {
        $_SERVER['SERVER_PORT'] = 443;
    }
}

exit(Boot::bootWeb($paths));
