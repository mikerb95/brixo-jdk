package com.brixo.controller;

import com.brixo.service.MapaService;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
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
    private final ObjectMapper objectMapper;

    public MapaController(MapaService mapaService, ObjectMapper objectMapper) {
        this.mapaService = mapaService;
        this.objectMapper = objectMapper;
    }

    @GetMapping({"/map", "/mapa"})
    public String index(Model model) throws JsonProcessingException {
        var professionals = mapaService.getAllPins();
        model.addAttribute("professionals", professionals);
        model.addAttribute("professionalsJson", objectMapper.writeValueAsString(professionals));
        model.addAttribute("navMode", "map");
        return "mapa";
    }
}
