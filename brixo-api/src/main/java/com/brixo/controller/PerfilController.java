package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.PerfilUpdateRequest;
import com.brixo.enums.UserRole;
import com.brixo.service.PerfilService;
import com.brixo.service.StorageService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Controlador de perfil de usuario.
 * - /perfil/ver/{id}   — Ver perfil de contratista (público)
 * - /perfil/editar      — Formulario de edición (autenticado)
 * - /perfil/actualizar  — Guardar cambios
 */
@Controller
public class PerfilController {

    private final PerfilService perfilService;

    public PerfilController(PerfilService perfilService) {
        this.perfilService = perfilService;
    }

    /** GET /perfil/ver/{id} — Ver perfil público de un contratista. */
    @GetMapping("/perfil/ver/{id}")
    public String verPerfil(@PathVariable Long id, Model model) {
        var profile = perfilService.getPublicProfile(id);
        if (profile.isEmpty()) {
            return "error/404";
        }
        model.addAttribute("pro", profile.get());
        return "perfil/ver";
    }

    /** GET /perfil/editar — Editar mi perfil. */
    @GetMapping("/perfil/editar")
    public String editarPerfil(@AuthenticationPrincipal BrixoUserPrincipal user,
                               Model model) {
        model.addAttribute("user", user);

        if (user.rol() == UserRole.CLIENTE) {
            var cliente = perfilService.findCliente(user.id());
            model.addAttribute("datos", cliente.orElse(null));
        } else {
            var contratista = perfilService.findContratista(user.id());
            model.addAttribute("datos", contratista.orElse(null));
        }
        return "perfil/editar";
    }

    /** POST /perfil/actualizar — Guardar cambios del perfil. */
    @PostMapping("/perfil/actualizar")
    public String actualizarPerfil(@AuthenticationPrincipal BrixoUserPrincipal user,
                                   @ModelAttribute PerfilUpdateRequest req,
                                   RedirectAttributes flash) {
        try {
            perfilService.updateProfile(user.id(), user.rol(), req);
            flash.addFlashAttribute("message", "Perfil actualizado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", "Error al actualizar: " + e.getMessage());
        }
        return "redirect:/perfil/editar";
    }
}
