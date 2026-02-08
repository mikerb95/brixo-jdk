<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificacionModel extends Model
{
    protected $table            = 'CERTIFICACION';
    protected $primaryKey       = 'id_certificado';
    protected $allowedFields    = ['nombre', 'entidad_emisora', 'fecha_obtenida', 'id_contratista'];
    protected $returnType       = 'array';
}
