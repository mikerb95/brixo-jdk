package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.AdminUserRequest;
import com.brixo.enums.UserRole;
import com.brixo.service.AdminService;
import com.brixo.service.AnalyticsService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Panel de administración.
 */
@Controller
@RequestMapping("/admin")
public class AdminController {

    private final AdminService adminService;
    private final AnalyticsService analyticsService;

    public AdminController(AdminService adminService, AnalyticsService analyticsService) {
        this.adminService = adminService;
        this.analyticsService = analyticsService;
    }

    /** GET /admin — Dashboard. */
    @GetMapping
    public String dashboard(@AuthenticationPrincipal BrixoUserPrincipal user, Model model) {
        model.addAttribute("user", user);
        model.addAttribute("stats", adminService.getStats());
        return "admin/dashboard";
    }

    /** GET /admin/usuarios — Listado de usuarios. */
    @GetMapping("/usuarios")
    public String usuarios(@AuthenticationPrincipal BrixoUserPrincipal user,
                           @RequestParam(defaultValue = "todos") String tipo,
                           @RequestParam(defaultValue = "") String q,
                           Model model) {
        model.addAttribute("user", user);
        model.addAttribute("usuarios", adminService.getAllUsers());
        model.addAttribute("filtro", tipo);
        model.addAttribute("busqueda", q);
        return "admin/usuarios";
    }

    /** GET /admin/usuarios/crear */
    @GetMapping("/usuarios/crear")
    public String crear(@AuthenticationPrincipal BrixoUserPrincipal user,
                        @RequestParam(defaultValue = "cliente") String tipo,
                        Model model) {
        model.addAttribute("user", user);
        model.addAttribute("tipo", tipo);
        model.addAttribute("editando", false);
        return "admin/usuario_form";
    }

    /** POST /admin/usuarios/guardar */
    @PostMapping("/usuarios/guardar")
    public String guardar(@ModelAttribute AdminUserRequest form, RedirectAttributes flash) {
        try {
            adminService.crearUsuario(form);
            flash.addFlashAttribute("message",
                    "Usuario '" + form.nombre() + "' creado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
            return "redirect:/admin/usuarios/crear?tipo=" + form.tipoUsuario();
        }
        return "redirect:/admin/usuarios";
    }

    /** GET /admin/usuarios/editar/{tipo}/{id} */
    @GetMapping("/usuarios/editar/{tipo}/{id}")
    public String editar(@AuthenticationPrincipal BrixoUserPrincipal user,
                         @PathVariable String tipo,
                         @PathVariable Long id,
                         Model model,
                         RedirectAttributes flash) {
        UserRole rol = parseRole(tipo);
        var usuario = adminService.findUsuario(id, rol);
        if (usuario.isEmpty()) {
            flash.addFlashAttribute("error", "Usuario no encontrado.");
            return "redirect:/admin/usuarios";
        }
        model.addAttribute("user", user);
        model.addAttribute("tipo", tipo);
        model.addAttribute("usuario", usuario.get());
        model.addAttribute("editando", true);
        return "admin/usuario_form";
    }

    /** POST /admin/usuarios/actualizar */
    @PostMapping("/usuarios/actualizar")
    public String actualizar(@ModelAttribute AdminUserRequest form,
                             @RequestParam Long id,
                             RedirectAttributes flash) {
        try {
            UserRole rol = parseRole(form.tipoUsuario());
            adminService.actualizarUsuario(id, rol, form);
            flash.addFlashAttribute("message",
                    "Usuario '" + form.nombre() + "' actualizado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/admin/usuarios";
    }

    /** GET /admin/usuarios/eliminar/{tipo}/{id} */
    @GetMapping("/usuarios/eliminar/{tipo}/{id}")
    public String eliminar(@AuthenticationPrincipal BrixoUserPrincipal user,
                           @PathVariable String tipo,
                           @PathVariable Long id,
                           RedirectAttributes flash) {
        try {
            UserRole rol = parseRole(tipo);
            adminService.eliminarUsuario(id, rol, user.id());
            flash.addFlashAttribute("message", "Usuario eliminado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/admin/usuarios";
    }

    private UserRole parseRole(String tipo) {
        return switch (tipo.toLowerCase()) {
            case "cliente" -> UserRole.CLIENTE;
            case "contratista" -> UserRole.CONTRATISTA;
            case "admin" -> UserRole.ADMIN;
            default -> throw new IllegalArgumentException("Tipo de usuario inválido: " + tipo);
        };
    }
}
