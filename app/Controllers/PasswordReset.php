<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class PasswordReset extends BaseController
{
    /**
     * GET /password/forgot - Muestra el formulario de recuperación
     */
    public function forgot(): string
    {
        return view('auth/forgot_password', [
            'message' => session()->getFlashdata('message'),
            'error' => session()->getFlashdata('error'),
        ]);
    }

    /**
     * POST /password/send-reset - Envía el email con el token de reset
     */
    public function sendResetLink(): RedirectResponse
    {
        $email = trim((string) $this->request->getPost('correo'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Por favor ingresa un correo electrónico válido.');
        }

        $db = db_connect();
        
        // Buscar el email en ambas tablas
        $cliente = $db->table('CLIENTE')->where('correo', $email)->get()->getRowArray();
        $contratista = null;
        $rol = null;

        if ($cliente) {
            $rol = 'cliente';
        } else {
            $contratista = $db->table('CONTRATISTA')->where('correo', $email)->get()->getRowArray();
            if ($contratista) {
                $rol = 'contratista';
            }
        }

        if (!$cliente && !$contratista) {
            // Por seguridad, mostramos mensaje genérico
            return redirect()->back()->with('message', 
                'Si el correo existe en nuestra base de datos, recibirás un enlace de recuperación en unos minutos.');
        }

        $usuario = $cliente ?: $contratista;

        // Crear tabla de password_resets si no existe
        $this->ensurePasswordResetsTable($db);

        // Generar token único
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Eliminar tokens antiguos para este email
        $db->table('password_resets')->where('email', $email)->delete();

        // Insertar nuevo token
        $db->table('password_resets')->insert([
            'email' => $email,
            'token' => hash('sha256', $token), // Guardamos hash del token
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiresAt,
        ]);

        // Enviar email
        $resetLink = base_url("/password/reset/{$token}");
        $sent = $this->sendResetEmail($email, $usuario['nombre'], $resetLink);

        if ($sent) {
            return redirect()->back()->with('message', 
                'Te hemos enviado un enlace de recuperación a tu correo. Revisa tu bandeja de entrada.');
        } else {
            return redirect()->back()->with('error', 
                'Hubo un problema al enviar el correo. Por favor intenta de nuevo más tarde.');
        }
    }

    /**
     * GET /password/reset/{token} - Muestra el formulario de cambio de contraseña
     */
    public function reset(string $token): string|RedirectResponse
    {
        $db = db_connect();
        $hashedToken = hash('sha256', $token);

        $reset = $db->table('password_resets')
            ->where('token', $hashedToken)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$reset) {
            return redirect()->to('/password/forgot')->with('error', 
                'El enlace de recuperación es inválido o ha expirado. Solicita uno nuevo.');
        }

        return view('auth/reset_password', [
            'token' => $token,
            'email' => $reset['email'],
            'error' => session()->getFlashdata('error'),
        ]);
    }

    /**
     * POST /password/update - Procesa el cambio de contraseña
     */
    public function processReset(): RedirectResponse
    {
        $token = trim((string) $this->request->getPost('token'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');

        // Validaciones
        if (empty($password) || strlen($password) < 8) {
            return redirect()->back()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Las contraseñas no coinciden.');
        }

        $db = db_connect();
        $hashedToken = hash('sha256', $token);

        // Verificar token
        $reset = $db->table('password_resets')
            ->where('token', $hashedToken)
            ->where('email', $email)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$reset) {
            return redirect()->to('/password/forgot')->with('error', 
                'El enlace de recuperación es inválido o ha expirado.');
        }

        // Actualizar contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Buscar en qué tabla está el usuario
        $cliente = $db->table('CLIENTE')->where('correo', $email)->get()->getRowArray();

        if ($cliente) {
            $db->table('CLIENTE')
                ->where('correo', $email)
                ->update(['contrasena' => $hashedPassword]);
        } else {
            $contratista = $db->table('CONTRATISTA')->where('correo', $email)->get()->getRowArray();
            if ($contratista) {
                $db->table('CONTRATISTA')
                    ->where('correo', $email)
                    ->update(['contrasena' => $hashedPassword]);
            } else {
                return redirect()->to('/password/forgot')->with('error', 
                    'No se encontró la cuenta asociada a este correo.');
            }
        }

        // Eliminar token usado
        $db->table('password_resets')->where('email', $email)->delete();

        // Enviar a login con mensaje de éxito
        return redirect()->to('/login')->with('message', 
            '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Crea la tabla password_resets si no existe
     */
    private function ensurePasswordResetsTable($db): void
    {
        $db->query("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                INDEX idx_email (email),
                INDEX idx_token (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Envía el email de recuperación de contraseña
     */
    private function sendResetEmail(string $email, string $nombre, string $resetLink): bool
    {
        $emailService = \Config\Services::email();

        $emailService->setFrom('no-reply@brixo.com', 'Brixo');
        $emailService->setTo($email);
        $emailService->setSubject('Recuperación de contraseña - Brixo');

        $message = view('emails/password_reset', [
            'nombre' => $nombre,
            'resetLink' => $resetLink,
        ]);

        $emailService->setMessage($message);

        // En desarrollo, solo simular el envío
        if (ENVIRONMENT === 'development') {
            log_message('info', "Password reset email would be sent to: {$email}");
            log_message('info', "Reset link: {$resetLink}");
            // En desarrollo, retornar true para testing
            return true;
        }

        return $emailService->send();
    }
}
