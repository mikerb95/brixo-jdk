<?php
// public/check_env.php

echo "<h1>Environment Check</h1>";

echo "<h2>GD Library</h2>";
if (extension_loaded('gd')) {
    echo "<p style='color:green'>GD is loaded.</p>";
    $gdInfo = gd_info();
    echo "<pre>";
    print_r($gdInfo);
    echo "</pre>";
} else {
    echo "<p style='color:red'>GD is NOT loaded.</p>";
}

echo "<h2>Write Permissions</h2>";
$targetDir = __DIR__ . '/images/profiles/';
echo "Target Dir: $targetDir<br>";

if (!is_dir($targetDir)) {
    echo "Directory does not exist. Trying to create... ";
    if (@mkdir($targetDir, 0755, true)) {
        echo "<span style='color:green'>Created successfully.</span><br>";
    } else {
        echo "<span style='color:red'>Failed to create.</span><br>";
        $error = error_get_last();
        echo "Error: " . ($error['message'] ?? 'Unknown') . "<br>";
    }
} else {
    echo "Directory exists.<br>";
}

if (is_writable($targetDir)) {
    echo "<p style='color:green'>Directory is writable.</p>";
    // Try writing a file
    $testFile = $targetDir . 'test_write.txt';
    if (file_put_contents($testFile, 'test')) {
        echo "File write test successful.<br>";
        unlink($testFile);
    } else {
        echo "<span style='color:red'>File write test failed.</span><br>";
    }
} else {
    echo "<p style='color:red'>Directory is NOT writable.</p>";
}

echo "<h2>PHP Info</h2>";
// phpinfo(); 
