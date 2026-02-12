package com.brixo.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.concurrent.atomic.AtomicReference;

/**
 * Controlador de la presentación académica.
 *
 * Rutas de vista:
 *   GET /slides      — Proyector de diapositivas (pantalla principal)
 *   GET /remote      — Control remoto móvil
 *   GET /presenter   — Notas del presentador
 *   GET /main-panel  — Panel de control completo
 *   GET /demo        — Proyector de demo (slides + iframe de demo URL)
 *
 * API (estado compartido en memoria):
 *   GET  /api/slide  — Estado actual {slide: N}
 *   POST /api/slide  — Cambiar slide {slide: N}
 *   GET  /api/demo   — Estado de demo {url: "...", active: true/false}
 *   POST /api/demo   — Cambiar demo URL {url: "...", active: true/false}
 */
@Controller
public class PresentationController {

    private static final int TOTAL_SLIDES = 11;

    // In-memory presentation state (single-presenter)
    private final AtomicInteger currentSlide = new AtomicInteger(1);
    private final AtomicReference<String> demoUrl = new AtomicReference<>("");
    private volatile boolean demoActive = false;

    // ── View routes ──

    @GetMapping("/slides")
    public String slides(Model model) {
        model.addAttribute("totalSlides", TOTAL_SLIDES);
        return "presentation/slides";
    }

    @GetMapping("/remote")
    public String remote(Model model) {
        model.addAttribute("totalSlides", TOTAL_SLIDES);
        return "presentation/remote";
    }

    @GetMapping("/presenter")
    public String presenter(Model model) {
        model.addAttribute("totalSlides", TOTAL_SLIDES);
        return "presentation/presenter";
    }

    @GetMapping("/main-panel")
    public String mainPanel(Model model) {
        model.addAttribute("totalSlides", TOTAL_SLIDES);
        return "presentation/main_panel";
    }

    @GetMapping("/demo")
    public String demo(Model model) {
        model.addAttribute("totalSlides", TOTAL_SLIDES);
        return "presentation/demo";
    }

    // ── API: slide state ──

    @GetMapping("/api/slide")
    @ResponseBody
    public Map<String, Object> getSlide() {
        return Map.of("slide", currentSlide.get());
    }

    @PostMapping("/api/slide")
    @ResponseBody
    public Map<String, Object> setSlide(@RequestBody Map<String, Integer> body) {
        int s = body.getOrDefault("slide", 1);
        s = Math.max(1, Math.min(s, TOTAL_SLIDES));
        currentSlide.set(s);
        return Map.of("slide", s);
    }

    // ── API: demo state ──

    @GetMapping("/api/demo")
    @ResponseBody
    public Map<String, Object> getDemo() {
        return Map.of("url", demoUrl.get(), "active", demoActive);
    }

    @PostMapping("/api/demo")
    @ResponseBody
    public Map<String, Object> setDemo(@RequestBody Map<String, Object> body) {
        String url = (String) body.getOrDefault("url", "");
        Boolean active = (Boolean) body.getOrDefault("active", false);
        demoUrl.set(url != null ? url : "");
        demoActive = Boolean.TRUE.equals(active);
        return Map.of("url", demoUrl.get(), "active", demoActive);
    }
}
