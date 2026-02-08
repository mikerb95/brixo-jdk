<?php

// public/show_logs.php

// Define paths
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$logPath = FCPATH . '../writable/logs/';

// Get all log files
$files = glob($logPath . 'log-*.log');

if (!$files) {
    die("No log files found in " . $logPath);
}

// Sort by modification time, newest first
usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latestFile = $files[0];

echo "<h1>Latest Log File: " . basename($latestFile) . "</h1>";
echo "<p>Last Modified: " . date('Y-m-d H:i:s', filemtime($latestFile)) . "</p>";
echo "<hr>";
echo "<pre style='background: #f4f4f4; padding: 15px; white-space: pre-wrap;'>";
echo htmlspecialchars(file_get_contents($latestFile));
echo "</pre>";
