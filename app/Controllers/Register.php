<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Register extends BaseController
{
    public function register(): RedirectResponse
    {
        $session = session();

        $nombre = trim((string) $this->request->getPost('nombre'));
        $correo = trim((string) $this->request->getPost('correo'));
        $telefono = trim((string) $this->request->getPost('telefono'));
        $contrasena = (string) $this->request->getPost('contrasena');
        $confirmacion = (string) $this->request->getPost('contrasena_confirm');
        $rol = trim((string) $this->request->getPost('rol'));
        $ciudad = trim((string) $this->request->getPost('ciudad'));
        $ubicacionMapa = trim((string) $this->request->getPost('ubicacion_mapa'));

        $old = [
            'nombre' => $nombre,
            'correo' => $correo,
            'telefono' => $telefono,
            'rol' => $rol,
            'ciudad' => $ciudad,
            'ubicacion_mapa' => $ubicacionMapa,
        ];

        if ($nombre === '' || $correo === '' || $contrasena === '' || $confirmacion === '' || !in_array($rol, ['cliente', 'contratista'], true)) {
            $session->setFlashdata('register_error', 'Completa todos los campos obligatorios y selecciona un tipo de cuenta válido.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $session->setFlashdata('register_error', 'Ingresa un correo electrónico válido.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        // Validación de contraseñas: coincidencia y fortaleza
        if ($contrasena !== $confirmacion) {
            $session->setFlashdata('register_error', 'Las contraseñas no coinciden.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        // Reglas de fortaleza: mínimo 8 caracteres, al menos una mayúscula, una minúscula, un dígito y un símbolo
        $lenOk = strlen($contrasena) >= 8;
        $upperOk = preg_match('/[A-Z]/', $contrasena) === 1;
        $lowerOk = preg_match('/[a-z]/', $contrasena) === 1;
        $digitOk = preg_match('/\d/', $contrasena) === 1;
        $symbolOk = preg_match('/[^A-Za-z0-9]/', $contrasena) === 1;
        if (!($lenOk && $upperOk && $lowerOk && $digitOk && $symbolOk)) {
            $session->setFlashdata('register_error', 'La contraseña debe tener mínimo 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        // Validación de ciudad para todos
        if ($ciudad === '') {
            $session->setFlashdata('register_error', 'Por favor selecciona tu ciudad.');
            $session->setFlashdata('register_old', $old);
            return redirect()->to('/');
        }

        if ($rol === 'contratista' && $ubicacionMapa === '') {
            $session->setFlashdata('register_error', 'Los contratistas deben indicar la ubicación exacta en el mapa.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        $db = db_connect();
        $existsCliente = $db->table('CLIENTE')->where('correo', $correo)->countAllResults();
        $existsContratista = $db->table('CONTRATISTA')->where('correo', $correo)->countAllResults();

        if ($existsCliente > 0 || $existsContratista > 0) {
            $session->setFlashdata('register_error', 'Ese correo ya está registrado.');
            $session->setFlashdata('register_old', $old);

            return redirect()->to('/');
        }

        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // Handle Image Upload
        $fotoPerfil = null;
        $img = $this->request->getFile('foto_perfil');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            try {
                // Basic size check (5MB)
                if ($img->getSize() <= 5242880) {
                    $newName = $img->getRandomName();

                    // Check if AWS S3 is configured
                    $key = getenv('AWS_ACCESS_KEY_ID');
                    $secret = getenv('AWS_SECRET_ACCESS_KEY');

                    if ($key && $secret && class_exists('\Aws\S3\S3Client')) {
                        // Upload to S3
                        $targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
                        $tempPath = $targetDir . $newName;
                        $img->move($targetDir, $newName);

                        $region = getenv('AWS_REGION') ?: 'us-east-1';
                        $bucket = getenv('AWS_S3_BUCKET') ?: 'brixo-images';

                        $s3Client = new \Aws\S3\S3Client([
                            'region' => $region,
                            'version' => 'latest',
                            'credentials' => [
                                'key' => $key,
                                'secret' => $secret,
                            ],
                        ]);

                        $result = $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key' => 'profiles/' . $newName,
                            'SourceFile' => $tempPath,
                        ]);

                        $fotoPerfil = $result['ObjectURL'];
                        @unlink($tempPath);
                    } else {
                        // Local upload
                        $img->move(FCPATH . 'images/profiles', $newName);
                        $fotoPerfil = $newName;
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Error uploading profile photo: ' . $e->getMessage());
            }
        }

        if ($rol === 'cliente') {
            $data = [
                'nombre' => $nombre,
                'correo' => $correo,
                'telefono' => $telefono,
                'contrasena' => $hash,
                'ciudad' => $ciudad,
            ];
            if ($fotoPerfil) {
                $data['foto_perfil'] = $fotoPerfil;
            }
            $db->table('CLIENTE')->insert($data);
        } else {
            $data = [
                'nombre' => $nombre,
                'correo' => $correo,
                'telefono' => $telefono,
                'contrasena' => $hash,
                'ciudad' => $ciudad,
                'ubicacion_mapa' => $ubicacionMapa,
            ];
            if ($fotoPerfil) {
                $data['foto_perfil'] = $fotoPerfil;
            }
            $db->table('CONTRATISTA')->insert($data);
        }

        $session->setFlashdata('message', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');

        return redirect()->to('/');
    }
}
