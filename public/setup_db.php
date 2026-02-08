<?php
// public/setup_db.php

// SEGURIDAD: Este script está diseñado para ejecutarse una sola vez.
// DEBES BORRARLO O RENOMBRARLO DESPUÉS DE USARLO.

header('Content-Type: text/html; charset=utf-8');
echo "<h1>Instalación de Base de Datos (Automática)</h1>";

// Intentar leer variables de entorno (Soporte para CodeIgniter 4 y variables genéricas)
$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$db = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>User:</strong> $user</li>";
echo "<li><strong>DB:</strong> $db</li>";
echo "<li><strong>Port:</strong> $port</li>";
echo "</ul>";

if ($host === 'localhost' && empty($pass)) {
    echo "<p style='color:orange; border:1px solid orange; padding:10px;'>
        <strong>Advertencia:</strong> Pareces estar usando la configuración por defecto (localhost). 
        <br>Si estás en Render, asegúrate de haber configurado las 'Environment Variables' en el dashboard de tu servicio.
        <br>Variables esperadas: <code>database.default.hostname</code>, <code>database.default.username</code>, etc.
    </p>";
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    // Configuración SSL permisiva para nubes como Aiven/Azure sin necesidad de subir el certificado CA
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    echo "<p>Conectando a la base de datos...</p>";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'><strong>✅ Conexión exitosa.</strong></p>";

    // 1. Limpiar base de datos (Eliminar TODAS las tablas y vistas)
    echo "<p>Limpiando base de datos (Eliminando esquema actual)...</p>";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    // Obtener todas las tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "<p>La base de datos ya estaba vacía.</p>";
    } else {
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
            echo "<p style='font-size:0.9em; color:gray;'> - Tabla eliminada: $table</p>";
        }
    }

    // Obtener todas las vistas (si las hay)
    $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
    $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($views as $view) {
        $pdo->exec("DROP VIEW IF EXISTS `$view`");
        echo "<p style='font-size:0.9em; color:gray;'> - Vista eliminada: $view</p>";
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "<p><strong>Esquema eliminado correctamente.</strong></p>";

    // 2. Ejecutar Schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        echo "<p>Importando estructura (schema.sql)...</p>";
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
        echo "<p style='color:green'>✅ Estructura creada correctamente.</p>";
    } else {
        echo "<p style='color:red'>❌ Error: No se encuentra el archivo public/schema.sql</p>";
    }

    // 3. Ejecutar Seed
    $seedFile = __DIR__ . '/seed.sql';
    if (file_exists($seedFile)) {
        echo "<p>Importando datos de prueba (seed.sql)...</p>";
        $sql = file_get_contents($seedFile);
        $pdo->exec($sql);
        echo "<p style='color:green'>✅ Datos de prueba insertados correctamente.</p>";
    } else {
        echo "<p style='color:red'>❌ Error: No se encuentra el archivo public/seed.sql</p>";
    }

    echo "<hr>";
    echo "<h2 style='color:green'>🎉 Instalación Completada</h2>";
    echo "<p>Ya puedes usar tu aplicación.</p>";
    echo "<p style='background-color:#ffcccc; padding:10px; border:1px solid red;'><strong>⚠️ IMPORTANTE:</strong> Por seguridad, elimina el archivo <code>public/setup_db.php</code> de tu repositorio o servidor ahora mismo.</p>";
    echo "<a href='/'>Ir al Inicio</a>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>❌ Error Crítico</h2>";
    echo "<p>No se pudo completar la instalación:</p>";
    echo "<pre style='background:#f0f0f0; padding:10px;'>" . $e->getMessage() . "</pre>";
}
