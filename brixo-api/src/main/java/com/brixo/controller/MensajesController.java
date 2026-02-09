package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.MensajeRequest;
import com.brixo.enums.UserRole;
import com.brixo.service.MensajeService;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

/**
 * Controlador de mensajería.
 */
@Controller
public class MensajesController {

    private final MensajeService mensajeService;

    public MensajesController(MensajeService mensajeService) {
        this.mensajeService = mensajeService;
    }

    /** GET /mensajes — Lista de conversaciones. */
    @GetMapping("/mensajes")
    public String index(@AuthenticationPrincipal BrixoUserPrincipal user,
                        Model model) {
        var conversaciones = mensajeService.getConversaciones(user.id(), user.rol());
        model.addAttribute("conversaciones", conversaciones);
        model.addAttribute("user", user);
        return "mensajes/index";
    }

    /** GET /mensajes/chat/{otroId}/{otroRol} — Vista del chat. */
    @GetMapping("/mensajes/chat/{otroId}/{otroRol}")
    public String chat(@AuthenticationPrincipal BrixoUserPrincipal user,
                       @PathVariable Long otroId,
                       @PathVariable String otroRol,
                       Model model) {
        UserRole otherRole = UserRole.valueOf(otroRol.toUpperCase());
        var mensajes = mensajeService.getChat(user.id(), user.rol(), otroId, otherRole);
        String nombreOtro = mensajeService.getNombreUsuario(otroId, otherRole);

        model.addAttribute("mensajes", mensajes);
        model.addAttribute("otroId", otroId);
        model.addAttribute("otroRol", otroRol);
        model.addAttribute("nombreOtro", nombreOtro);
        model.addAttribute("miId", user.id());
        model.addAttribute("miRol", user.rol().name().toLowerCase());
        model.addAttribute("user", user);
        return "mensajes/chat";
    }

    /** POST /mensajes/enviar — Enviar mensaje (AJAX). */
    @PostMapping("/mensajes/enviar")
    @ResponseBody
    public ResponseEntity<Map<String, String>> enviar(
            @AuthenticationPrincipal BrixoUserPrincipal user,
            @RequestParam("destinatario_id") Long destinatarioId,
            @RequestParam("destinatario_rol") String destinatarioRol,
            @RequestParam String contenido) {
        try {
            var req = new MensajeRequest(destinatarioId, destinatarioRol, contenido);
            mensajeService.enviar(user.id(), user.rol(), req);
            return ResponseEntity.ok(Map.of("status", "success"));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(Map.of("status", "error", "message", e.getMessage()));
        }
    }

    /** GET /mensajes/nuevos/{otroId}/{otroRol} — Polling AJAX. */
    @GetMapping("/mensajes/nuevos/{otroId}/{otroRol}")
    @ResponseBody
    public ResponseEntity<?> nuevos(@AuthenticationPrincipal BrixoUserPrincipal user,
                                    @PathVariable Long otroId,
                                    @PathVariable String otroRol) {
        UserRole otherRole = UserRole.valueOf(otroRol.toUpperCase());
        var nuevos = mensajeService.getNuevos(user.id(), user.rol(), otroId, otherRole, null);
        return ResponseEntity.ok(nuevos);
    }
}
