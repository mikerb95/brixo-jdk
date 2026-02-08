<?php
// Script para crear la tabla MENSAJE en la base de datos
// Ejecutar accediendo a: https://tu-dominio.com/setup_mensajes.php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'brixo';

// Detectar credenciales de Render si existen
if (getenv('RENDER')) {
    $hostname = getenv('DB_HOST');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    $database = getenv('DB_NAME');
}

$mysqli = new mysqli($hostname, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$sql = "
CREATE TABLE IF NOT EXISTS MENSAJE (
    id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
    remitente_id INT NOT NULL,
    remitente_rol ENUM('cliente', 'contratista') NOT NULL,
    destinatario_id INT NOT NULL,
    destinatario_rol ENUM('cliente', 'contratista') NOT NULL,
    contenido TEXT NOT NULL,
    leido TINYINT(1) DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($mysqli->query($sql) === TRUE) {
    echo "<h1>✅ Tabla MENSAJE creada correctamente.</h1>";
    echo "<p>El sistema de mensajería está listo.</p>";
    echo "<p><a href='/'>Volver al inicio</a></p>";
} else {
    echo "<h1>❌ Error al crear la tabla:</h1>";
    echo "<p>" . $mysqli->error . "</p>";
}

$mysqli->close();
