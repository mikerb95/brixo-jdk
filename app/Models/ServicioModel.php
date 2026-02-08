<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table            = 'SERVICIO';
    protected $primaryKey       = 'id_servicio';
    protected $allowedFields    = ['nombre', 'descripcion', 'imagen_url', 'precio_estimado', 'id_categoria'];
    protected $returnType       = 'array';

    public function getWithCategory()
    {
        return $this->select('SERVICIO.*, CATEGORIA.nombre as categoria_nombre')
            ->join('CATEGORIA', 'CATEGORIA.id_categoria = SERVICIO.id_categoria', 'left')
            ->findAll();
    }
}
