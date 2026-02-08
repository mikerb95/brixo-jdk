<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratistaServicioModel extends Model
{
    protected $table = 'CONTRATISTA_SERVICIO';
    protected $primaryKey = 'id_contratista'; // Composite key handling is tricky in CI4, usually we just use query builder
    protected $allowedFields = ['id_contratista', 'id_servicio', 'precio_personalizado', 'descripcion_personalizada'];
    protected $returnType = 'array';

    public function getServicesByContratista($id_contratista)
    {
        return $this->select('SERVICIO.*')
            ->join('SERVICIO', 'SERVICIO.id_servicio = CONTRATISTA_SERVICIO.id_servicio')
            ->where('CONTRATISTA_SERVICIO.id_contratista', $id_contratista)
            ->findAll();
    }

    public function getAllOffers()
    {
        return $this->select('SERVICIO.id_servicio, SERVICIO.nombre as titulo, COALESCE(CONTRATISTA_SERVICIO.descripcion_personalizada, SERVICIO.descripcion) as descripcion, COALESCE(CONTRATISTA_SERVICIO.precio_personalizado, SERVICIO.precio_estimado) as precio, SERVICIO.imagen_url, CATEGORIA.nombre as categoria, CONTRATISTA.id_contratista, CONTRATISTA.nombre as profesional_nombre, CONTRATISTA.foto_perfil, COALESCE(UBICACION.ciudad, CONTRATISTA.ciudad) as ubicacion')
            ->join('SERVICIO', 'SERVICIO.id_servicio = CONTRATISTA_SERVICIO.id_servicio')
            ->join('CONTRATISTA', 'CONTRATISTA.id_contratista = CONTRATISTA_SERVICIO.id_contratista')
            ->join('CATEGORIA', 'CATEGORIA.id_categoria = SERVICIO.id_categoria', 'left')
            ->join('CONTRATISTA_UBICACION', 'CONTRATISTA_UBICACION.id_contratista = CONTRATISTA.id_contratista', 'left')
            ->join('UBICACION', 'UBICACION.id_ubicacion = CONTRATISTA_UBICACION.id_ubicacion', 'left')
            ->findAll();
    }
}
