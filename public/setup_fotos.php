<?php
// Script para asegurar columnas de foto_perfil
// Ejecutar accediendo a: https://tu-dominio.com/setup_fotos.php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'brixo';

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

// Check CLIENTE
$result = $mysqli->query("SHOW COLUMNS FROM CLIENTE LIKE 'foto_perfil'");
if ($result->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE CLIENTE ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL")) {
        echo "<h1>✅ Columna 'foto_perfil' agregada a CLIENTE.</h1>";
    } else {
        echo "<h1>❌ Error agregando columna a CLIENTE: " . $mysqli->error . "</h1>";
    }
} else {
    echo "<h1>ℹ️ Columna 'foto_perfil' ya existe en CLIENTE.</h1>";
}

// Check CONTRATISTA
$result = $mysqli->query("SHOW COLUMNS FROM CONTRATISTA LIKE 'foto_perfil'");
if ($result->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE CONTRATISTA ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL")) {
        echo "<h1>✅ Columna 'foto_perfil' agregada a CONTRATISTA.</h1>";
    } else {
        echo "<h1>❌ Error agregando columna a CONTRATISTA: " . $mysqli->error . "</h1>";
    }
} else {
    echo "<h1>ℹ️ Columna 'foto_perfil' ya existe en CONTRATISTA.</h1>";
}

echo "<p><a href='/'>Volver al inicio</a></p>";

$mysqli->close();
?>