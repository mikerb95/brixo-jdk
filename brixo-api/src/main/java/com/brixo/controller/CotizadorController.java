package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.CotizacionResult;
import com.brixo.service.CotizadorService;
import jakarta.servlet.http.HttpSession;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import java.util.Map;
import java.util.Optional;

/**
 * Controlador del cotizador inteligente (IA).
 *
 * Rutas:
 *   GET  /cotizador           — Formulario
 *   POST /cotizador/generar   — Genera cotización (AJAX o form)
 *   POST /cotizador/confirmar — Confirma y guarda en BD
 *   GET  /cotizador/exito     — Vista de confirmación
 */
@Controller
@RequestMapping("/cotizador")
public class CotizadorController {

    private final CotizadorService cotizadorService;

    public CotizadorController(CotizadorService cotizadorService) {
        this.cotizadorService = cotizadorService;
    }

    /** GET /cotizador — Formulario principal. */
    @GetMapping
    public String index(@AuthenticationPrincipal BrixoUserPrincipal user, Model model) {
        if (user != null) model.addAttribute("user", user);
        return "cotizador/index";
    }

    /** POST /cotizador/generar — Genera cotización via LLM. */
    @PostMapping("/generar")
    @ResponseBody
    public ResponseEntity<?> generar(@RequestParam String descripcion,
                                     HttpSession session) {
        if (descripcion == null || descripcion.trim().length() < 10) {
            return ResponseEntity.badRequest()
                    .body(Map.of("ok", false, "error", "La descripción debe tener al menos 10 caracteres."));
        }

        Optional<CotizacionResult> resultado = cotizadorService.generar(descripcion.trim());

        if (resultado.isEmpty()) {
            return ResponseEntity.ok(
                    Map.of("ok", false, "error", "No se pudo generar la cotización. Intenta de nuevo."));
        }

        // Persist in session for confirm step
        session.setAttribute("ultima_cotizacion", Map.of(
                "descripcion", descripcion.trim(),
                "data", resultado.get()
        ));

        return ResponseEntity.ok(Map.of("ok", true, "data", resultado.get()));
    }

    /** POST /cotizador/confirmar — Confirmar cotización y guardar en BD. */
    @PostMapping("/confirmar")
    public String confirmar(@AuthenticationPrincipal BrixoUserPrincipal user,
                            HttpSession session,
                            RedirectAttributes flash) {
        if (user == null) {
            flash.addFlashAttribute("error", "Debes iniciar sesión para confirmar una cotización.");
            return "redirect:/login";
        }

        @SuppressWarnings("unchecked")
        var cotSession = (Map<String, Object>) session.getAttribute("ultima_cotizacion");
        if (cotSession == null) {
            flash.addFlashAttribute("error", "No hay cotización para confirmar. Genera una primero.");
            return "redirect:/cotizador";
        }

        var data = (CotizacionResult) cotSession.get("data");
        var descripcion = (String) cotSession.get("descripcion");

        cotizadorService.confirmar(user.id(), data, descripcion);

        // Pre-fill solicitud from cotización
        session.setAttribute("prefill_solicitud", Map.of(
                "titulo", data.servicioPrincipal(),
                "descripcion", descripcion + "\n\n--- Desglose estimado (IA) ---\n" + buildDesglose(data)
        ));

        session.removeAttribute("ultima_cotizacion");
        return "redirect:/solicitud/nueva";
    }

    private String buildDesglose(CotizacionResult data) {
        var sb = new StringBuilder();
        if (data.materiales() != null && !data.materiales().isEmpty()) {
            sb.append("Materiales:\n");
            for (var m : data.materiales()) {
                sb.append("  • ").append(m.nombre()).append(" — Cant: ").append(m.cantidad()).append("\n");
            }
        }
        if (data.personal() != null && !data.personal().isEmpty()) {
            sb.append("Personal:\n");
            for (var p : data.personal()) {
                sb.append("  • ").append(p.rol()).append(" — ").append(p.horas()).append(" hrs\n");
            }
        }
        sb.append("Complejidad: ").append(data.complejidad()).append("\n");
        sb.append("Total estimado: $").append(String.format("%,.0f", data.totalEstimado())).append(" COP");
        return sb.toString();
    }
}
