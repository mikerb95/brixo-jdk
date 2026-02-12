package com.brixo.controller;

import com.brixo.dto.CotizacionResult;
import com.brixo.service.CotizadorService;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.security.test.context.support.WithAnonymousUser;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.web.servlet.MockMvc;

import java.util.List;
import java.util.Optional;

import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.when;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(CotizadorController.class)
@DisplayName("CotizadorController")
class CotizadorControllerTest {

    @Autowired
    private MockMvc mockMvc;
    @MockitoBean
    private CotizadorService cotizadorService;

    // ═══════════════════════════════════════════
    // GET /cotizador
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("GET /cotizador retorna vista 'cotizador/index'")
    @WithMockUser
    void index_returnsFormView() throws Exception {
        mockMvc.perform(get("/cotizador"))
                .andExpect(status().isOk())
                .andExpect(view().name("cotizador/index"));
    }

    @Test
    @DisplayName("GET /cotizador sin autenticar redirige a login")
    @WithAnonymousUser
    void index_requiresAuth() throws Exception {
        mockMvc.perform(get("/cotizador"))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrlPattern("**/login"));
    }

    // ═══════════════════════════════════════════
    // POST /cotizador/generar
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("POST /cotizador/generar")
    class Generar {

        @Test
        @DisplayName("Retorna 400 si la descripción es muy corta")
        @WithMockUser
        void rejectsShortDescription() throws Exception {
            mockMvc.perform(post("/cotizador/generar")
                    .with(csrf())
                    .param("descripcion", "corto"))
                    .andExpect(status().isBadRequest())
                    .andExpect(jsonPath("$.ok").value(false))
                    .andExpect(jsonPath("$.error").exists());
        }

        @Test
        @DisplayName("Retorna cotización exitosa del LLM")
        @WithMockUser
        void returnsSuccessfulQuote() throws Exception {
            CotizacionResult result = new CotizacionResult(
                    "Plomería", List.of(), List.of(), "MEDIA", 300000.0);
            when(cotizadorService.generar(anyString())).thenReturn(Optional.of(result));

            mockMvc.perform(post("/cotizador/generar")
                    .with(csrf())
                    .param("descripcion", "Reparar tubería de agua en la cocina"))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.ok").value(true))
                    .andExpect(jsonPath("$.data.servicioPrincipal").value("Plomería"));
        }

        @Test
        @DisplayName("Retorna error cuando LLM falla")
        @WithMockUser
        void handlesLlmFailure() throws Exception {
            when(cotizadorService.generar(anyString())).thenReturn(Optional.empty());

            mockMvc.perform(post("/cotizador/generar")
                    .with(csrf())
                    .param("descripcion", "Instalación eléctrica completa en oficina"))
                    .andExpect(status().isOk())
                    .andExpect(jsonPath("$.ok").value(false))
                    .andExpect(jsonPath("$.error").exists());
        }
    }

    // ═══════════════════════════════════════════
    // GET /cotizador/exito
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("GET /cotizador/exito retorna vista 'cotizador/exito'")
    @WithMockUser
    void exito_returnsView() throws Exception {
        mockMvc.perform(get("/cotizador/exito"))
                .andExpect(status().isOk())
                .andExpect(view().name("cotizador/exito"));
    }
}
