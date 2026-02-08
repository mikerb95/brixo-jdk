<?php
/**
 * Setup: Tabla ADMIN
 * ──────────────────
 * Ejecuta este archivo UNA VEZ para crear la tabla de administradores
 * y el usuario admin por defecto.
 *
 * Acceder a: /setup_admin.php
 *
 * Credenciales por defecto:
 *   Correo:    admin@brixo.co
 *   Contraseña: Admin123!
 */

header('Content-Type: text/html; charset=utf-8');

// Leer variables de entorno (mismo patrón que setup_db.php)
$host = getenv('database.default.hostname') ?: getenv('DB_HOST') ?: 'localhost';
$user = getenv('database.default.username') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('database.default.password') ?: getenv('DB_PASSWORD') ?: '';
$dbname = getenv('database.default.database') ?: getenv('DB_NAME') ?: 'brixo';
$port = getenv('database.default.port') ?: getenv('DB_PORT') ?: 3306;

echo "<h2>🔧 Setup: Tabla ADMIN</h2>";
echo "<pre style='background:#222;color:#0f0;padding:20px;border-radius:8px;'>";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Conexión a BD exitosa ({$host}:{$port}/{$dbname})\n\n";
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "</pre>";
    exit;
}

// 1. Crear tabla ADMIN
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ADMIN (
            id_admin INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            correo VARCHAR(255) NOT NULL UNIQUE,
            contrasena VARCHAR(255) NOT NULL,
            foto_perfil VARCHAR(255) DEFAULT NULL,
            activo TINYINT(1) DEFAULT 1,
            creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
            ultimo_acceso DATETIME DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabla ADMIN creada correctamente.\n";
} catch (PDOException $e) {
    echo "⚠️  Error creando tabla ADMIN: " . $e->getMessage() . "\n";
}

// 2. Insertar admin por defecto (solo si no existe)
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ADMIN WHERE correo = ?");
    $stmt->execute(['admin@brixo.co']);
    $exists = (int) $stmt->fetchColumn();

    if ($exists === 0) {
        $hash = password_hash('Admin123!', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO ADMIN (nombre, correo, contrasena, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute(['Administrador', 'admin@brixo.co', $hash]);
        echo "✅ Admin por defecto creado: admin@brixo.co / Admin123!\n";
    } else {
        echo "ℹ️  El admin admin@brixo.co ya existe, no se insertó.\n";
    }
} catch (PDOException $e) {
    echo "⚠️  Error insertando admin: " . $e->getMessage() . "\n";
}

echo "\n──────────────────────────────────────────────\n";
echo "🎉 Setup completado.\n";
echo "   Accede al panel admin en: /admin\n";
echo "   Correo:    admin@brixo.co\n";
echo "   Contraseña: Admin123!\n";
echo "──────────────────────────────────────────────\n";
echo "</pre>";
