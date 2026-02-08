<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\ServicioModel;

class Especialidades extends BaseController
{
    public function index(): string
    {
        $categoriaModel = new CategoriaModel();
        $servicioModel = new ServicioModel();
        
        // Obtener todas las categorías
        $categorias = $categoriaModel->findAll();
        
        // Obtener servicios por categoría (primeros 4 de cada una)
        $especialidades = [];
        foreach ($categorias as $categoria) {
            $servicios = $servicioModel
                ->select('SERVICIO.*, CATEGORIA.nombre as categoria_nombre')
                ->join('CATEGORIA', 'CATEGORIA.id_categoria = SERVICIO.id_categoria')
                ->where('SERVICIO.id_categoria', $categoria['id_categoria'])
                ->limit(4)
                ->findAll();
            
            if (!empty($servicios)) {
                $especialidades[] = [
                    'categoria' => $categoria,
                    'servicios' => $servicios
                ];
            }
        }
        
        $data = [
            'user' => session()->get('user'),
            'especialidades' => $especialidades,
            'categorias' => $categorias
        ];
        
        return view('especialidades', $data);
    }
    
    public function categoria($id_categoria = null): string
    {
        if (!$id_categoria) {
            return redirect()->to('/especialidades');
        }
        
        $categoriaModel = new CategoriaModel();
        $servicioModel = new ServicioModel();
        
        $categoria = $categoriaModel->find($id_categoria);
        
        if (!$categoria) {
            return redirect()->to('/especialidades')->with('error', 'Categoría no encontrada');
        }
        
        // Obtener todos los servicios de esta categoría
        $servicios = $servicioModel
            ->select('SERVICIO.*, CATEGORIA.nombre as categoria_nombre')
            ->join('CATEGORIA', 'CATEGORIA.id_categoria = SERVICIO.id_categoria')
            ->where('SERVICIO.id_categoria', $id_categoria)
            ->findAll();
        
        $data = [
            'user' => session()->get('user'),
            'categoria' => $categoria,
            'servicios' => $servicios
        ];
        
        return view('categoria_detalle', $data);
    }
}
