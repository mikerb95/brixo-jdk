<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function showLogin()
    {
        helper('form');
        $session = session();
        $data = [
            'login_error' => $session->getFlashdata('login_error'),
            'message' => $session->getFlashdata('message'),
        ];

        return view('auth/login', $data);
    }

    public function login(): RedirectResponse
    {
        $session = session();
        $email = trim((string) $this->request->getPost('correo'));
        $password = (string) $this->request->getPost('contrasena');

        if ($email === '' || $password === '') {
            $session->setFlashdata('login_error', 'Debes ingresar el correo y la contraseña.');
            return redirect()->back();
        }

        $db = db_connect();
        $usuario = $db->table('CLIENTE')->where('correo', $email)->get()->getRowArray();
        $rol = 'cliente';

        if ($usuario === null) {
            $usuario = $db->table('CONTRATISTA')->where('correo', $email)->get()->getRowArray();
            $rol = $usuario === null ? '' : 'contratista';
        }

        // Verificar si es admin
        if ($usuario === null) {
            try {
                $usuario = $db->table('ADMIN')->where('correo', $email)->where('activo', 1)->get()->getRowArray();
                $rol = $usuario === null ? '' : 'admin';
            } catch (\Exception $e) {
                // Tabla ADMIN no existe aún — ignorar
            }
        }

        if ($usuario === null) {
            $session->setFlashdata('login_error', 'No encontramos una cuenta asociada a ese correo.');
            return redirect()->back();
        }

        if (!password_verify($password, $usuario['contrasena'])) {
            $session->setFlashdata('login_error', 'La contraseña ingresada es incorrecta.');
            return redirect()->back();
        }

        if ($rol === 'admin') {
            $session->set('user', [
                'id' => $usuario['id_admin'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol' => 'admin',
                'foto_perfil' => $usuario['foto_perfil'] ?? null,
            ]);

            // Actualizar último acceso
            try {
                $db->table('ADMIN')
                    ->where('id_admin', $usuario['id_admin'])
                    ->update(['ultimo_acceso' => date('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
                // Silenciar
            }
        } elseif ($rol === 'cliente') {
            $session->set('user', [
                'id' => $usuario['id_cliente'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol' => 'cliente',
                'foto_perfil' => $usuario['foto_perfil'] ?? null,
            ]);
        } else {
            $session->set('user', [
                'id' => $usuario['id_contratista'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol' => 'contratista',
                'foto_perfil' => $usuario['foto_perfil'] ?? null,
            ]);
        }

        $session->regenerate();
        $session->setFlashdata('message', 'Inicio de sesión correcto. ¡Bienvenido!');

        // Respetar redirect_to si fue enviado (ej: desde modal en /cotizador)
        $redirectTo = $this->request->getPost('redirect_to');
        if (!empty($redirectTo) && str_starts_with($redirectTo, '/')) {
            return redirect()->to($redirectTo);
        }

        // Admin va al panel de admin
        if ($rol === 'admin') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/panel');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/')->with('message', 'Sesión cerrada.');
    }
}
