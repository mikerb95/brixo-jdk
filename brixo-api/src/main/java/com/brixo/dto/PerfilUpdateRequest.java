package com.brixo.dto;

import org.springframework.web.multipart.MultipartFile;

/**
 * Datos para actualizar el perfil de un usuario.
 */
public record PerfilUpdateRequest(
        String nombre,
        String telefono,
        String ciudad,

        // Campos exclusivos de contratista
        String ubicacionMapa,
        String experiencia,
        String descripcionPerfil,
        String portafolio,

        MultipartFile fotoPerfil
) {}
