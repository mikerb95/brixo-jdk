package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.entity.Contrato;
import com.brixo.entity.Resena;
import com.brixo.entity.Solicitud;
import com.brixo.enums.EstadoSolicitud;
import com.brixo.enums.UserRole;
import com.brixo.repository.ContratoRepository;
import com.brixo.repository.ResenaRepository;
import com.brixo.repository.SolicitudRepository;
import com.brixo.service.PerfilService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.List;

/**
 * Panel principal del usuario autenticado.
 * Muestra vistas diferentes según el rol (cliente / contratista).
 */
@Controller
public class PanelController {

    private final ContratoRepository contratoRepo;
    private final ResenaRepository resenaRepo;
    private final SolicitudRepository solicitudRepo;
    private final PerfilService perfilService;

    public PanelController(ContratoRepository contratoRepo,
                           ResenaRepository resenaRepo,
                           SolicitudRepository solicitudRepo,
                           PerfilService perfilService) {
        this.contratoRepo = contratoRepo;
        this.resenaRepo = resenaRepo;
        this.solicitudRepo = solicitudRepo;
        this.perfilService = perfilService;
    }

    @GetMapping("/panel")
    public String panel(@AuthenticationPrincipal BrixoUserPrincipal user, Model model) {
        model.addAttribute("user", user);

        if (user.rol() == UserRole.ADMIN) {
            return "redirect:/admin";
        }

        if (user.rol() == UserRole.CLIENTE) {
            List<Contrato> contracts = contratoRepo.findByClienteId(user.id());
            List<Resena> reviews = resenaRepo.findByClienteId(user.id());
            List<Solicitud> solicitudes = solicitudRepo.findByClienteIdOrderByCreadoEnDesc(user.id());

            model.addAttribute("contracts", contracts);
            model.addAttribute("reviews", reviews);
            model.addAttribute("solicitudes", solicitudes);
            return "panel/cliente";
        }

        // Contratista
        List<Contrato> contracts = contratoRepo.findByContratistaId(user.id());
        List<Resena> reviews = resenaRepo.findByContratistaId(user.id());
        List<Solicitud> solicitudesDisponibles = solicitudRepo.findByEstadoOrderByCreadoEnDesc(EstadoSolicitud.ABIERTA);

        model.addAttribute("contracts", contracts);
        model.addAttribute("reviews", reviews);
        model.addAttribute("solicitudesDisponibles", solicitudesDisponibles);
        return "panel/contratista";
    }

    /** Alias: GET /perfil redirige al panel */
    @GetMapping("/perfil")
    public String perfilRedirect() {
        return "redirect:/panel";
    }
}
