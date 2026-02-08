package com.brixo.service;

import com.brixo.dto.ContratistaMapPin;
import com.brixo.entity.Contratista;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.ContratistaServicioRepository;
import com.brixo.repository.ResenaRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

/**
 * Servicio de datos para el mapa interactivo de contratistas.
 *
 * Replica la lógica de Mapa::index del PHP legacy:
 * - Obtiene contratistas con ubicación de mapa
 * - Parsea coordenadas desde el campo ubicacion_mapa (formato "lat,lng")
 * - Calcula calificaciones promedio
 * - Lista servicios ofrecidos
 */
@Service
public class MapaService {

    private static final Logger log = LoggerFactory.getLogger(MapaService.class);

    private final ContratistaRepository contratistaRepo;
    private final ResenaRepository resenaRepo;
    private final ContratistaServicioRepository csRepo;

    public MapaService(ContratistaRepository contratistaRepo,
                       ResenaRepository resenaRepo,
                       ContratistaServicioRepository csRepo) {
        this.contratistaRepo = contratistaRepo;
        this.resenaRepo = resenaRepo;
        this.csRepo = csRepo;
    }

    /**
     * Obtiene todos los pins de contratistas para el mapa.
     */
    public List<ContratistaMapPin> getAllPins() {
        List<Contratista> contratistas = contratistaRepo.findAllWithLocation();
        List<ContratistaMapPin> pins = new ArrayList<>();

        for (Contratista c : contratistas) {
            double[] coords = parseCoordinates(c.getUbicacionMapa());
            if (coords == null) continue;

            Double avgRating = resenaRepo.getAverageRatingByContratistaId(c.getId());
            var servicios = csRepo.findByContratistaIdWithDetails(c.getId());
            List<String> nombresServicios = servicios.stream()
                    .map(cs -> cs.getServicio().getNombre())
                    .toList();

            long totalResenas = resenaRepo.findByContratistaId(c.getId()).size();

            pins.add(new ContratistaMapPin(
                    c.getId(),
                    c.getNombre(),
                    c.getCiudad(),
                    c.getFotoPerfil(),
                    coords[0], coords[1],
                    avgRating != null ? avgRating : 0.0,
                    (int) totalResenas,
                    nombresServicios
            ));
        }

        return pins;
    }

    /**
     * Parsea coordenadas del formato "lat,lng" usado en el campo ubicacion_mapa.
     */
    private double[] parseCoordinates(String ubicacionMapa) {
        if (ubicacionMapa == null || ubicacionMapa.isBlank()) return null;
        try {
            String[] parts = ubicacionMapa.split(",");
            if (parts.length != 2) return null;
            return new double[]{
                    Double.parseDouble(parts[0].trim()),
                    Double.parseDouble(parts[1].trim())
            };
        } catch (NumberFormatException e) {
            log.warn("Coordenadas inválidas: {}", ubicacionMapa);
            return null;
        }
    }
}
