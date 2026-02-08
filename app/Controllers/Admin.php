<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Admin Controller – Panel de Administración
 * ────────────────────────────────────────────
 *
 * CRUD completo de usuarios (clientes, contratistas, admins).
 * Solo accesible por usuarios con rol 'admin'.
 *
 * Rutas:
 *   GET  /admin              → Dashboard principal
 *   GET  /admin/usuarios     → Listado de todos los usuarios
 *   GET  /admin/usuarios/crear        → Formulario de creación
 *   POST /admin/usuarios/guardar      → Guardar nuevo usuario
 *   GET  /admin/usuarios/editar/:tipo/:id  → Formulario de edición
 *   POST /admin/usuarios/actualizar   → Actualizar usuario
 *   GET  /admin/usuarios/eliminar/:tipo/:id → Eliminar usuario
 */
class Admin extends BaseController
{
    /**
     * Verifica rol admin. Se llama al inicio de cada método.
     */
    private function requireAdmin()
    {
        $user = session()->get('user');
        if (!$user || ($user['rol'] ?? '') !== 'admin') {
            session()->setFlashdata('login_error', 'Acceso restringido a administradores.');
            return redirect()->to('/login');
        }
        return null;
    }

    /**
     * GET /admin — Dashboard principal
     */
    public function index()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();

        // Conteos
        $totalClientes = $db->table('CLIENTE')->countAllResults();
        $totalContratistas = $db->table('CONTRATISTA')->countAllResults();
        $totalAdmins = 0;
        try {
            $totalAdmins = $db->table('ADMIN')->countAllResults();
        } catch (\Exception $e) {}

