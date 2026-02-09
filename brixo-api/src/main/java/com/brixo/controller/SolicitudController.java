package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.enums.UserRole;
import com.brixo.service.SolicitudService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Controlador de solicitudes de servicio.
 *
 * Rutas:
 *  GET  /solicitud/nueva           — Formulario nueva solicitud (cliente)
 *  POST /solicitud/guardar         — Crear solicitud
 *  GET  /solicitud/editar/{id}     — Formulario editar (cliente, dueño)
 *  POST /solicitud/actualizar/{id} — Actualizar solicitud
 *  GET  /solicitud/eliminar/{id}   — Eliminar solicitud (cliente, dueño)
 *  GET  /tablon-tareas             — Listado solicitudes abiertas (contratista)
 *  GET  /solicitudes               — Mis contratos asignados (contratista)
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

        // Pre-fill from cotizador (if available)
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
                          @RequestParam(defaultValue = "0") double presupuesto,
                          @RequestParam(required = false) String ubicacion,
                          @RequestParam(required = false) String departamento,
                          @RequestParam(required = false) String ciudad,
                          @RequestParam(name = "id_contratista", required = false) Long idContratista,
                          RedirectAttributes flash) {
        try {
            String ubicacionFinal = ubicacion != null ? ubicacion : "";
            if (ciudad != null && !ciudad.isBlank() && departamento != null && !departamento.isBlank()) {
                ubicacionFinal = ciudad + ", " + departamento +
                        (ubicacion != null && !ubicacion.isBlank() ? " - " + ubicacion : "");
            }

            solicitudService.crear(user.id(), titulo, descripcion, presupuesto, ubicacionFinal, idContratista);

            String msg = idContratista != null
                    ? "Solicitud enviada al contratista correctamente."
                    : "Solicitud publicada en el tablón de tareas abiertas.";
            flash.addFlashAttribute("message", msg);
        } catch (IllegalArgumentException e) {
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
        var solicitud = solicitudService.getForOwner(id, user.id());
        if (solicitud == null) {
            flash.addFlashAttribute("error", "Solicitud no encontrada o sin permiso.");
            return "redirect:/panel";
        }
        model.addAttribute("solicitud", solicitud);
        return "solicitud/editar";
    }

    /** POST /solicitud/actualizar/{id} */
    @PostMapping("/solicitud/actualizar/{id}")
    public String actualizar(@AuthenticationPrincipal BrixoUserPrincipal user,
                             @PathVariable Long id,
                             @RequestParam String titulo,
                             @RequestParam String descripcion,
                             @RequestParam(defaultValue = "0") double presupuesto,
                             @RequestParam(required = false) String ubicacion,
                             RedirectAttributes flash) {
        try {
            solicitudService.actualizar(id, user.id(), titulo, descripcion, presupuesto, ubicacion);
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
        try {
            solicitudService.eliminar(id, user.id());
            flash.addFlashAttribute("message", "Solicitud eliminada correctamente.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/panel";
    }

    /** GET /tablon-tareas — Solicitudes abiertas para contratistas. */
    @GetMapping("/tablon-tareas")
    public String tablonTareas(@AuthenticationPrincipal BrixoUserPrincipal user,
                               Model model) {
        var solicitudes = solicitudService.tablonAbierto();
        model.addAttribute("user", user);
        model.addAttribute("solicitudes", solicitudes);
        return "solicitud/lista";
    }

    /** GET /solicitudes — Contratos del contratista autenticado. */
    @GetMapping("/solicitudes")
    public String misSolicitudes(@AuthenticationPrincipal BrixoUserPrincipal user,
                                 Model model) {
        model.addAttribute("user", user);
        // For contratistas: show assigned solicitudes
        var solicitudes = solicitudService.getByContratistaId(user.id());
        model.addAttribute("solicitudes", solicitudes);
        return "solicitudes";
    }
}
