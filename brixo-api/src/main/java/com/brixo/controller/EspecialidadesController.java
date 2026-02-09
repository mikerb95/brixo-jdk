package com.brixo.controller;

import com.brixo.service.EspecialidadesService;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;

/**
 * Controlador de especialidades (categorías y servicios).
 * Rutas: /especialidades, /especialidades/categoria/{id}
 */
@Controller
public class EspecialidadesController {

    private final EspecialidadesService especialidadesService;

    public EspecialidadesController(EspecialidadesService especialidadesService) {
        this.especialidadesService = especialidadesService;
    }

    @GetMapping("/especialidades")
    public String index(Model model) {
        var categorias = especialidadesService.getAllCategorias();
        var serviciosPorCategoria = especialidadesService.getServiciosByCategoria(null);
        model.addAttribute("categorias", categorias);
        model.addAttribute("especialidades", serviciosPorCategoria);
        return "especialidades";
    }

    @GetMapping("/especialidades/categoria/{id}")
    public String categoria(@PathVariable Long id, Model model) {
        var categoria = especialidadesService.getCategoriaById(id);
        var servicios = especialidadesService.getServiciosByCategoria(id);
        model.addAttribute("categoria", categoria);
        model.addAttribute("servicios", servicios);
        return "categoria_detalle";
    }
}