        // Últimos registros
        $ultimosClientes = $db->table('CLIENTE')
            ->orderBy('creado_en', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $ultimosContratistas = $db->table('CONTRATISTA')
            ->orderBy('creado_en', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Solicitudes recientes
        $totalSolicitudes = 0;
        try {
            $totalSolicitudes = $db->table('SOLICITUD')->countAllResults();
        } catch (\Exception $e) {}

        // Analytics events (últimas 24h)
        $eventosHoy = 0;
        try {
            $eventosHoy = $db->table('analytics_events')
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->countAllResults();
        } catch (\Exception $e) {}

        $data = [
            'title' => 'Panel de Administración',
            'user' => session()->get('user'),
            'totalClientes' => $totalClientes,
            'totalContratistas' => $totalContratistas,
            'totalAdmins' => $totalAdmins,
            'totalSolicitudes' => $totalSolicitudes,
            'eventosHoy' => $eventosHoy,
            'ultimosClientes' => $ultimosClientes,
            'ultimosContratistas' => $ultimosContratistas,
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * GET /admin/usuarios — Listado de todos los usuarios
     */
    public function usuarios()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $filtro = $this->request->getGet('tipo') ?? 'todos';
        $busqueda = trim($this->request->getGet('q') ?? '');

        $clientes = [];
        $contratistas = [];
        $admins = [];

        if ($filtro === 'todos' || $filtro === 'clientes') {
            $builder = $db->table('CLIENTE');
            if ($busqueda !== '') {
                $builder->groupStart()
                    ->like('nombre', $busqueda)
                    ->orLike('correo', $busqueda)
                    ->groupEnd();
            }
            $clientes = $builder->orderBy('creado_en', 'DESC')->get()->getResultArray();
        }

        if ($filtro === 'todos' || $filtro === 'contratistas') {
            $builder = $db->table('CONTRATISTA');
            if ($busqueda !== '') {
                $builder->groupStart()
                    ->like('nombre', $busqueda)
                    ->orLike('correo', $busqueda)
                    ->groupEnd();
            }
            $contratistas = $builder->orderBy('creado_en', 'DESC')->get()->getResultArray();
        }

        if ($filtro === 'todos' || $filtro === 'admins') {
            try {
                $builder = $db->table('ADMIN');
                if ($busqueda !== '') {
                    $builder->groupStart()
                        ->like('nombre', $busqueda)
                        ->orLike('correo', $busqueda)
                        ->groupEnd();
                }
                $admins = $builder->orderBy('creado_en', 'DESC')->get()->getResultArray();
            } catch (\Exception $e) {}
        }

        $data = [
            'title' => 'Gestión de Usuarios',
            'user' => session()->get('user'),
            'clientes' => $clientes,
            'contratistas' => $contratistas,
            'admins' => $admins,
            'filtro' => $filtro,
            'busqueda' => $busqueda,
        ];

        return view('admin/usuarios', $data);
    }

    /**
     * GET /admin/usuarios/crear — Formulario de creación
     */
    public function crear()
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $data = [
            'title' => 'Crear Usuario',
            'user' => session()->get('user'),
            'tipo' => $this->request->getGet('tipo') ?? 'cliente',
        ];

        return view('admin/usuario_form', $data);
    }

    /**
     * POST /admin/usuarios/guardar — Guardar nuevo usuario
     */
    public function guardar(): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $tipo = $this->request->getPost('tipo');
        $nombre = trim($this->request->getPost('nombre') ?? '');
        $correo = trim($this->request->getPost('correo') ?? '');
        $password = $this->request->getPost('contrasena') ?? '';
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $ciudad = trim($this->request->getPost('ciudad') ?? '');

        // Validación básica
        if ($nombre === '' || $correo === '' || $password === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nombre, correo y contraseña son obligatorios.');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()
                ->with('error', 'El correo electrónico no es válido.');
        }

        // Verificar duplicados en las tres tablas
        $existsCliente = $db->table('CLIENTE')->where('correo', $correo)->countAllResults() > 0;
        $existsContratista = $db->table('CONTRATISTA')->where('correo', $correo)->countAllResults() > 0;
        $existsAdmin = false;
        try {
            $existsAdmin = $db->table('ADMIN')->where('correo', $correo)->countAllResults() > 0;
        } catch (\Exception $e) {}

        if ($existsCliente || $existsContratista || $existsAdmin) {
            return redirect()->back()->withInput()
                ->with('error', 'Ya existe un usuario con ese correo electrónico.');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            if ($tipo === 'cliente') {
                $db->table('CLIENTE')->insert([
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'contrasena' => $hashedPassword,
                    'telefono' => $telefono ?: null,
                    'ciudad' => $ciudad ?: null,
                ]);
            } elseif ($tipo === 'contratista') {
                $experiencia = trim($this->request->getPost('experiencia') ?? '');
                $descripcion = trim($this->request->getPost('descripcion_perfil') ?? '');

                $db->table('CONTRATISTA')->insert([
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'contrasena' => $hashedPassword,
                    'telefono' => $telefono ?: null,
                    'ciudad' => $ciudad ?: null,
                    'experiencia' => $experiencia ?: null,
                    'descripcion_perfil' => $descripcion ?: null,
                    'verificado' => (int) ($this->request->getPost('verificado') ?? 0),
                ]);
            } elseif ($tipo === 'admin') {
                $db->table('ADMIN')->insert([
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'contrasena' => $hashedPassword,
                    'activo' => 1,
                ]);
            }

            return redirect()->to('/admin/usuarios')
                ->with('message', "Usuario '{$nombre}' creado correctamente como {$tipo}.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/usuarios/editar/:tipo/:id — Formulario de edición
     */
    public function editar(string $tipo, int $id)
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $usuario = null;

        if ($tipo === 'cliente') {
            $usuario = $db->table('CLIENTE')->where('id_cliente', $id)->get()->getRowArray();
        } elseif ($tipo === 'contratista') {
            $usuario = $db->table('CONTRATISTA')->where('id_contratista', $id)->get()->getRowArray();
        } elseif ($tipo === 'admin') {
            try {
                $usuario = $db->table('ADMIN')->where('id_admin', $id)->get()->getRowArray();
            } catch (\Exception $e) {}
        }

        if ($usuario === null) {
            return redirect()->to('/admin/usuarios')
                ->with('error', 'Usuario no encontrado.');
        }

        $data = [
            'title' => 'Editar Usuario',
            'user' => session()->get('user'),
            'tipo' => $tipo,
            'usuario' => $usuario,
            'editando' => true,
        ];

        return view('admin/usuario_form', $data);
    }

    /**
     * POST /admin/usuarios/actualizar — Actualizar usuario existente
     */
    public function actualizar(): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $tipo = $this->request->getPost('tipo');
        $id = (int) $this->request->getPost('id');
        $nombre = trim($this->request->getPost('nombre') ?? '');
        $correo = trim($this->request->getPost('correo') ?? '');
        $password = $this->request->getPost('contrasena') ?? '';
        $telefono = trim($this->request->getPost('telefono') ?? '');
        $ciudad = trim($this->request->getPost('ciudad') ?? '');

        if ($nombre === '' || $correo === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nombre y correo son obligatorios.');
        }

        try {
            if ($tipo === 'cliente') {
                $updateData = [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'telefono' => $telefono ?: null,
                    'ciudad' => $ciudad ?: null,
                ];
                if ($password !== '') {
                    $updateData['contrasena'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $db->table('CLIENTE')->where('id_cliente', $id)->update($updateData);

            } elseif ($tipo === 'contratista') {
                $updateData = [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'telefono' => $telefono ?: null,
                    'ciudad' => $ciudad ?: null,
                    'experiencia' => trim($this->request->getPost('experiencia') ?? '') ?: null,
                    'descripcion_perfil' => trim($this->request->getPost('descripcion_perfil') ?? '') ?: null,
                    'verificado' => (int) ($this->request->getPost('verificado') ?? 0),
                ];
                if ($password !== '') {
                    $updateData['contrasena'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $db->table('CONTRATISTA')->where('id_contratista', $id)->update($updateData);

            } elseif ($tipo === 'admin') {
                $updateData = [
                    'nombre' => $nombre,
                    'correo' => $correo,
                    'activo' => (int) ($this->request->getPost('activo') ?? 1),
                ];
                if ($password !== '') {
                    $updateData['contrasena'] = password_hash($password, PASSWORD_BCRYPT);
                }
                $db->table('ADMIN')->where('id_admin', $id)->update($updateData);
            }

            return redirect()->to('/admin/usuarios')
                ->with('message', "Usuario '{$nombre}' actualizado correctamente.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/usuarios/eliminar/:tipo/:id — Eliminar usuario
     */
    public function eliminar(string $tipo, int $id): RedirectResponse
    {
        if ($redirect = $this->requireAdmin()) return $redirect;

        $db = db_connect();
        $currentUser = session()->get('user');

        // Protección: no puede eliminarse a sí mismo
        if ($tipo === 'admin' && $id === (int) $currentUser['id']) {
            return redirect()->to('/admin/usuarios')
                ->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        try {
            if ($tipo === 'cliente') {
                $db->table('CLIENTE')->where('id_cliente', $id)->delete();
            } elseif ($tipo === 'contratista') {
                $db->table('CONTRATISTA')->where('id_contratista', $id)->delete();
            } elseif ($tipo === 'admin') {
                $db->table('ADMIN')->where('id_admin', $id)->delete();
            }

            return redirect()->to('/admin/usuarios')
                ->with('message', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->to('/admin/usuarios')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
