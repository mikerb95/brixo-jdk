<?php

namespace App\Controllers;

class DebugAuth extends BaseController
{
    public function index()
    {
        $session = session();

        // Auto-increment counter to test persistence
        $count = $session->get('debug_counter') ?? 0;
        $session->set('debug_counter', $count + 1);

        // Set a timestamp
        if (!$session->has('debug_start_time')) {
            $session->set('debug_start_time', date('Y-m-d H:i:s'));
        }

        echo "<!DOCTYPE html><html><head><title>Auth Debugger</title>";
        echo "<style>body{font-family:sans-serif; padding:20px;} .pass{color:green; font-weight:bold;} .fail{color:red; font-weight:bold;} .warn{color:orange; font-weight:bold;} table{border-collapse:collapse; width:100%;} td,th{border:1px solid #ddd; padding:8px; text-align:left;} tr:nth-child(even){background-color:#f2f2f2;}</style>";
        echo "</head><body>";

        echo "<h1>🕵️‍♂️ Diagnóstico de Sesión y Autenticación</h1>";
        echo "<p><a href='/debug-auth'>🔄 Recargar Página</a> (Haz clic aquí para probar persistencia)</p>";

        echo "<h2>1. Prueba de Persistencia</h2>";
        echo "<ul>";
        echo "<li><strong>Contador de Sesión:</strong> $count " . ($count > 0 ? "<span class='pass'>✅ (Persistiendo)</span>" : "<span class='warn'>⚠️ (Primer carga o reiniciado)</span>") . "</li>";
        echo "<li><strong>ID de Sesión:</strong> " . session_id() . "</li>";
        echo "<li><strong>Inicio de Sesión:</strong> " . $session->get('debug_start_time') . "</li>";
        echo "</ul>";

        if ($count == 0) {
            echo "<p class='warn'>ℹ️ Si recargas la página y el contador sigue en 0, las sesiones NO están funcionando.</p>";
        } else {
            echo "<p class='pass'>✅ Las sesiones están funcionando correctamente en el servidor.</p>";
        }

        echo "<h2>2. Diagnóstico de Cookies</h2>";
        $cookieName = config('Session')->cookieName;
        echo "<table>";
        echo "<tr><th>Variable</th><th>Valor</th><th>Estado</th></tr>";

        $hasCookie = isset($_COOKIE[$cookieName]);
        echo "<tr><td>Cookie Navegador ($cookieName)</td><td>" . ($hasCookie ? 'Presente' : 'Ausente') . "</td><td>" . ($hasCookie ? '<span class="pass">OK</span>' : '<span class="fail">FAIL</span>') . "</td></tr>";

        echo "<tr><td>Config: Secure</td><td>" . (config('Cookie')->secure ? 'true' : 'false') . "</td><td>" . ($this->request->isSecure() && !config('Cookie')->secure ? '<span class="warn">Debería ser true en HTTPS</span>' : 'Info') . "</td></tr>";
        echo "<tr><td>Config: SameSite</td><td>" . config('Cookie')->samesite . "</td><td>Info</td></tr>";
        echo "<tr><td>Config: Domain</td><td>'" . config('Cookie')->domain . "'</td><td>" . (config('Cookie')->domain == '' ? 'Info (Auto)' : 'Info') . "</td></tr>";
        echo "</table>";

        echo "<h2>3. Entorno del Servidor</h2>";
        echo "<table>";
        echo "<tr><th>Variable</th><th>Valor</th></tr>";
        echo "<tr><td>HTTPS Detectado (request->isSecure())</td><td>" . ($this->request->isSecure() ? 'Sí' : 'No') . "</td></tr>";
        echo "<tr><td>\$_SERVER['HTTPS']</td><td>" . ($_SERVER['HTTPS'] ?? 'N/A') . "</td></tr>";
        echo "<tr><td>\$_SERVER['HTTP_X_FORWARDED_PROTO']</td><td>" . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'N/A') . "</td></tr>";
        echo "<tr><td>\$_SERVER['REMOTE_ADDR']</td><td>" . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "</td></tr>";
        echo "<tr><td>CI_ENVIRONMENT</td><td>" . env('CI_ENVIRONMENT') . "</td></tr>";
        echo "</table>";

        echo "<h2>4. Datos de Sesión Actuales</h2>";
        echo "<pre>" . print_r($session->get(), true) . "</pre>";

        echo "<h2>5. Configuración de Base de Datos (Session)</h2>";
        echo "Driver: " . config('Session')->driver . "<br>";
        echo "Save Path: " . config('Session')->savePath . "<br>";

        echo "</body></html>";
    }
}
