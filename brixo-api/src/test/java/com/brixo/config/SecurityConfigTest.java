package com.brixo.config;

import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.params.ParameterizedTest;
import org.junit.jupiter.params.provider.ValueSource;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.web.servlet.MockMvc;

import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.user;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.redirectedUrlPattern;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

/**
 * Test de integración para verificar que SecurityConfig
 * protege y expone las rutas correctas.
 */
@SpringBootTest
@AutoConfigureMockMvc
@ActiveProfiles("test")
@DisplayName("SecurityConfig — rutas públicas y protegidas")
class SecurityConfigTest {

    @Autowired
    private MockMvc mockMvc;

    // ═══════════════════════════════════════════
    // Rutas públicas — deben ser accesibles sin auth
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("Rutas públicas (200 OK sin autenticación)")
    class PublicRoutes {

        @ParameterizedTest(name = "{0}")
        @ValueSource(strings = {
                "/",
                "/mapa",
                "/map",
                "/especialidades",
                "/showcase",
                "/servicios",
                "/productos"
        })
        @DisplayName("Páginas principales accesibles")
        void mainPublicPages(String path) throws Exception {
            mockMvc.perform(get(path))
                    .andExpect(status().isOk());
        }

        @ParameterizedTest(name = "{0}")
        @ValueSource(strings = {
                "/sobre-nosotros",
                "/como-funciona",
                "/seguridad",
                "/ayuda",
                "/unete-pro",
                "/historias-exito",
                "/recursos",
                "/carreras",
                "/prensa",
                "/blog",
                "/politica-cookies"
        })
        @DisplayName("Páginas de info accesibles")
        void infoPages(String path) throws Exception {
            mockMvc.perform(get(path))
                    .andExpect(status().isOk());
        }

        @ParameterizedTest(name = "{0}")
        @ValueSource(strings = {
                "/slides",
                "/remote",
                "/presenter",
                "/main-panel",
                "/demo"
        })
        @DisplayName("Rutas de presentación accesibles")
        void presentationRoutes(String path) throws Exception {
            mockMvc.perform(get(path))
                    .andExpect(status().isOk());
        }

        @Test
        @DisplayName("API /api/slide accesible sin auth")
        void slideApiPublic() throws Exception {
            mockMvc.perform(get("/api/slide"))
                    .andExpect(status().isOk());
        }

        @Test
        @DisplayName("API /api/demo accesible sin auth")
        void demoApiPublic() throws Exception {
            mockMvc.perform(get("/api/demo"))
                    .andExpect(status().isOk());
        }
    }

    // ═══════════════════════════════════════════
    // Rutas protegidas — requieren autenticación
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("Rutas protegidas (redirigen a /login)")
    class ProtectedRoutes {

        @ParameterizedTest(name = "{0}")
        @ValueSource(strings = {
                "/panel",
                "/cotizador",
                "/solicitud/nueva",
                "/mensajes",
                "/perfil"
        })
        @DisplayName("Rutas de usuario redirigen a login")
        void userRoutesRequireAuth(String path) throws Exception {
            mockMvc.perform(get(path))
                    .andExpect(status().is3xxRedirection())
                    .andExpect(redirectedUrlPattern("**/login"));
        }

        @ParameterizedTest(name = "{0}")
        @ValueSource(strings = {
                "/admin",
                "/admin/usuarios"
        })
        @DisplayName("Rutas de admin redirigen a login cuando anónimo")
        void adminRoutesRequireAuth(String path) throws Exception {
            mockMvc.perform(get(path))
                    .andExpect(status().is3xxRedirection())
                    .andExpect(redirectedUrlPattern("**/login"));
        }
    }

    // ═══════════════════════════════════════════
    // Recursos estáticos — siempre accesibles
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("CSS estático accesible sin auth")
    void staticCssAccessible() throws Exception {
        mockMvc.perform(get("/css/styles.css"))
                .andExpect(status().isOk());
    }

    @Test
    @DisplayName("JS estático accesible sin auth")
    void staticJsAccessible() throws Exception {
        mockMvc.perform(get("/js/navbar.js"))
                .andExpect(status().isOk());
    }
}
