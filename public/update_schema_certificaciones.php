<?php
// public/update_schema_certificaciones.php

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Actualización de Esquema: Tabla CERTIFICACION</h1>";

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

    // Crear tabla CERTIFICACION si no existe
    $sql = "CREATE TABLE IF NOT EXISTS CERTIFICACION (
        id_certificado INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        entidad_emisora VARCHAR(255),
        fecha_obtenida DATE,
        id_contratista INT NOT NULL,
        CONSTRAINT fk_cert_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "<p style='color:green'><strong>✅ Tabla CERTIFICACION verificada/creada.</strong></p>";

} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
