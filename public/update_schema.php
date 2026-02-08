<?php
// public/update_schema.php

// Script para ACTUALIZAR el esquema sin perder datos.
// Específicamente añade las columnas 'foto_perfil' si no existen.

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Actualización de Esquema de Base de Datos</h1>";

// Cargar configuración de entorno (si estamos en CodeIgniter, intentamos cargar .env)
// Nota: Al estar en public/, no tenemos acceso directo al framework completo fácilmente sin bootstrap,
// así que confiamos en getenv() que Render/Aiven inyectan.

$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$db = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>User:</strong> $user</li>";
echo "<li><strong>DB:</strong> $db</li>";
echo "</ul>";

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'><strong>✅ Conexión exitosa.</strong></p>";

    // Función auxiliar para verificar si existe una columna
    function columnExists($pdo, $table, $column)
    {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->fetch() !== false;
    }

    // 1. Actualizar tabla CLIENTE
    echo "<h3>Verificando tabla CLIENTE...</h3>";
    if (!columnExists($pdo, 'CLIENTE', 'foto_perfil')) {
        echo "Columna 'foto_perfil' no encontrada. Agregando... ";
        $pdo->exec("ALTER TABLE CLIENTE ADD COLUMN foto_perfil VARCHAR(255) AFTER ciudad");
        echo "<span style='color:green'>OK</span><br>";
    } else {
        echo "Columna 'foto_perfil' ya existe.<br>";
    }

    // 2. Actualizar tabla CONTRATISTA
    echo "<h3>Verificando tabla CONTRATISTA...</h3>";
    if (!columnExists($pdo, 'CONTRATISTA', 'foto_perfil')) {
        echo "Columna 'foto_perfil' no encontrada. Agregando... ";
        $pdo->exec("ALTER TABLE CONTRATISTA ADD COLUMN foto_perfil VARCHAR(255) AFTER ubicacion_mapa");
        echo "<span style='color:green'>OK</span><br>";
    } else {
        echo "Columna 'foto_perfil' ya existe.<br>";
    }

    echo "<hr>";
    echo "<p style='color:green; font-size:1.2em;'><strong>✅ Actualización completada correctamente.</strong></p>";
    echo "<p>Ahora puedes borrar este archivo o dejarlo para futuras actualizaciones.</p>";

} catch (PDOException $e) {
    echo "<p style='color:red'><strong>❌ Error de conexión o ejecución:</strong> " . $e->getMessage() . "</p>";
}
