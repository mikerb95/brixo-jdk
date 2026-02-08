<?php

namespace App\Models;

use CodeIgniter\Model;

class MensajeModel extends Model
{
    protected $table = 'MENSAJE';
    protected $primaryKey = 'id_mensaje';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'remitente_id',
        'remitente_rol',
        'destinatario_id',
        'destinatario_rol',
        'contenido',
        'leido',
        'creado_en'
    ];

    protected $useTimestamps = false; // Lo manejamos manual o con default current_timestamp en DB
    protected $createdField = 'creado_en';
    protected $updatedField = '';
    protected $deletedField = '';

    // Obtener conversaciones (último mensaje por usuario)
    public function getConversaciones($userId, $userRol)
    {
        // Esta consulta es un poco compleja para obtener la lista de chats únicos
        // Seleccionamos los IDs de los usuarios con los que hemos hablado
        $sql = "
            SELECT 
                CASE 
                    WHEN remitente_id = ? AND remitente_rol = ? THEN destinatario_id
                    ELSE remitente_id 
                END as otro_usuario_id,
                CASE 
                    WHEN remitente_id = ? AND remitente_rol = ? THEN destinatario_rol
                    ELSE remitente_rol 
                END as otro_usuario_rol,
                MAX(creado_en) as ultimo_mensaje_fecha
            FROM MENSAJE
            WHERE (remitente_id = ? AND remitente_rol = ?) 
               OR (destinatario_id = ? AND destinatario_rol = ?)
            GROUP BY otro_usuario_id, otro_usuario_rol
            ORDER BY ultimo_mensaje_fecha DESC
        ";

        $query = $this->db->query($sql, [$userId, $userRol, $userId, $userRol, $userId, $userRol, $userId, $userRol]);
        return $query->getResultArray();
    }

    public function getMensajesChat($userId, $userRol, $otroId, $otroRol)
    {
        return $this->where(function ($builder) use ($userId, $userRol, $otroId, $otroRol) {
            $builder->groupStart()
                ->where('remitente_id', $userId)
                ->where('remitente_rol', $userRol)
                ->where('destinatario_id', $otroId)
                ->where('destinatario_rol', $otroRol)
                ->groupEnd();
        })
            ->orWhere(function ($builder) use ($userId, $userRol, $otroId, $otroRol) {
                $builder->groupStart()
                    ->where('remitente_id', $otroId)
                    ->where('remitente_rol', $otroRol)
                    ->where('destinatario_id', $userId)
                    ->where('destinatario_rol', $userRol)
                    ->groupEnd();
            })
            ->orderBy('creado_en', 'ASC')
            ->findAll();
    }

    public function marcarComoLeidos($userId, $userRol, $otroId, $otroRol)
    {
        return $this->where('remitente_id', $otroId)
            ->where('remitente_rol', $otroRol)
            ->where('destinatario_id', $userId)
            ->where('destinatario_rol', $userRol)
            ->where('leido', 0)
            ->set(['leido' => 1])
            ->update();
    }
}
