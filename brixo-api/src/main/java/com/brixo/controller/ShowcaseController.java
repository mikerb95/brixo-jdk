package com.brixo.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

/**
 * Controlador del showcase / demo de componentes UI.
 */
@Controller
public class ShowcaseController {

    @GetMapping("/showcase")
    public String index() {
        return "showcase";
    }
}
