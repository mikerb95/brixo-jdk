<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Solicitud extends BaseController
{
    public function nueva(): string|RedirectResponse
    {
        $session = session();
        $user = $session->get('user');

        // Solo clientes pueden crear solicitudes
        if (empty($user) || $user['rol'] !== 'cliente') {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión como cliente para crear una solicitud.');
        }

        // Si viene un ID de contratista en la URL (para solicitud directa)
        $idContratista = $this->request->getGet('contratista');
        $nombreContratista = '';

        if ($idContratista) {
            $db = db_connect();
            $contratista = $db->table('CONTRATISTA')->select('nombre')->where('id_contratista', $idContratista)->get()->getRowArray();
            if ($contratista) {
                $nombreContratista = $contratista['nombre'];
            }
        }

        // Pre-fill desde cotizador (si viene de /cotizador/confirmar)
        $prefill = $session->get('prefill_solicitud');
        $session->remove('prefill_solicitud');

        return view('solicitud/nueva', [
            'user' => $user,
            'id_contratista' => $idContratista,
            'nombre_contratista' => $nombreContratista,
            'prefill' => $prefill ?? [],
        ]);
    }

    public function guardar(): RedirectResponse
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'cliente') {
            return redirect()->to('/');
        }

        $titulo = trim((string) $this->request->getPost('titulo'));
        $descripcion = trim((string) $this->request->getPost('descripcion'));
        $presupuesto = $this->request->getPost('presupuesto');

        // Capturar ubicación detallada
        $departamento = trim((string) $this->request->getPost('departamento'));
        $ciudad = trim((string) $this->request->getPost('ciudad'));
        $direccion = trim((string) $this->request->getPost('ubicacion'));

        $ubicacionFinal = $direccion;
        if (!empty($ciudad) && !empty($departamento)) {
            $ubicacionFinal = $ciudad . ', ' . $departamento . ($direccion ? ' - ' . $direccion : '');
        }

        $idContratista = $this->request->getPost('id_contratista'); // Puede ser vacío (abierta)

        if ($titulo === '' || $descripcion === '') {
            return redirect()->back()->with('error', 'El título y la descripción son obligatorios.');
        }

        $data = [
            'id_cliente' => $user['id'],
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'presupuesto' => $presupuesto ?: 0,
            'ubicacion' => $ubicacionFinal,
            'estado' => 'ABIERTA',
            'id_contratista' => !empty($idContratista) ? $idContratista : null
        ];

        $db = db_connect();
        $db->table('SOLICITUD')->insert($data);

        $mensaje = !empty($idContratista)
            ? 'Solicitud enviada al contratista correctamente.'
            : 'Solicitud publicada en el tablón de tareas abiertas.';

        return redirect()->to('/panel')->with('message', $mensaje);
    }

    // Listar solicitudes abiertas (Para contratistas)
    public function index(): string|RedirectResponse
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'contratista') {
            return redirect()->to('/')->with('error', 'Acceso exclusivo para contratistas.');
        }

        $db = db_connect();

        // Obtener solicitudes abiertas (id_contratista IS NULL)
        $solicitudes = $db->query("
            SELECT s.*, c.nombre as nombre_cliente 
            FROM SOLICITUD s
            JOIN CLIENTE c ON c.id_cliente = s.id_cliente
            WHERE s.id_contratista IS NULL 
            AND s.estado = 'ABIERTA'
            ORDER BY s.creado_en DESC
        ")->getResultArray();

        return view('solicitud/lista', [
            'user' => $user,
            'solicitudes' => $solicitudes
        ]);
    }

    public function eliminar($id)
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'cliente') {
            return redirect()->to('/')->with('error', 'Acceso denegado.');
        }

        $db = db_connect();

        // Verificar que la solicitud pertenezca al usuario
        $solicitud = $db->table('SOLICITUD')
            ->where('id_solicitud', $id)
            ->where('id_cliente', $user['id'])
            ->get()
            ->getRowArray();

        if (!$solicitud) {
            return redirect()->to('/panel')->with('error', 'Solicitud no encontrada o no tienes permiso para eliminarla.');
        }

        // Eliminar
        $db->table('SOLICITUD')->where('id_solicitud', $id)->delete();

        return redirect()->to('/panel')->with('message', 'Solicitud eliminada correctamente.');
    }

    public function editar($id)
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'cliente') {
            return redirect()->to('/')->with('error', 'Acceso denegado.');
        }

        $db = db_connect();

        // Verificar que la solicitud pertenezca al usuario
        $solicitud = $db->table('SOLICITUD')
            ->where('id_solicitud', $id)
            ->where('id_cliente', $user['id'])
            ->get()
            ->getRowArray();

        if (!$solicitud) {
            return redirect()->to('/panel')->with('error', 'Solicitud no encontrada o no tienes permiso para editarla.');
        }

        return view('solicitud/editar', ['solicitud' => $solicitud]);
    }

    public function actualizar($id)
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'cliente') {
            return redirect()->to('/')->with('error', 'Acceso denegado.');
        }

        $db = db_connect();

        // Verificar que la solicitud pertenezca al usuario
        $solicitud = $db->table('SOLICITUD')
            ->where('id_solicitud', $id)
            ->where('id_cliente', $user['id'])
            ->get()
            ->getRowArray();

        if (!$solicitud) {
            return redirect()->to('/panel')->with('error', 'Solicitud no encontrada o no tienes permiso para editarla.');
        }

        $titulo = trim((string) $this->request->getPost('titulo'));
        $descripcion = trim((string) $this->request->getPost('descripcion'));
        $presupuesto = $this->request->getPost('presupuesto');
        $ubicacion = trim((string) $this->request->getPost('ubicacion'));

        if ($titulo === '' || $descripcion === '') {
            return redirect()->back()->with('error', 'El título y la descripción son obligatorios.');
        }

        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'presupuesto' => $presupuesto ?: 0,
            'ubicacion' => $ubicacion,
        ];

        $db->table('SOLICITUD')->where('id_solicitud', $id)->update($data);

        return redirect()->to('/panel')->with('message', 'Solicitud actualizada correctamente.');
    }
}
