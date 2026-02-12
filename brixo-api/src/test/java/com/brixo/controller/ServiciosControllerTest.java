package com.brixo.controller;

import com.brixo.entity.Categoria;
import com.brixo.entity.Servicio;
import com.brixo.repository.CategoriaRepository;
import com.brixo.repository.ServicioRepository;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.security.test.context.support.WithAnonymousUser;
import org.springframework.test.web.servlet.MockMvc;

import java.math.BigDecimal;
import java.util.List;

import static org.hamcrest.Matchers.hasSize;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(ServiciosController.class)
@DisplayName("ServiciosController")
class ServiciosControllerTest {

    @Autowired
    private MockMvc mockMvc;
    @MockitoBean
    private ServicioRepository servicioRepo;
    @MockitoBean
    private CategoriaRepository categoriaRepo;

    private Categoria buildCategoria(Long id, String nombre) {
        Categoria c = new Categoria();
        c.setId(id);
        c.setNombre(nombre);
        return c;
    }

    private Servicio buildServicio(Long id, String nombre, Categoria cat) {
        return Servicio.builder()
                .id(id).nombre(nombre)
                .descripcion("Desc " + nombre)
                .precioEstimado(new BigDecimal("100000"))
                .categoria(cat)
                .build();
    }

    // ═══════════════════════════════════════════
    // GET /servicios
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("GET /servicios retorna vista con todos los servicios")
    @WithAnonymousUser
    void index_returnsAllServices() throws Exception {
        Categoria cat = buildCategoria(1L, "Plomería");
        when(categoriaRepo.findAll()).thenReturn(List.of(cat));
        when(servicioRepo.findAll()).thenReturn(List.of(
                buildServicio(1L, "Reparación tuberías", cat),
                buildServicio(2L, "Instalación sanitaria", cat)));

        mockMvc.perform(get("/servicios"))
                .andExpect(status().isOk())
                .andExpect(view().name("servicios"))
                .andExpect(model().attribute("servicios", hasSize(2)))
                .andExpect(model().attribute("categorias", hasSize(1)));
    }

    @Test
    @DisplayName("GET /servicios?categoriaId=1 filtra por categoría")
    @WithAnonymousUser
    void index_filtersByCategoria() throws Exception {
        Categoria cat = buildCategoria(1L, "Electricidad");
        when(categoriaRepo.findAll()).thenReturn(List.of(cat));
        when(servicioRepo.findByCategoriaId(1L)).thenReturn(List.of(
                buildServicio(10L, "Cableado", cat)));

        mockMvc.perform(get("/servicios").param("categoriaId", "1"))
                .andExpect(status().isOk())
                .andExpect(view().name("servicios"))
                .andExpect(model().attribute("servicios", hasSize(1)))
                .andExpect(model().attribute("categoriaActiva", 1L));
    }

    // ═══════════════════════════════════════════
    // GET /servicios/{id}
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("GET /servicios/{id} retorna vista de detalle")
    @WithAnonymousUser
    void detalle_returnsServiceDetail() throws Exception {
        Categoria cat = buildCategoria(1L, "Plomería");
        Servicio s = buildServicio(5L, "Reparación de tuberías", cat);

        when(servicioRepo.findByIdWithCategoria(5L)).thenReturn(s);
        when(servicioRepo.findByCategoriaId(1L)).thenReturn(List.of(
                s, buildServicio(6L, "Instalación", cat)));

        mockMvc.perform(get("/servicios/5"))
                .andExpect(status().isOk())
                .andExpect(view().name("servicio_detalle"))
                .andExpect(model().attributeExists("servicio"))
                .andExpect(model().attributeExists("relacionados"));
    }

    @Test
    @DisplayName("GET /servicios/{id} redirige si el servicio no existe")
    @WithAnonymousUser
    void detalle_redirectsWhenNotFound() throws Exception {
        when(servicioRepo.findByIdWithCategoria(999L)).thenReturn(null);

        mockMvc.perform(get("/servicios/999"))
                .andExpect(status().is3xxRedirection())
                .andExpect(redirectedUrl("/servicios"));
    }
}
