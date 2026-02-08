<?php
// Script para crear la tabla SOLICITUD en la base de datos
// Ejecutar accediendo a: https://brixoci4.onrender.com/setup_solicitudes.php

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
CREATE TABLE IF NOT EXISTS SOLICITUD (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_contratista INT NULL, -- NULL significa que es una solicitud abierta para todos
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    presupuesto DECIMAL(12,2) DEFAULT 0,
    ubicacion VARCHAR(255),
    estado ENUM('ABIERTA', 'ASIGNADA', 'COMPLETADA', 'CANCELADA') DEFAULT 'ABIERTA',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitud_cliente FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente) ON DELETE CASCADE,
    CONSTRAINT fk_solicitud_contratista FOREIGN KEY (id_contratista) REFERENCES CONTRATISTA(id_contratista) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($mysqli->query($sql) === TRUE) {
    echo "<h1>✅ Tabla SOLICITUD creada correctamente.</h1>";
    echo "<p>Ahora puedes proceder a usar el sistema de solicitudes.</p>";
} else {
    echo "<h1>❌ Error al crear la tabla:</h1>";
    echo "<p>" . $mysqli->error . "</p>";
}

$mysqli->close();
