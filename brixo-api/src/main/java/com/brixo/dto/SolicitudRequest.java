package com.brixo.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import java.math.BigDecimal;

/**
 * Datos de entrada para crear/editar una solicitud de servicio.
 */
public record SolicitudRequest(
        @NotBlank(message = "El título es obligatorio")
        @Size(max = 255)
        String titulo,

        String descripcion,

        BigDecimal presupuesto,

        String ubicacion
) {}
