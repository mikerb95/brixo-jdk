<?php

namespace App\Controllers;

use App\Models\ContratistaModel;
use App\Models\ResenaModel;

class Mapa extends BaseController
{
    public function index()
    {
        try {
            $contratistaModel = new ContratistaModel();
            $resenaModel = new ResenaModel();

            // Fetch contractors with location info
            $rawProfessionals = $contratistaModel->getWithLocation();

            $professionals = [];

            // Base coordinates for Bogota to simulate location if missing
            $baseLat = 4.6097;
            $baseLng = -74.0817;

            foreach ($rawProfessionals as $pro) {
                // Calculate average rating
                $reviews = $resenaModel->getByContratista($pro['id_contratista']);
                $ratingSum = 0;
                foreach ($reviews as $r) {
                    $ratingSum += $r['calificacion'];
                }
                $avgRating = count($reviews) > 0 ? $ratingSum / count($reviews) : 0;

                // Use real coordinates from ubicacion_mapa if available, otherwise simulate
                $lat = $baseLat;
                $lng = $baseLng;
                if (!empty($pro['ubicacion_mapa'])) {
                    $coords = explode(',', $pro['ubicacion_mapa']);
                    if (count($coords) === 2) {
                        $lat = floatval(trim($coords[0]));
                        $lng = floatval(trim($coords[1]));
                    }
                } else {
                    // Cast to int to ensure sin/cos works correctly
                    $id = (int) $pro['id_contratista'];
                    $latOffset = (sin($id) * 0.05);
                    $lngOffset = (cos($id) * 0.05);
                    $lat = $baseLat + $latOffset;
                    $lng = $baseLng + $lngOffset;
                }

                $fotoPerfil = $pro['foto_perfil'];
                if (!empty($fotoPerfil)) {
                    if (strpos($fotoPerfil, 'http') === 0) {
                        $imagen = $fotoPerfil; // S3 URL
                    } else {
                        $imagen = '/images/profiles/' . $fotoPerfil; // Local
                    }
                } else {
                    $imagen = 'https://ui-avatars.com/api/?name=' . urlencode($pro['nombre']) . '&background=random';
                }

                $professionals[] = [
                    'id' => $pro['id_contratista'],
                    'nombre' => $pro['nombre'],
                    'profesion' => $pro['experiencia'] ?: 'Profesional', // Fallback if empty
                    'rating' => number_format($avgRating, 1),
                    'reviews' => count($reviews),
                    'precio' => 50000, // Placeholder as price is per service, not per contractor
                    'lat' => $lat,
                    'lng' => $lng,
                    'imagen' => $imagen,
                    'ubicacion' => $pro['ciudad'] ?? 'Bogotá'
                ];
            }


            // Pass professionals data to view
            return view('map', ['professionals' => $professionals]);
        } catch (\Throwable $e) {
            // Temporary debugging: Show error directly
            return $e->getMessage() . "<br><pre>" . $e->getTraceAsString() . "</pre>";
        }
    }


}
