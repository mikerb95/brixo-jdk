<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table            = 'CATEGORIA';
    protected $primaryKey       = 'id_categoria';
    protected $allowedFields    = ['nombre', 'descripcion'];
    protected $returnType       = 'array';
}
