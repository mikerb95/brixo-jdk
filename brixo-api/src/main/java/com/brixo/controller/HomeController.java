package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.enums.UserRole;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

/**
 * Controlador de la página de inicio.
 * Ruta: /
 */
@Controller
public class HomeController {

    @GetMapping("/")
    public String index(@AuthenticationPrincipal BrixoUserPrincipal user, Model model) {
        if (user != null) {
            model.addAttribute("user", user);
        }
        return "index";
    }
}
