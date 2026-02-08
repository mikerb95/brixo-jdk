<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Solicitudes extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $user = session()->get('user');
        if (empty($user) || ($user['rol'] ?? '') !== 'contratista') {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión como contratista para ver tus solicitudes.');
        }

        $db = db_connect();
        // Listado de contratos/solicitudes asociados al contratista
        $solicitudes = $db->query(
            'SELECT ct.id_contrato, ct.estado, ct.fecha_inicio, ct.fecha_fin, ct.costo_total,
                    cli.nombre AS cliente, cli.telefono AS cliente_telefono
             FROM CONTRATO ct
             JOIN CLIENTE cli ON cli.id_cliente = ct.id_cliente
             WHERE ct.id_contratista = ?
             ORDER BY ct.fecha_inicio DESC
             LIMIT 50',
            [$user['id']]
        )->getResultArray();

        return view('solicitudes', [
            'user' => $user,
            'solicitudes' => $solicitudes,
        ]);
    }
}
