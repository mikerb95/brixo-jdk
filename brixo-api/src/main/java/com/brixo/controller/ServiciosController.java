package com.brixo.controller;

import com.brixo.entity.Categoria;
import com.brixo.entity.Servicio;
import com.brixo.repository.CategoriaRepository;
import com.brixo.repository.ServicioRepository;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;

import java.util.List;

/**
 * Controlador del catálogo público de servicios.
 *
 * Rutas:
 * GET /servicios — Listado con filtro por categoría
 * GET /servicios/{id} — Detalle de un servicio
 */
@Controller
@RequestMapping("/servicios")
public class ServiciosController {

    private final ServicioRepository servicioRepo;
    private final CategoriaRepository categoriaRepo;

    public ServiciosController(ServicioRepository servicioRepo,
            CategoriaRepository categoriaRepo) {
        this.servicioRepo = servicioRepo;
        this.categoriaRepo = categoriaRepo;
    }

    @GetMapping
    public String index(@RequestParam(required = false) Long categoriaId, Model model) {
        List<Categoria> categorias = categoriaRepo.findAll();
        List<Servicio> servicios;

        if (categoriaId != null) {
            servicios = servicioRepo.findByCategoriaId(categoriaId);
            model.addAttribute("categoriaActiva", categoriaId);
        } else {
            servicios = servicioRepo.findAll();
        }

        model.addAttribute("categorias", categorias);
        model.addAttribute("servicios", servicios);
        return "servicios";
    }

    @GetMapping("/{id}")
    public String detalle(@PathVariable Long id, Model model) {
        Servicio servicio = servicioRepo.findByIdWithCategoria(id);
        if (servicio == null) {
            return "redirect:/servicios";
        }

        // Other services in same category for "related" section
        List<Servicio> relacionados = servicioRepo.findByCategoriaId(servicio.getCategoria().getId())
                .stream()
                .filter(s -> !s.getId().equals(id))
                .limit(3)
                .toList();

        model.addAttribute("servicio", servicio);
        model.addAttribute("relacionados", relacionados);
        return "servicio_detalle";
    }
}
