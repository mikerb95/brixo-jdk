package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.SolicitudRequest;
import com.brixo.enums.UserRole;
import com.brixo.service.SolicitudService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.math.BigDecimal;

/**
 * Controlador de solicitudes de servicio.
 */
@Controller
public class SolicitudController {

    private final SolicitudService solicitudService;

    public SolicitudController(SolicitudService solicitudService) {
        this.solicitudService = solicitudService;
    }

    /** GET /solicitud/nueva */
    @GetMapping("/solicitud/nueva")
    public String nueva(@AuthenticationPrincipal BrixoUserPrincipal user,
                        @RequestParam(required = false) Long contratista,
                        Model model,
                        jakarta.servlet.http.HttpSession session) {
        model.addAttribute("user", user);
        model.addAttribute("idContratista", contratista);

        @SuppressWarnings("unchecked")
        var prefill = (java.util.Map<String, String>) session.getAttribute("prefill_solicitud");
        if (prefill != null) {
            model.addAttribute("prefill", prefill);
            session.removeAttribute("prefill_solicitud");
        }
        return "solicitud/nueva";
    }

    /** POST /solicitud/guardar */
    @PostMapping("/solicitud/guardar")
    public String guardar(@AuthenticationPrincipal BrixoUserPrincipal user,
                          @RequestParam String titulo,
                          @RequestParam String descripcion,
                          @RequestParam(defaultValue = "0") String presupuesto,
                          @RequestParam(required = false) String ubicacion,
                          @RequestParam(required = false) String departamento,
                          @RequestParam(required = false) String ciudad,
                          RedirectAttributes flash) {
        try {
            String ubicacionFinal = ubicacion != null ? ubicacion : "";
            if (ciudad != null && !ciudad.isBlank() && departamento != null && !departamento.isBlank()) {
                ubicacionFinal = ciudad + ", " + departamento +
                        (!ubicacionFinal.isBlank() ? " - " + ubicacionFinal : "");
            }

            var req = new SolicitudRequest(titulo, descripcion,
                    new BigDecimal(presupuesto), ubicacionFinal);
            solicitudService.crear(user.id(), req);
            flash.addFlashAttribute("message", "Solicitud publicada correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/panel";
    }

    /** GET /solicitud/editar/{id} */
    @GetMapping("/solicitud/editar/{id}")
    public String editar(@AuthenticationPrincipal BrixoUserPrincipal user,
                         @PathVariable Long id,
                         Model model,
                         RedirectAttributes flash) {
        var solicitud = solicitudService.findById(id);
        if (solicitud.isEmpty()) {
            flash.addFlashAttribute("error", "Solicitud no encontrada.");
            return "redirect:/panel";
        }
        model.addAttribute("solicitud", solicitud.get());
        return "solicitud/editar";
    }

    /** POST /solicitud/actualizar/{id} */
    @PostMapping("/solicitud/actualizar/{id}")
    public String actualizar(@AuthenticationPrincipal BrixoUserPrincipal user,
                             @PathVariable Long id,
                             @RequestParam String titulo,
                             @RequestParam String descripcion,
                             @RequestParam(defaultValue = "0") String presupuesto,
                             @RequestParam(required = false) String ubicacion,
                             RedirectAttributes flash) {
        try {
            var req = new SolicitudRequest(titulo, descripcion,
                    new BigDecimal(presupuesto), ubicacion);
            solicitudService.actualizar(id, user.id(), req);
            flash.addFlashAttribute("message", "Solicitud actualizada correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/panel";
    }

    /** GET /solicitud/eliminar/{id} */
    @GetMapping("/solicitud/eliminar/{id}")
    public String eliminar(@AuthenticationPrincipal BrixoUserPrincipal user,
                           @PathVariable Long id,
                           RedirectAttributes flash) {
        boolean ok = solicitudService.eliminar(id, user.id());
        flash.addFlashAttribute(ok ? "message" : "error",
                ok ? "Solicitud eliminada." : "No tienes permiso para eliminar esta solicitud.");
        return "redirect:/panel";
    }

    /** GET /tablon-tareas — Tablón público de solicitudes abiertas (para contratistas). */
    @GetMapping("/tablon-tareas")
    public String tablonTareas(@AuthenticationPrincipal BrixoUserPrincipal user,
                               Model model) {
        model.addAttribute("user", user);
        model.addAttribute("solicitudes", solicitudService.tablonAbierto());
        return "solicitudes";
    }

    /** GET /solicitudes — Mis solicitudes (lista personal del usuario). */
    @GetMapping("/solicitudes")
    public String misSolicitudes(@AuthenticationPrincipal BrixoUserPrincipal user,
                                 Model model) {
        model.addAttribute("user", user);
        model.addAttribute("solicitudes", solicitudService.findByContratista(user.id()));
        return "solicitud/lista";
    }
}
