<?php

namespace App\Models;

use CodeIgniter\Model;

class ResenaModel extends Model
{
    protected $table            = 'RESENA';
    protected $primaryKey       = 'id_resena';
    protected $allowedFields    = ['comentario', 'fecha', 'calificacion', 'id_contrato', 'id_cliente'];
    protected $returnType       = 'array';

    public function getByContratista($id_contratista)
    {
        return $this->select('RESENA.*, CLIENTE.nombre as autor')
            ->join('CONTRATO', 'CONTRATO.id_contrato = RESENA.id_contrato')
            ->join('CLIENTE', 'CLIENTE.id_cliente = RESENA.id_cliente')
            ->where('CONTRATO.id_contratista', $id_contratista)
            ->findAll();
    }
}
