package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.enums.UserRole;
import com.brixo.service.PerfilService;
import com.brixo.service.StorageService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
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
    private final StorageService storageService;

    public PerfilController(PerfilService perfilService, StorageService storageService) {
        this.perfilService = perfilService;
        this.storageService = storageService;
    }

    /** GET /perfil/ver/{id} — Ver perfil público de un contratista. */
    @GetMapping("/perfil/ver/{id}")
    public String verPerfil(@PathVariable Long id, Model model) {
        var profile = perfilService.getContratistaProfile(id);
        model.addAttribute("pro", profile);
        return "perfil/ver";
    }

    /** GET /perfil/editar — Editar mi perfil. */
    @GetMapping("/perfil/editar")
    public String editarPerfil(@AuthenticationPrincipal BrixoUserPrincipal user,
                               Model model) {
        var datos = perfilService.getDatosCompletos(user.id(), user.rol());
        model.addAttribute("user", user);
        model.addAttribute("datos", datos);
        return "perfil/editar";
    }

    /** POST /perfil/actualizar — Guardar cambios del perfil. */
    @PostMapping("/perfil/actualizar")
    public String actualizarPerfil(@AuthenticationPrincipal BrixoUserPrincipal user,
                                   @RequestParam String nombre,
                                   @RequestParam(required = false) String telefono,
                                   @RequestParam(required = false) String direccion,
                                   @RequestParam(required = false) String ciudad,
                                   @RequestParam(required = false) String experiencia,
                                   @RequestParam(required = false) String descripcionPerfil,
                                   @RequestParam(required = false) String ubicacionMapa,
                                   @RequestParam(required = false) MultipartFile fotoPerfil,
                                   RedirectAttributes flash) {
        try {
            // Upload photo if provided
            String fotoUrl = null;
            if (fotoPerfil != null && !fotoPerfil.isEmpty()) {
                fotoUrl = storageService.upload(fotoPerfil, "profiles");
            }

            perfilService.actualizar(user.id(), user.rol(), nombre, telefono, direccion,
                    ciudad, experiencia, descripcionPerfil, ubicacionMapa, fotoUrl);

            flash.addFlashAttribute("message", "Perfil actualizado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", "Error al actualizar: " + e.getMessage());
        }
        return "redirect:/perfil/editar";
    }
}
