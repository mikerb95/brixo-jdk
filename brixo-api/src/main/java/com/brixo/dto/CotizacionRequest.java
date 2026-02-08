package com.brixo.dto;

import jakarta.validation.constraints.NotBlank;

/**
 * Descripción libre del trabajo para generar cotización con IA.
 */
public record CotizacionRequest(
        @NotBlank(message = "La descripción del trabajo es obligatoria")
        String descripcion
) {}
