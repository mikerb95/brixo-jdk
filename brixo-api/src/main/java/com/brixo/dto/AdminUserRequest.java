package com.brixo.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;

/**
 * Datos para crear/editar usuario desde el panel de admin.
 */
public record AdminUserRequest(
        @NotBlank(message = "El nombre es obligatorio")
        String nombre,

        @NotBlank(message = "El correo es obligatorio")
        @Email
        String correo,

        @Size(min = 8, message = "La contraseña debe tener al menos 8 caracteres")
        String contrasena,

        String telefono,
        String ciudad,

        /** "cliente", "contratista" o "admin" */
        @NotBlank(message = "El tipo de usuario es obligatorio")
        String tipoUsuario
) {}
