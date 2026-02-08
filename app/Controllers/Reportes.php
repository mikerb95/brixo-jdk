<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContratistaModel;
// Eliminado PhpSpreadsheet para simplificar dependencias
use Shuchkin\SimpleXLSXGen;

class Reportes extends BaseController
{
    public function contratistas()
    {
        $model = new ContratistaModel();
        $data = $model->getWithLocation();

        $rows = [];
        $rows[] = ['ID', 'Nombre', 'Correo', 'Teléfono', 'Ciudad', 'Departamento', 'Experiencia'];
        foreach ($data as $item) {
            $rows[] = [
                $item['id_contratista'],
                $item['nombre'],
                $item['correo'],
                $item['telefono'],
                $item['ciudad'] ?? 'N/A',
                $item['departamento'] ?? 'N/A',
                $item['experiencia'],
            ];
        }

        $xlsx = SimpleXLSXGen::fromArray($rows)->setDefaultFont('Arial');
        $filename = 'reporte_contratistas_' . date('Y-m-d_H-i') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $xlsx->saveAs('php://output');
        exit;
    }

    public function solicitudesXlsx()
    {
        // Export simple usando SimpleXLSXGen para minimizar dependencias y conflictos
        $db = db_connect();
        $rows = $db->table('SOLICITUD')
            ->select('id_solicitud, id_cliente, id_contratista, titulo, descripcion, presupuesto, ubicacion, estado, creado_en')
            ->orderBy('creado_en', 'DESC')
            ->get()->getResultArray();

        $data = [];
        $data[] = ['ID', 'Cliente', 'Contratista', 'Título', 'Descripción', 'Presupuesto', 'Ubicación', 'Estado', 'Creado en'];
        foreach ($rows as $r) {
            $data[] = [
                $r['id_solicitud'],
                $r['id_cliente'],
                $r['id_contratista'],
                $r['titulo'],
                $r['descripcion'],
                $r['presupuesto'],
                $r['ubicacion'],
                $r['estado'],
                $r['creado_en'],
            ];
        }

        $xlsx = SimpleXLSXGen::fromArray($data)->setDefaultFont('Arial');
        $fileName = 'reporte_solicitudes_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $xlsx->saveAs('php://output');
        exit;
    }
}
