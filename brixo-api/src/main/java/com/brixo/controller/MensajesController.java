package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.service.MensajeService;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

/**
 * Controlador de mensajería.
 *
 * Rutas:
 *   GET  /mensajes                           — Lista de conversaciones
 *   GET  /mensajes/chat/{otroId}/{otroRol}   — Chat con un usuario
 *   POST /mensajes/enviar                    — Enviar mensaje (AJAX)
 *   GET  /mensajes/nuevos/{otroId}/{otroRol} — Polling nuevos mensajes (AJAX)
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
        var conversaciones = mensajeService.getConversaciones(user.id(), user.rol().name().toLowerCase());
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
        var mensajes = mensajeService.getChat(user.id(), user.rol().name().toLowerCase(),
                otroId, otroRol);
        String nombreOtro = mensajeService.resolveUserName(otroId, otroRol);

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
            mensajeService.enviar(user.id(), user.rol().name().toLowerCase(),
                    destinatarioId, destinatarioRol, contenido);
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
        var nuevos = mensajeService.getNuevosMensajes(user.id(), user.rol().name().toLowerCase(),
                otroId, otroRol);
        return ResponseEntity.ok(nuevos);
    }
}
