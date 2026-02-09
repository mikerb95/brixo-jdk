package com.brixo.controller;

import com.brixo.dto.ContratistaMapPin;
import com.brixo.service.MapaService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.bean.MockBean;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.web.servlet.MockMvc;

import java.util.List;

import static org.hamcrest.Matchers.*;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(MapaController.class)
@DisplayName("MapaController")
class MapaControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private MapaService mapaService;

    @Test
    @DisplayName("GET /mapa retorna vista 'mapa' con navMode='map' y professionalsJson")
    @WithMockUser
    void mapa_returnsViewWithMapMode() throws Exception {
        List<ContratistaMapPin> pins = List.of(
                new ContratistaMapPin(1L, "Juan", "Bogotá", null,
                        4.6097, -74.0817, 4.5, 10, List.of("Plomería"))
        );
        when(mapaService.getAllPins()).thenReturn(pins);

        mockMvc.perform(get("/mapa"))
                .andExpect(status().isOk())
                .andExpect(view().name("mapa"))
                .andExpect(model().attribute("navMode", "map"))
                .andExpect(model().attribute("professionals", hasSize(1)))
                .andExpect(model().attributeExists("professionalsJson"));
    }

    @Test
    @DisplayName("GET /map también funciona (alias)")
    @WithMockUser
    void map_aliasWorks() throws Exception {
        when(mapaService.getAllPins()).thenReturn(List.of());

        mockMvc.perform(get("/map"))
                .andExpect(status().isOk())
                .andExpect(view().name("mapa"));
    }
}
