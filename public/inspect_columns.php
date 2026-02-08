<?php
// public/inspect_columns.php

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Inspección de Columnas</h1>";

$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$db = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'><strong>✅ Conexión exitosa.</strong></p>";

    $tables = ['CLIENTE', 'CONTRATISTA'];

    foreach ($tables as $table) {
        echo "<h3>Tabla: $table</h3>";
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
        $columns = $stmt->fetchAll();
        echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
