<?php

namespace App\Controllers;

use App\Models\ContratistaModel;
use App\Models\ResenaModel;
use App\Models\ContratistaServicioModel;
use App\Models\CertificacionModel;

class Perfil extends BaseController
{
    public function ver($id)
    {
        $contratistaModel = new ContratistaModel();

        $pro = $contratistaModel->find($id);
        if (!$pro) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Perfil no encontrado");
        }

        // Get reviews with error handling
        $reviews = [];
        $avgRating = 0;
        try {
            $resenaModel = new ResenaModel();
            $reviews = $resenaModel->getByContratista($id) ?? [];
            
            // Calculate rating
            if (!empty($reviews)) {
                $ratingSum = 0;
                foreach ($reviews as $r) {
                    $ratingSum += (float)($r['calificacion'] ?? 0);
                }
                $avgRating = $ratingSum / count($reviews);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error fetching reviews for contractor ' . $id . ': ' . $e->getMessage());
            $reviews = [];
        }

        // Get services with error handling
        $services = [];
        try {
            $contratistaServicioModel = new ContratistaServicioModel();
            $services = $contratistaServicioModel->getServicesByContratista($id) ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching services for contractor ' . $id . ': ' . $e->getMessage());
            $services = [];
        }

        // Get certifications with error handling
        $certifications = [];
        try {
            $certificacionModel = new CertificacionModel();
            $certifications = $certificacionModel->where('id_contratista', $id)->findAll() ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching certifications for contractor ' . $id . ': ' . $e->getMessage());
            $certifications = [];
        }

        // Prepare data for view with safe defaults
        $fotoPerfil = $pro['foto_perfil'] ?? '';
        if (!empty($fotoPerfil)) {
            if (strpos($fotoPerfil, 'http') === 0) {
                $pro['imagen'] = $fotoPerfil; // S3 URL
            } else {
                $pro['imagen'] = '/images/profiles/' . $fotoPerfil; // Local
            }
        } else {
            $pro['imagen'] = 'https://ui-avatars.com/api/?name=' . urlencode($pro['nombre'] ?? 'Pro') . '&background=random';
        }
        
        $pro['profesion'] = $pro['experiencia'] ?? 'Profesional';
        $pro['rating'] = number_format($avgRating, 1);
        $pro['reviews_count'] = count($reviews);
        $pro['ubicacion'] = $pro['ciudad'] ?? 'Colombia';
        $pro['descripcion'] = $pro['descripcion_perfil'] ?? 'Sin descripción disponible.';
        $pro['verificado'] = $pro['verificado'] ?? false;

        return view('perfil', [
            'pro' => $pro,
            'resenas' => $reviews,
            'servicios' => $services,
            'certificaciones' => $certifications,
        ]);
    }
}