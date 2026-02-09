package com.brixo.controller;

import com.brixo.service.MapaService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

/**
 * Controlador del mapa de contratistas.
 * Ruta: /map, /mapa
 */
@Controller
public class MapaController {

    private final MapaService mapaService;

    public MapaController(MapaService mapaService) {
        this.mapaService = mapaService;
    }

    @GetMapping({"/map", "/mapa"})
    public String index(Model model) {
        var professionals = mapaService.getContratistaPins();
        model.addAttribute("professionals", professionals);
        return "mapa";
    }
}
