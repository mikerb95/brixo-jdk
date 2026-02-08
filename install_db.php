<?php
// install_db.php
// Instalador: conecta a MySQL, elimina tablas actuales y aplica schema + seed.

// Leer parámetros desde stdin con valores por defecto para XAMPP
function prompt($label, $default = '')
{
    echo $label . ($default !== '' ? " (por defecto: $default)" : '') . ": ";
    $h = fopen('php://stdin', 'r');
    $v = trim(fgets($h));
    fclose($h);
    return $v !== '' ? $v : $default;
}

$host = prompt('Host', '127.0.0.1');
$port = (int) prompt('Puerto', '3306');
$db = prompt('Base de datos', 'brixo');
$user = prompt('Usuario', 'root');
echo "Contraseña: ";
$h = fopen('php://stdin', 'r');
$pass = trim(fgets($h));
fclose($h);

// Preguntar por SSL (necesario para Aiven y otras nubes)
$ssl = prompt('¿Usar SSL (Requerido para Aiven)? (s/n)', 'n');
$sslCa = '';
if (strtolower($ssl) === 's') {
    $sslCa = prompt('Ruta al certificado CA (ca.pem) [Dejar vacío si no tienes uno]', '');
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
];

if (strtolower($ssl) === 's') {
    if (!empty($sslCa)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    }
    // Desactivar verificación estricta del certificado del servidor
    // Esto ayuda si el certificado es autofirmado o el hostname no coincide exactamente
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}

try {
    echo "\nConectando a la base de datos...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexión exitosa.\n";

    // Desactivar restricciones para eliminar en orden seguro
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    // Intentar eliminar tablas existentes (legado y nuevas)
    $toDrop = [
        // Nuevo esquema
        'RESENA',
        'CONTRATO',
        'COTIZACION',
        'USUARIO',
        // Esquema legado (por si quedan)
        'ADMINISTRADOR',
        'CONTRATISTA',
        'CLIENTE',
        'SERVICIO',
        'RESENAS',
        'COTIZACIONES',
        'CONTRATOS'
    ];
    foreach ($toDrop as $tbl) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$tbl`");
            echo "Eliminada (si existía): $tbl\n";
        } catch (PDOException $e) {
            echo "Aviso al eliminar $tbl: " . $e->getMessage() . "\n";
        }
    }

    // Reactivar restricciones
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    // Función auxiliar para ejecutar archivos SQL
    function runSqlFile($pdo, $filePath)
    {
        echo "\nProcesando " . basename($filePath) . "...\n";
        if (!file_exists($filePath)) {
            echo "ERROR: El archivo $filePath no existe.\n";
            return false;
        }
        $sql = file_get_contents($filePath);
        try {
            $pdo->exec($sql);
            echo "ÉXITO: " . basename($filePath) . " importado correctamente.\n";
            return true;
        } catch (PDOException $e) {
            echo "ERROR al importar " . basename($filePath) . ": " . $e->getMessage() . "\n";
            return false;
        }
    }

    // Ejecutar schema y seed
    $okSchema = runSqlFile($pdo, __DIR__ . '/public/schema.sql');
    if ($okSchema) {
        runSqlFile($pdo, __DIR__ . '/public/seed.sql');
    } else {
        echo "Saltando seed por error en schema.\n";
    }
} catch (\PDOException $e) {
    echo "\nERROR CRÍTICO DE CONEXIÓN:\n";
    echo $e->getMessage() . "\n";
    echo "Código: " . $e->getCode() . "\n";
}
