package com.brixo.exception;

import org.springframework.http.HttpStatus;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.servlet.NoHandlerFoundException;

/**
 * Manejo global de excepciones — renderiza vistas de error personalizadas.
 * Equivale a app/Views/errors/ del sistema PHP legacy.
 */
@ControllerAdvice
public class GlobalExceptionHandler {

    @ExceptionHandler(NoHandlerFoundException.class)
    @ResponseStatus(HttpStatus.NOT_FOUND)
    public String handleNotFound(Model model) {
        model.addAttribute("message", "La página solicitada no fue encontrada.");
        return "error/404";
    }

    @ExceptionHandler(org.springframework.security.access.AccessDeniedException.class)
    @ResponseStatus(HttpStatus.FORBIDDEN)
    public String handleAccessDenied(Model model) {
        model.addAttribute("message", "No tienes permisos para acceder a esta página.");
        return "error/403";
    }

    @ExceptionHandler(Exception.class)
    @ResponseStatus(HttpStatus.INTERNAL_SERVER_ERROR)
    public String handleGenericError(Exception ex, Model model) {
        model.addAttribute("message", "Ha ocurrido un error inesperado.");
        model.addAttribute("detail", ex.getMessage());
        return "error/500";
    }
}
