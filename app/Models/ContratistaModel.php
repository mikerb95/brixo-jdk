<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratistaModel extends Model
{
    protected $table = 'CONTRATISTA';
    protected $primaryKey = 'id_contratista';
    protected $allowedFields = ['nombre', 'experiencia', 'portafolio', 'foto_perfil', 'descripcion_perfil', 'verificado', 'telefono', 'correo', 'contrasena', 'ciudad', 'direccion', 'ubicacion_mapa'];
    protected $returnType = 'array';

    public function getWithLocation()
    {
        // Alias UBICACION columns to avoid overwriting CONTRATISTA columns (like ciudad, direccion)
        return $this->select('CONTRATISTA.*, UBICACION.ciudad as u_ciudad, UBICACION.departamento, UBICACION.direccion as u_direccion, UBICACION.latitud, UBICACION.longitud')
            ->join('CONTRATISTA_UBICACION', 'CONTRATISTA_UBICACION.id_contratista = CONTRATISTA.id_contratista', 'left')
            ->join('UBICACION', 'UBICACION.id_ubicacion = CONTRATISTA_UBICACION.id_ubicacion', 'left')
            ->findAll();
    }
}
