package com.brixo.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import org.springframework.web.multipart.MultipartFile;

/**
 * Datos de entrada para registro de usuario (cliente o contratista).
 */
public record RegisterRequest(
        @NotBlank(message = "El nombre es obligatorio")
        String nombre,

        @NotBlank(message = "El correo es obligatorio")
        @Email(message = "Formato de correo inválido")
        String correo,

        @NotBlank(message = "La contraseña es obligatoria")
        @Size(min = 8, message = "La contraseña debe tener al menos 8 caracteres")
        String contrasena,

        @NotBlank(message = "La confirmación de contraseña es obligatoria")
        String contrasenaConfirm,

        /** "cliente" o "contratista" */
        @NotBlank(message = "El rol es obligatorio")
        String rol,

        String telefono,
        String ciudad,

        // Campos exclusivos de contratista
        String ubicacionMapa,
        String experiencia,
        String descripcionPerfil,

        MultipartFile fotoPerfil
) {}
