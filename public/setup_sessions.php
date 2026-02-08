<?php
// public/setup_sessions.php

// Configuración de seguridad básica
header('Content-Type: text/html; charset=utf-8');
echo "<h1>Instalador de Tabla de Sesiones (CodeIgniter 4)</h1>";

// 1. Obtener credenciales (Compatible con Render y .env de CI4)
$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$db = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Database:</strong> $db</li>";
echo "</ul>";

// 2. Conexión PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Importante para conexiones SSL en la nube si no se tiene el certificado a mano
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'><strong>✅ Conexión a la base de datos exitosa.</strong></p>";

    // 3. Verificar si la tabla ya existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'ci_sessions'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:blue'>ℹ️ La tabla <code>ci_sessions</code> ya existe. No es necesario hacer nada.</p>";
    } else {
        // 4. Crear la tabla
        echo "<p>Creando tabla <code>ci_sessions</code>...</p>";

        $sql = "
            CREATE TABLE `ci_sessions` (
                `id` varchar(128) NOT NULL,
                `ip_address` varchar(45) NOT NULL,
                `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
                `data` blob NOT NULL,
                KEY `ci_sessions_timestamp` (`timestamp`),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";

        $pdo->exec($sql);
        echo "<p style='color:green; font-size:1.2em'><strong>✅ Tabla `ci_sessions` creada correctamente.</strong></p>";
        echo "<p>Ahora puedes cambiar el driver de sesión a 'DatabaseHandler' en tu configuración.</p>";
    }

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error de Base de Datos</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<p><strong>Pista:</strong> Verifica que las variables de entorno (DB_HOST, DB_USER, etc.) estén configuradas correctamente en Render.</p>";
    }
}
