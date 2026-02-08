package com.brixo.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

/**
 * Datos de entrada para enviar un mensaje.
 */
public record MensajeRequest(
        @NotNull(message = "El destinatario es obligatorio")
        Long destinatarioId,

        @NotBlank(message = "El rol del destinatario es obligatorio")
        String destinatarioRol,

        @NotBlank(message = "El contenido es obligatorio")
        String contenido
) {}
