<?php
// public/debug_session_db.php

// Configuración para mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Diagnóstico de Sesiones en Base de Datos (Modo Seguro)</h1>";

// 1. Obtener credenciales directamente del entorno (Como lo hace Render)
$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$db = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

echo "<h2>1. Verificación de Credenciales</h2>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Database:</strong> $db</li>";
echo "<li><strong>User:</strong> " . ($user ? '******' : 'Vacío') . "</li>";
echo "</ul>";

// 2. Conexión PDO Directa
echo "<h2>2. Prueba de Conexión</h2>";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "<p style='color:green'><strong>✅ Conexión exitosa a la base de datos.</strong></p>";

    // 3. Verificar Tabla ci_sessions
    echo "<h2>3. Estado de la Tabla de Sesiones</h2>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'ci_sessions'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'><strong>✅ La tabla `ci_sessions` EXISTE.</strong></p>";

        // Contar sesiones
        $count = $pdo->query("SELECT COUNT(*) FROM ci_sessions")->fetchColumn();
        echo "<p>Sesiones activas actualmente: <strong>$count</strong></p>";

        // 4. Prueba de Escritura (CRÍTICO)
        echo "<h2>4. Prueba de Escritura (INSERT/DELETE)</h2>";
        $testId = 'test_' . bin2hex(random_bytes(8));
        $ip = '127.0.0.1';
        $ts = time();
        $blob = 'data_prueba';

        try {
            $sql = "INSERT INTO ci_sessions (id, ip_address, timestamp, data) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$testId, $ip, $ts, $blob]);
            echo "<p style='color:green'><strong>✅ Escritura exitosa:</strong> Se pudo insertar una sesión de prueba.</p>";

            // Borrar prueba
            $pdo->exec("DELETE FROM ci_sessions WHERE id = '$testId'");
            echo "<p style='color:green'><strong>✅ Borrado exitoso:</strong> Se eliminó la sesión de prueba.</p>";

        } catch (PDOException $e) {
            echo "<p style='color:red; background:#ffe6e6; padding:10px; border:1px solid red;'>
                <strong>❌ ERROR DE ESCRITURA:</strong> No se pudo escribir en la tabla.<br>
                Mensaje: " . $e->getMessage() . "
            </p>";
        }

    } else {
        echo "<p style='color:red; background:#ffe6e6; padding:10px; border:1px solid red;'>
            <strong>❌ ERROR CRÍTICO:</strong> La tabla `ci_sessions` NO EXISTE.
            <br>Ejecuta el script de instalación nuevamente.
        </p>";
    }

} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ Falló la conexión</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}

// 5. Información de Cookies (PHP)
echo "<h2>5. Configuración de Cookies (PHP.ini)</h2>";
echo "<ul>";
echo "<li><strong>session.save_handler:</strong> " . ini_get('session.save_handler') . " (Debería ser 'user' o similar en CI4, pero aquí muestra el default de PHP)</li>";
echo "<li><strong>session.cookie_secure:</strong> " . (ini_get('session.cookie_secure') ? 'On' : 'Off') . "</li>";
echo "<li><strong>session.cookie_httponly:</strong> " . (ini_get('session.cookie_httponly') ? 'On' : 'Off') . "</li>";
echo "<li><strong>session.cookie_samesite:</strong> " . ini_get('session.cookie_samesite') . "</li>";
echo "</ul>";
?>