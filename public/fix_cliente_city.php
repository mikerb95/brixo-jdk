<?php
// Script para agregar la columna 'ciudad' a la tabla CLIENTE si no existe
// Ejecutar accediendo a: https://tu-dominio.com/fix_cliente_city.php

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

// Verificar si la columna ya existe
$checkColumn = $mysqli->query("SHOW COLUMNS FROM CLIENTE LIKE 'ciudad'");

if ($checkColumn->num_rows == 0) {
    // La columna no existe, agregarla
    $sql = "ALTER TABLE CLIENTE ADD COLUMN ciudad VARCHAR(100) AFTER telefono";

    if ($mysqli->query($sql) === TRUE) {
        echo "<h1>✅ Columna 'ciudad' agregada correctamente a la tabla CLIENTE.</h1>";
    } else {
        echo "<h1>❌ Error al agregar la columna:</h1>";
        echo "<p>" . $mysqli->error . "</p>";
    }
} else {
    echo "<h1>ℹ️ La columna 'ciudad' ya existe en la tabla CLIENTE.</h1>";
}

$mysqli->close();
