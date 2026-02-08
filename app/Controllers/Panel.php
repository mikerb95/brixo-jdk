<?php
namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use App\Models\ContratistaModel;
use App\Models\ClienteModel;

class Panel extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $session = session();
        $user = $session->get('user');
        $message = $session->getFlashdata('message');
        if (empty($user)) {
            return redirect()->to('/');
        }

        $db = db_connect();
        if ($user['rol'] === 'cliente') {
            // Recuperar datos completos del cliente (incluyendo foto_perfil)
            $clienteModel = new ClienteModel();
            $full = $clienteModel->find($user['id']);
            if (!empty($full)) {
                $user = array_merge($user, $full);
            }

            $contracts = $db->query(
                "SELECT ct.id_contrato, ct.estado, ct.fecha_inicio, ct.fecha_fin, ct.costo_total,
                        'Servicio contratado' as detalle,
                        con.nombre as contratista
                 FROM CONTRATO ct
                 JOIN CONTRATISTA con ON con.id_contratista = ct.id_contratista
                 WHERE ct.id_cliente = ?
                 ORDER BY ct.fecha_inicio DESC",
                [$user['id']]
            )->getResultArray();

            $reviews = $db->query(
                'SELECT r.calificacion, r.comentario, r.fecha as fecha_resena,
                        con.nombre as contratista
                 FROM RESENA r
                 JOIN CONTRATO ct ON ct.id_contrato = r.id_contrato
                 JOIN CONTRATISTA con ON con.id_contratista = ct.id_contratista
                 WHERE r.id_cliente = ?
                 ORDER BY r.fecha DESC',
                [$user['id']]
            )->getResultArray();

            $solicitudes = $db->query(
                "SELECT * FROM SOLICITUD WHERE id_cliente = ? ORDER BY creado_en DESC",
                [$user['id']]
            )->getResultArray();

            return view('panel_cliente', [
                'user' => $user,
                'contracts' => $contracts,
                'reviews' => $reviews,
                'solicitudes' => $solicitudes,
                'message' => $message,
            ]);
        }

        // Contratista
        // Si es contratista, recuperar datos completos (incluyendo foto_perfil)
        if ($user['rol'] === 'contratista') {
            $contratistaModel = new ContratistaModel();
            $full = $contratistaModel->find($user['id']);
            if (!empty($full)) {
                // mezclar datos para que la vista pueda usar $user['foto_perfil'] etc
                $user = array_merge($user, $full);
            }
        }
        $contracts = $db->query(
            "SELECT ct.id_contrato, ct.estado, ct.fecha_inicio, ct.fecha_fin, ct.costo_total,
                    'Servicio contratado' as detalle,
                    cli.nombre as cliente
             FROM CONTRATO ct
             JOIN CLIENTE cli ON cli.id_cliente = ct.id_cliente
             WHERE ct.id_contratista = ?
             ORDER BY ct.fecha_inicio DESC",
            [$user['id']]
        )->getResultArray();

        $reviews = $db->query(
            'SELECT r.calificacion, r.comentario, r.fecha as fecha_resena,
                    cli.nombre as cliente
             FROM RESENA r
             JOIN CONTRATO ct ON ct.id_contrato = r.id_contrato
             JOIN CLIENTE cli ON cli.id_cliente = r.id_cliente
             WHERE ct.id_contratista = ?
             ORDER BY r.fecha DESC',
            [$user['id']]
        )->getResultArray();

        // Obtener solicitudes abiertas recientes para mostrar en el panel
        $solicitudesDisponibles = $db->query("
            SELECT s.*, c.nombre as nombre_cliente 
            FROM SOLICITUD s
            JOIN CLIENTE c ON c.id_cliente = s.id_cliente
            WHERE s.id_contratista IS NULL 
            AND s.estado = 'ABIERTA'
            ORDER BY s.creado_en DESC
            LIMIT 5
        ")->getResultArray();

        return view('panel_contratista', [
            'user' => $user,
            'contracts' => $contracts,
            'reviews' => $reviews,
            'solicitudesDisponibles' => $solicitudesDisponibles,
            'message' => $message,
        ]);
    }

    public function subirImagen()
    {
        helper(['form', 'url']);
        $session = session();
        $user = $session->get('user');

        if (empty($user) || $user['rol'] !== 'contratista') {
            return redirect()->to('/')->with('error', 'Acceso no autorizado.');
        }

        $validationRules = [
            'imagen' => [
                'rules' => 'uploaded[imagen]|is_image[imagen]|max_size[imagen,5120]|mime_in[imagen,image/png,image/jpg,image/jpeg,image/webp]',
                'errors' => [
                    'uploaded' => 'Selecciona una imagen.',
                    'is_image' => 'El archivo debe ser una imagen.',
                    'max_size' => 'La imagen no puede superar 5MB.',
                    'mime_in' => 'Tipos permitidos: png, jpg, jpeg, webp.',
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $img = $this->request->getFile('imagen');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            // Guardar en public/images/profiles/
            $targetDir = FCPATH . 'images/profiles/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $img->move($targetDir, $newName);

            // Redimensionar y generar versiones
            try {
                $imgService = \Config\Services::image();
                $imgService->withFile($targetDir . $newName)->fit(300, 300, 'center')->save($targetDir . 'profile_' . $newName);
                $imgService->withFile($targetDir . $newName)->fit(64, 64, 'center')->save($targetDir . 'thumb_' . $newName);
            } catch (\Exception $e) {
                // Si falla el redimensionado, seguimos con el archivo original renombrado
                copy($targetDir . $newName, $targetDir . 'profile_' . $newName);
                copy($targetDir . $newName, $targetDir . 'thumb_' . $newName);
            }

            @unlink($targetDir . $newName);

            // Actualizar DB (campo foto_perfil en CONTRATISTA)
            $contratistaModel = new ContratistaModel();
            // Borrar imagen antigua si existe
            $old = $contratistaModel->find($user['id']);
            if (!empty($old['foto_perfil'])) {
                @unlink($targetDir . $old['foto_perfil']);
                @unlink($targetDir . 'thumb_' . preg_replace('/^profile_/', '', $old['foto_perfil']));
            }

            $contratistaModel->update($user['id'], ['foto_perfil' => 'profile_' . $newName]);

            // Actualizar sesión para que la navbar refleje el cambio inmediatamente
            $user['foto_perfil'] = 'profile_' . $newName;
            $session->set('user', $user);

            return redirect()->to('/panel')->with('message', 'Imagen de perfil actualizada.');
        }

        return redirect()->back()->with('error', 'Error al subir la imagen.');
    }

    public function editarPerfil()
    {
        $session = session();
        $user = $session->get('user');

        if (empty($user)) {
            return redirect()->to('/');
        }

        $data = [];
        if (isset($user['id'])) {
            $id = $user['id'];
        } else {
            $idKey = 'id_' . $user['rol'];
            $id = $user[$idKey] ?? null;
        }

        if (!$id) {
            return redirect()->to('/')->with('error', 'ID de usuario no encontrado');
        }

        if ($user['rol'] === 'cliente') {
            $model = new ClienteModel();
            $data['user'] = $model->find($id);
            $data['user']['rol'] = 'cliente';
        } else {
            $model = new ContratistaModel();
            $data['user'] = $model->find($id);
            $data['user']['rol'] = 'contratista';
        }

        if (!$data['user']) {
            return redirect()->to('/')->with('error', 'Usuario no encontrado en la base de datos');
        }
        return view('perfil_editar', $data);
    }

    public function actualizarPerfil()
    {
        try {
            $session = session();
            $user = $session->get('user');

            if (empty($user)) {
                return redirect()->to('/');
            }

            if (isset($user['id'])) {
                $id = $user['id'];
            } else {
                $idKey = 'id_' . $user['rol'];
                $id = $user[$idKey] ?? null;
            }

            if (!$id) {
                return redirect()->back()->withInput()->with('error', 'ID de usuario no encontrado');
            }

            $rules = [
                'nombre' => 'required|min_length[3]',
                'telefono' => 'required|min_length[7]',
                'direccion' => 'required',
            ];

            if ($user['rol'] === 'contratista') {
                $rules['experiencia'] = 'required';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'ciudad' => $this->request->getPost('ciudad'),
            ];

            if ($user['rol'] === 'contratista') {
                $data['experiencia'] = $this->request->getPost('experiencia');
                $data['descripcion_perfil'] = $this->request->getPost('descripcion_perfil');

                $ubicacionMapa = $this->request->getPost('ubicacion_mapa');
                if (!empty($ubicacionMapa)) {
                    $data['ubicacion_mapa'] = $ubicacionMapa;
                }
            }

            // Handle Image Upload (to AWS S3)
            $img = $this->request->getFile('foto_perfil');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                try {
                    // Basic size check
                    if ($img->getSize() > 5242880) {
                        return redirect()->back()->withInput()->with('error', 'La imagen es demasiado grande (Máx 5MB).');
                    }

                    $newName = $img->getRandomName();
                    $targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
                    $tempPath = $targetDir . $newName;

                    // Move to temp
                    $img->move($targetDir, $newName);

                    // Upload to S3 inline
                    if (!class_exists('\Aws\S3\S3Client')) {
                        throw new \Exception('AWS SDK not loaded. Run composer install.');
                    }

                    $key = getenv('AWS_ACCESS_KEY_ID');
                    $secret = getenv('AWS_SECRET_ACCESS_KEY');
                    $region = getenv('AWS_REGION') ?: 'us-east-1';
                    $bucket = getenv('AWS_S3_BUCKET') ?: 'brixo-images';

                    if (!$key || !$secret) {
                        throw new \Exception('AWS credentials not set');
                    }

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

                    $s3Url = $result['ObjectURL'];

                    // Clean up temp file
                    @unlink($tempPath);

                    $data['foto_perfil'] = $s3Url;

                    // Delete old image from S3 (if it's an S3 URL)
                    if ($user['rol'] === 'cliente') {
                        $model = new ClienteModel();
                    } else {
                        $model = new ContratistaModel();
                    }
                    $oldUser = $model->find($id);
                    if (!empty($oldUser['foto_perfil']) && strpos($oldUser['foto_perfil'], 's3.amazonaws.com') !== false) {
                        // TODO: Delete from S3 if needed
                    }
                } catch (\Throwable $e) {
                    return redirect()->back()->withInput()->with('error', 'Error procesando imagen: ' . $e->getMessage());
                }
            }

            // Update DB
            if ($user['rol'] === 'cliente') {
                $model = new ClienteModel();
                $model->update($id, $data);
            } else {
                $model = new ContratistaModel();
                $model->update($id, $data);
            }

            // Update Session
            $updatedUser = $model->find($id);
            if ($updatedUser) {
                $updatedUser['rol'] = $user['rol'];
                $updatedUser['id'] = $id; // Ensure id is set
                $session->set('user', $updatedUser);
            }

            return redirect()->to('/perfil/editar')->with('message', 'Perfil actualizado correctamente.');

        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }
}
