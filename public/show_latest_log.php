<?php
// Script para visualizar el último log de CodeIgniter 4
// Útil para depurar errores "Whoops!" en producción

$logDir = '../writable/logs/';
$files = glob($logDir . 'log-*.log');

echo "<!DOCTYPE html><html><head><title>Log Viewer</title><style>body{font-family:monospace;}</style></head><body>";

if (!$files) {
    echo "<h2>No se encontraron archivos de log en " . realpath($logDir) . "</h2>";
    echo "<p>Asegúrate de que la carpeta writable/logs tenga permisos de escritura.</p>";
} else {
    // Ordenar por fecha de modificación (el más nuevo primero)
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $latestLog = $files[0];
    echo "<h2>Mostrando último log: " . basename($latestLog) . "</h2>";
    echo "<p>Fecha: " . date("Y-m-d H:i:s", filemtime($latestLog)) . "</p>";
    echo "<hr>";

    $content = file_get_contents($latestLog);

    // Resaltar errores
    $content = htmlspecialchars($content);
    $content = str_replace('CRITICAL', '<span style="color:red; font-weight:bold;">CRITICAL</span>', $content);
    $content = str_replace('ERROR', '<span style="color:orange; font-weight:bold;">ERROR</span>', $content);

    echo "<pre style='background:#f8f9fa; padding:15px; border:1px solid #ddd; white-space:pre-wrap;'>" . $content . "</pre>";
}

echo "</body></html>";
