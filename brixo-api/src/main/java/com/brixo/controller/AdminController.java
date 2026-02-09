package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.service.AdminService;
import com.brixo.service.AnalyticsService;
import com.brixo.dto.AdminUserRequest;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Panel de administración.
 *
 * Rutas:
 *   GET  /admin                                — Dashboard
 *   GET  /admin/usuarios                       — Listado de usuarios
 *   GET  /admin/usuarios/crear                 — Formulario creación
 *   POST /admin/usuarios/guardar               — Guardar nuevo usuario
 *   GET  /admin/usuarios/editar/{tipo}/{id}     — Formulario edición
 *   POST /admin/usuarios/actualizar             — Actualizar usuario
 *   GET  /admin/usuarios/eliminar/{tipo}/{id}   — Eliminar usuario
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

    /** GET /admin — Dashboard principal. */
    @GetMapping
    public String dashboard(@AuthenticationPrincipal BrixoUserPrincipal user,
                            Model model) {
        model.addAttribute("user", user);
        model.addAttribute("stats", adminService.getDashboardStats());
        return "admin/dashboard";
    }

    /** GET /admin/usuarios — Listado de usuarios con filtros. */
    @GetMapping("/usuarios")
    public String usuarios(@AuthenticationPrincipal BrixoUserPrincipal user,
                           @RequestParam(defaultValue = "todos") String tipo,
                           @RequestParam(defaultValue = "") String q,
                           Model model) {
        var users = adminService.listarUsuarios(tipo, q);
        model.addAttribute("user", user);
        model.addAttribute("usuarios", users);
        model.addAttribute("filtro", tipo);
        model.addAttribute("busqueda", q);
        return "admin/usuarios";
    }

    /** GET /admin/usuarios/crear — Formulario de creación. */
    @GetMapping("/usuarios/crear")
    public String crear(@AuthenticationPrincipal BrixoUserPrincipal user,
                        @RequestParam(defaultValue = "cliente") String tipo,
                        Model model) {
        model.addAttribute("user", user);
        model.addAttribute("tipo", tipo);
        model.addAttribute("editando", false);
        return "admin/usuario_form";
    }

    /** POST /admin/usuarios/guardar — Crear usuario. */
    @PostMapping("/usuarios/guardar")
    public String guardar(@ModelAttribute AdminUserRequest form,
                          RedirectAttributes flash) {
        try {
            adminService.crearUsuario(form);
            flash.addFlashAttribute("message",
                    "Usuario '" + form.nombre() + "' creado correctamente como " + form.tipo() + ".");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
            return "redirect:/admin/usuarios/crear?tipo=" + form.tipo();
        }
        return "redirect:/admin/usuarios";
    }

    /** GET /admin/usuarios/editar/{tipo}/{id} — Formulario edición. */
    @GetMapping("/usuarios/editar/{tipo}/{id}")
    public String editar(@AuthenticationPrincipal BrixoUserPrincipal user,
                         @PathVariable String tipo,
                         @PathVariable Long id,
                         Model model,
                         RedirectAttributes flash) {
        var usuario = adminService.buscarUsuario(tipo, id);
        if (usuario == null) {
            flash.addFlashAttribute("error", "Usuario no encontrado.");
            return "redirect:/admin/usuarios";
        }
        model.addAttribute("user", user);
        model.addAttribute("tipo", tipo);
        model.addAttribute("usuario", usuario);
        model.addAttribute("editando", true);
        return "admin/usuario_form";
    }

    /** POST /admin/usuarios/actualizar — Actualizar usuario. */
    @PostMapping("/usuarios/actualizar")
    public String actualizar(@ModelAttribute AdminUserRequest form,
                             RedirectAttributes flash) {
        try {
            adminService.actualizarUsuario(form);
            flash.addFlashAttribute("message",
                    "Usuario '" + form.nombre() + "' actualizado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/admin/usuarios";
    }

    /** GET /admin/usuarios/eliminar/{tipo}/{id} — Eliminar usuario. */
    @GetMapping("/usuarios/eliminar/{tipo}/{id}")
    public String eliminar(@AuthenticationPrincipal BrixoUserPrincipal user,
                           @PathVariable String tipo,
                           @PathVariable Long id,
                           RedirectAttributes flash) {
        try {
            adminService.eliminarUsuario(tipo, id, user.id());
            flash.addFlashAttribute("message", "Usuario eliminado correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/admin/usuarios";
    }
}
