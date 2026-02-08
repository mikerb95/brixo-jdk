package com.brixo.dto;

import java.util.List;

/**
 * Datos del contratista para el mapa interactivo.
 */
public record ContratistaMapPin(
        Long id,
        String nombre,
        String ciudad,
        String fotoPerfil,
        double latitud,
        double longitud,
        double calificacionPromedio,
        int totalResenas,
        List<String> servicios
) {}
