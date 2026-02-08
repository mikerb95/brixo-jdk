<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'CLIENTE';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = ['nombre', 'correo', 'contrasena', 'telefono', 'ciudad', 'direccion', 'foto_perfil'];
    protected $returnType = 'array';
}
