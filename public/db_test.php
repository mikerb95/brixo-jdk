<?php
// public/db_test.php

// Load environment variables if .env exists (CodeIgniter does this, but we are standalone)
// We will rely on getenv() which should be populated by Render

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$name = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;
$ssl = getenv('DB_SSL') === 'true';

echo "<h1>Database Connection Test</h1>";
echo "Host: $host<br>";
echo "User: $user<br>";
echo "Database: $name<br>";
echo "Port: $port<br>";
echo "SSL: " . ($ssl ? 'true' : 'false') . "<br>";

if (!$host || !$user || !$name) {
    die("Error: Missing database environment variables.");
}

$mysqli = mysqli_init();

if ($ssl) {
    // Render/Aiven usually requires SSL but doesn't strictly verify the cert if not provided
    // We can try setting SSL to true without specific certs if the server allows it,
    // or we might need to download the CA cert.
    // For now, let's try basic SSL.
    $mysqli->ssl_set(NULL, NULL, NULL, NULL, NULL);
}

// Try to connect
if (!$mysqli->real_connect($host, $user, $pass, $name, (int) $port, NULL, $ssl ? MYSQLI_CLIENT_SSL : 0)) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

echo 'Success... ' . $mysqli->host_info . "<br>";

// Check if ci_sessions table exists
$result = $mysqli->query("SHOW TABLES LIKE 'ci_sessions'");
if ($result->num_rows > 0) {
    echo "Table 'ci_sessions' EXISTS.<br>";
} else {
    echo "Table 'ci_sessions' DOES NOT EXIST.<br>";
}

$mysqli->close();
