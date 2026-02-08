<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MensajeModel;
use App\Models\ClienteModel;
use App\Models\ContratistaModel;

class Mensajes extends BaseController
{
    protected $mensajeModel;
    protected $clienteModel;
    protected $contratistaModel;
    protected $session;

    public function __construct()
    {
        $this->mensajeModel = new MensajeModel();
        $this->clienteModel = new ClienteModel();
        $this->contratistaModel = new ContratistaModel();
        $this->session = session();
    }

    private function getUsuarioActual()
    {
        $user = $this->session->get('user');
        
        if (!$user) {
            return null;
        }
        
        // Estructura estándar de session()->get('user')
        // user = ['id' => X, 'rol' => 'cliente'|'contratista', 'nombre' => ..., etc.]
        return [
            'id' => $user['id'] ?? null,
            'rol' => $user['rol'] ?? null
        ];
    }

    public function index()
    {
        $usuario = $this->getUsuarioActual();
        if (!$usuario) {
            return redirect()->to('/auth/login');
        }

        $conversacionesRaw = $this->mensajeModel->getConversaciones($usuario['id'], $usuario['rol']);

        $conversaciones = [];
        foreach ($conversacionesRaw as $conv) {
            $nombre = 'Usuario Desconocido';
            $foto = 'default-profile.png'; // Placeholder

            if ($conv['otro_usuario_rol'] == 'cliente') {
                $cliente = $this->clienteModel->find($conv['otro_usuario_id']);
                if ($cliente) {
                    $nombre = $cliente['nombre'] . ' ' . $cliente['apellido'];
                    // Si tuvieran foto, la asignaríamos aquí
                }
            } else {
                $contratista = $this->contratistaModel->find($conv['otro_usuario_id']);
                if ($contratista) {
                    $nombre = $contratista['nombre'] . ' ' . $contratista['apellido'];
                    // Si tuvieran foto
                }
            }

            // Obtener el último mensaje para mostrar preview
            // Esto podría optimizarse en la query principal, pero por ahora lo hacemos simple
            $ultimoMensaje = $this->mensajeModel->where(function ($builder) use ($usuario, $conv) {
                $builder->groupStart()
                    ->where('remitente_id', $usuario['id'])->where('remitente_rol', $usuario['rol'])
                    ->where('destinatario_id', $conv['otro_usuario_id'])->where('destinatario_rol', $conv['otro_usuario_rol'])
                    ->groupEnd();
            })->orWhere(function ($builder) use ($usuario, $conv) {
                $builder->groupStart()
                    ->where('remitente_id', $conv['otro_usuario_id'])->where('remitente_rol', $conv['otro_usuario_rol'])
                    ->where('destinatario_id', $usuario['id'])->where('destinatario_rol', $usuario['rol'])
                    ->groupEnd();
            })->orderBy('creado_en', 'DESC')->first();


            $conversaciones[] = [
                'id' => $conv['otro_usuario_id'],
                'rol' => $conv['otro_usuario_rol'],
                'nombre' => $nombre,
                'ultimo_mensaje' => $ultimoMensaje ? $ultimoMensaje['contenido'] : '',
                'fecha' => $conv['ultimo_mensaje_fecha'],
                'leido' => ($ultimoMensaje && $ultimoMensaje['destinatario_id'] == $usuario['id'] && $ultimoMensaje['leido'] == 0) ? false : true
            ];
        }

        return view('mensajes/index', ['conversaciones' => $conversaciones]);
    }

    public function chat($otroId, $otroRol)
    {
        $usuario = $this->getUsuarioActual();
        if (!$usuario) {
            return redirect()->to('/auth/login');
        }

        // Marcar como leídos
        $this->mensajeModel->marcarComoLeidos($usuario['id'], $usuario['rol'], $otroId, $otroRol);

        $mensajes = $this->mensajeModel->getMensajesChat($usuario['id'], $usuario['rol'], $otroId, $otroRol);

        // Obtener datos del otro usuario
        $nombreOtro = 'Usuario';
        if ($otroRol == 'cliente') {
            $c = $this->clienteModel->find($otroId);
            if ($c)
                $nombreOtro = $c['nombre'] . ' ' . $c['apellido'];
        } else {
            $c = $this->contratistaModel->find($otroId);
            if ($c)
                $nombreOtro = $c['nombre'] . ' ' . $c['apellido'];
        }

        return view('mensajes/chat', [
            'mensajes' => $mensajes,
            'otroId' => $otroId,
            'otroRol' => $otroRol,
            'nombreOtro' => $nombreOtro,
            'miId' => $usuario['id'],
            'miRol' => $usuario['rol']
        ]);
    }

    public function enviar()
    {
        $usuario = $this->getUsuarioActual();
        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $destinatarioId = $this->request->getPost('destinatario_id');
        $destinatarioRol = $this->request->getPost('destinatario_rol');
        $contenido = $this->request->getPost('contenido');

        if (empty($contenido)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mensaje vacío']);
        }

        $data = [
            'remitente_id' => $usuario['id'],
            'remitente_rol' => $usuario['rol'],
            'destinatario_id' => $destinatarioId,
            'destinatario_rol' => $destinatarioRol,
            'contenido' => $contenido,
            'leido' => 0
        ];

        $this->mensajeModel->insert($data);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function nuevos($otroId, $otroRol)
    {
        $usuario = $this->getUsuarioActual();
        if (!$usuario)
            return $this->response->setJSON([]);

        // Buscar mensajes no leídos de este usuario específico
        $nuevos = $this->mensajeModel->where('remitente_id', $otroId)
            ->where('remitente_rol', $otroRol)
            ->where('destinatario_id', $usuario['id'])
            ->where('destinatario_rol', $usuario['rol'])
            ->where('leido', 0)
            ->findAll();

        if (!empty($nuevos)) {
            $this->mensajeModel->marcarComoLeidos($usuario['id'], $usuario['rol'], $otroId, $otroRol);
        }

        return $this->response->setJSON($nuevos);
    }
}
