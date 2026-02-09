package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.RegisterRequest;
import com.brixo.service.RegistroService;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Controlador de autenticación.
 *
 * Spring Security maneja el POST /login y POST /logout automáticamente;
 * este controlador solo provee las páginas y el registro de nuevos usuarios.
 */
@Controller
public class AuthController {

    private final RegistroService registroService;

    public AuthController(RegistroService registroService) {
        this.registroService = registroService;
    }

    /**
     * GET /login — Página de inicio de sesión.
     */
    @GetMapping("/login")
    public String showLogin(@AuthenticationPrincipal BrixoUserPrincipal user,
                            HttpServletRequest request,
                            Model model) {
        // Si ya está autenticado, redirigir
        if (user != null) {
            return "redirect:/panel";
        }

        if (request.getParameter("error") != null) {
            model.addAttribute("loginError", "Correo o contraseña incorrectos.");
        }
        return "auth/login";
    }

    /**
     * POST /register — Registro de nuevo usuario (cliente o contratista).
     */
    @PostMapping("/register")
    public String register(@ModelAttribute RegisterRequest form,
                           RedirectAttributes flash) {
        try {
            registroService.registrar(form);
            flash.addFlashAttribute("message", "Cuenta creada correctamente. Ya puedes iniciar sesión.");
            return "redirect:/login";
        } catch (IllegalArgumentException e) {
            flash.addFlashAttribute("registerError", e.getMessage());
            flash.addFlashAttribute("registerOld", form);
            return "redirect:/login";
        }
    }
}
