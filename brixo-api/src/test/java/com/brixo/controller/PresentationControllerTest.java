package com.brixo.controller;

import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.http.MediaType;
import org.springframework.security.test.context.support.WithAnonymousUser;
import org.springframework.test.web.servlet.MockMvc;

import java.util.Map;

import static org.hamcrest.Matchers.is;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(PresentationController.class)
@DisplayName("PresentationController")
class PresentationControllerTest {

    @Autowired
    private MockMvc mockMvc;
    @Autowired
    private ObjectMapper objectMapper;

    // ═══════════════════════════════════════════
    // View routes (all public)
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("Rutas de vista")
    @WithAnonymousUser
    class ViewRoutes {

        @Test
        @DisplayName("GET /slides retorna vista 'presentation/slides' con totalSlides")
        void slides() throws Exception {
            mockMvc.perform(get("/slides"))
                    .andExpect(status().isOk())
                    .andExpect(view().name("presentation/slides"))
                    .andExpect(model().attribute("totalSlides", 11));
        }

        @Test
        @DisplayName("GET /remote retorna vista 'presentation/remote'")
        void remote() throws Exception {
            mockMvc.perform(get("/remote"))
                    .andExpect(status().isOk())
                    .andExpect(view().name("presentation/remote"))
                    .andExpect(model().attribute("totalSlides", 11));
        }

        @Test
        @DisplayName("GET /presenter retorna vista 'presentation/presenter'")
        void presenter() throws Exception {
            mockMvc.perform(get("/presenter"))
                    .andExpect(status().isOk())
                    .andExpect(view().name("presentation/presenter"))
                    .andExpect(model().attribute("totalSlides", 11));
        }

        @Test
        @DisplayName("GET /main-panel retorna vista 'presentation/main_panel'")
        void mainPanel() throws Exception {
            mockMvc.perform(get("/main-panel"))
                    .andExpect(status().isOk())
                    .andExpect(view().name("presentation/main_panel"))
                    .andExpect(model().attribute("totalSlides", 11));
        }

        @Test
        @DisplayName("GET /demo retorna vista 'presentation/demo'")
        void demo() throws Exception {
            mockMvc.perform(get("/demo"))
                    .andExpect(status().isOk())
                    .andExpect(view().name("presentation/demo"))
                    .andExpect(model().attribute("totalSlides", 11));
        }
    }

    // ═══════════════════════════════════════════
    // Slide API
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("API /api/slide")
    class SlideApi {

        @Test
        @DisplayName("GET /api/slide retorna slide actual (default=1)")
        void getSlide_default() throws Exception {
            mockMvc.perform(get("/api/slide"))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.slide", is(1)));
        }

        @Test
        @DisplayName("POST /api/slide cambia el slide y lo retorna")
        void setSlide() throws Exception {
            mockMvc.perform(post("/api/slide")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(Map.of("slide", 5))))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.slide", is(5)));

            // Verify state persisted
            mockMvc.perform(get("/api/slide"))
                    .andExpect(jsonPath("$.slide", is(5)));
        }

        @Test
        @DisplayName("POST /api/slide clampea al rango [1, totalSlides]")
        void setSlide_clampsRange() throws Exception {
            mockMvc.perform(post("/api/slide")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(Map.of("slide", 99))))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.slide", is(11)));

            mockMvc.perform(post("/api/slide")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(Map.of("slide", 0))))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.slide", is(1)));
        }
    }

    // ═══════════════════════════════════════════
    // Demo API
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("API /api/demo")
    class DemoApi {

        @Test
        @DisplayName("GET /api/demo retorna estado inicial (inactive, empty url)")
        void getDemo_default() throws Exception {
            mockMvc.perform(get("/api/demo"))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.active", is(false)))
                    .andExpect(jsonPath("$.url", is("")));
        }

        @Test
        @DisplayName("POST /api/demo activa demo con URL")
        void setDemo_activates() throws Exception {
            mockMvc.perform(post("/api/demo")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(
                            Map.of("url", "https://brixo.com.mx", "active", true))))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.active", is(true)))
                    .andExpect(jsonPath("$.url", is("https://brixo.com.mx")));
        }

        @Test
        @DisplayName("POST /api/demo desactiva demo")
        void setDemo_deactivates() throws Exception {
            // First activate
            mockMvc.perform(post("/api/demo")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(
                            Map.of("url", "https://brixo.com.mx", "active", true))));

            // Then deactivate
            mockMvc.perform(post("/api/demo")
                    .contentType(MediaType.APPLICATION_JSON)
                    .content(objectMapper.writeValueAsString(
                            Map.of("url", "", "active", false))))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.active", is(false)));
        }
    }
}
