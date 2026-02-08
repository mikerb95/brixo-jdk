<?php

// public/debug_boot.php

// Force error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Boot Debugger</h1>";

try {
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

    echo "FCPATH: " . FCPATH . "<br>";

    $pathsFile = FCPATH . '../app/Config/Paths.php';
    if (!file_exists($pathsFile)) {
        throw new Exception("Paths config not found at: $pathsFile");
    }
    require $pathsFile;

    $paths = new Config\Paths();
    echo "System Directory: " . $paths->systemDirectory . "<br>";

    $bootFile = $paths->systemDirectory . '/Boot.php';
    if (!file_exists($bootFile)) {
        throw new Exception("Boot file not found at: $bootFile");
    }
    require $bootFile;

    echo "Boot file loaded.<br>";

    // Try to load the app
    CodeIgniter\Boot::bootWeb($paths);

    echo "Boot successful (if you see this, the app didn't exit, which is unusual for CI4 bootWeb).";

} catch (Throwable $e) {
    echo "<h2 style='color:red'>Boot Error</h2>";
    echo "<strong>Type:</strong> " . get_class($e) . "<br>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<h3>Trace:</h3><pre>" . $e->getTraceAsString() . "</pre>";
}
