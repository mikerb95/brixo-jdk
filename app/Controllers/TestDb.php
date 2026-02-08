<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Throwable;

class TestDb extends Controller
{
    public function index()
    {
        // Deshabilitar el debugbar para esta vista limpia si estuviera activo
        // service('debugbar')->disable(); 

        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Prueba de Conexión DB</title>
            <style>
                body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
                .success { color: green; border: 1px solid green; padding: 15px; background: #eaffea; border-radius: 5px; }
                .error { color: red; border: 1px solid red; padding: 15px; background: #ffeaea; border-radius: 5px; }
                pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <h1>Diagnóstico de Conexión a Base de Datos</h1>';

        try {
            $db = db_connect();

            // Intentar conectar explícitamente
            $db->initialize();

            // Ejecutar una consulta simple
            $query = $db->query('SELECT 1 as test');
            $result = $query->getRow();

            if ($result && $result->test == 1) {
                echo '<div class="success">
                    <h2>✅ Conexión Exitosa</h2>
                    <p>Se ha establecido conexión correctamente con la base de datos.</p>
                    <ul>
                        <li><strong>Host:</strong> ' . esc($db->hostname) . '</li>
                        <li><strong>Base de Datos:</strong> ' . esc($db->database) . '</li>
                        <li><strong>Usuario:</strong> ' . esc($db->username) . '</li>
                        <li><strong>Driver:</strong> ' . esc($db->DBDriver) . '</li>
                        <li><strong>Versión del Servidor:</strong> ' . esc($db->getVersion()) . '</li>
                    </ul>
                </div>';
            } else {
                throw new \Exception("La consulta de prueba no devolvió el resultado esperado.");
            }

        } catch (Throwable $e) {
            echo '<div class="error">
                <h2>❌ Error de Conexión</h2>
                <p>No se pudo conectar a la base de datos.</p>
                <p><strong>Mensaje:</strong> ' . esc($e->getMessage()) . '</p>
                <p><strong>Archivo:</strong> ' . esc($e->getFile()) . ' en línea ' . $e->getLine() . '</p>
            </div>';

            echo '<h3>Detalles Técnicos (Stack Trace):</h3>';
            echo '<pre>' . esc($e->getTraceAsString()) . '</pre>';

            echo '<h3>Configuración Detectada (Parcial):</h3>';
            echo '<ul>';
            // Intentar mostrar variables de entorno si existen (sin mostrar password)
            echo '<li>env(database.default.hostname): ' . (env('database.default.hostname') ? esc(env('database.default.hostname')) : '<em>No definido en .env</em>') . '</li>';
            echo '<li>env(database.default.database): ' . (env('database.default.database') ? esc(env('database.default.database')) : '<em>No definido en .env</em>') . '</li>';
            echo '<li>env(database.default.username): ' . (env('database.default.username') ? esc(env('database.default.username')) : '<em>No definido en .env</em>') . '</li>';
            echo '</ul>';
        }

        echo '</body></html>';
    }
}
