package com.brixo.dto;

import com.brixo.enums.UserRole;

/**
 * Datos de sesión del usuario autenticado, disponible para las vistas Thymeleaf.
 * Equivale a session('user') del sistema PHP legacy: {id, nombre, correo, rol, foto_perfil}.
 */
public record UserSession(
        Long id,
        String nombre,
        String correo,
        UserRole rol,
        String fotoPerfil
) {}
