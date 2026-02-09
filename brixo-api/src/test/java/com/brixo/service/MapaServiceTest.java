package com.brixo.service;

import com.brixo.dto.ContratistaMapPin;
import com.brixo.entity.Contratista;
import com.brixo.entity.ContratistaServicio;
import com.brixo.entity.Servicio;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.ContratistaServicioRepository;
import com.brixo.repository.ResenaRepository;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.util.Collections;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
@DisplayName("MapaService")
class MapaServiceTest {

    @Mock
    private ContratistaRepository contratistaRepo;
    @Mock
    private ResenaRepository resenaRepo;
    @Mock
    private ContratistaServicioRepository csRepo;

    @InjectMocks
    private MapaService service;

    @Test
    @DisplayName("getAllPins() convierte contratistas con coordenadas válidas en pins")
    void getAllPins_validCoords() {
        Contratista c = Contratista.builder()
                .id(1L)
                .nombre("Juan Pérez")
                .ciudad("Bogotá")
                .fotoPerfil("/img/juan.jpg")
                .ubicacionMapa("4.6097, -74.0817")
                .build();

        Servicio servicio = new Servicio();
        servicio.setNombre("Plomería");
        ContratistaServicio cs = new ContratistaServicio();
        cs.setServicio(servicio);

        when(contratistaRepo.findAllWithLocation()).thenReturn(List.of(c));
        when(resenaRepo.getAverageRatingByContratistaId(1L)).thenReturn(4.5);
        when(resenaRepo.findByContratistaId(1L)).thenReturn(Collections.emptyList());
        when(csRepo.findByContratistaIdWithDetails(1L)).thenReturn(List.of(cs));

        List<ContratistaMapPin> pins = service.getAllPins();

        assertThat(pins).hasSize(1);
        ContratistaMapPin pin = pins.get(0);
        assertThat(pin.nombre()).isEqualTo("Juan Pérez");
        assertThat(pin.latitud()).isCloseTo(4.6097, org.assertj.core.data.Offset.offset(0.001));
        assertThat(pin.longitud()).isCloseTo(-74.0817, org.assertj.core.data.Offset.offset(0.001));
        assertThat(pin.calificacionPromedio()).isEqualTo(4.5);
        assertThat(pin.servicios()).containsExactly("Plomería");
    }

    @Test
    @DisplayName("getAllPins() omite contratistas sin coordenadas")
    void getAllPins_skipsNullCoords() {
        Contratista c = Contratista.builder()
                .id(2L)
                .nombre("Sin ubicación")
                .ubicacionMapa(null)
                .build();

        when(contratistaRepo.findAllWithLocation()).thenReturn(List.of(c));

        List<ContratistaMapPin> pins = service.getAllPins();

        assertThat(pins).isEmpty();
    }

    @Test
    @DisplayName("getAllPins() omite coordenadas malformadas")
    void getAllPins_skipsBadFormat() {
        Contratista c = Contratista.builder()
                .id(3L)
                .nombre("Formato malo")
                .ubicacionMapa("not-a-number")
                .build();

        when(contratistaRepo.findAllWithLocation()).thenReturn(List.of(c));

        List<ContratistaMapPin> pins = service.getAllPins();

        assertThat(pins).isEmpty();
    }

    @Test
    @DisplayName("getAllPins() usa 0.0 cuando no hay calificación")
    void getAllPins_nullRating() {
        Contratista c = Contratista.builder()
                .id(4L)
                .nombre("Nuevo pro")
                .ciudad("Medellín")
                .ubicacionMapa("6.2442,-75.5812")
                .build();

        when(contratistaRepo.findAllWithLocation()).thenReturn(List.of(c));
        when(resenaRepo.getAverageRatingByContratistaId(4L)).thenReturn(null);
        when(resenaRepo.findByContratistaId(4L)).thenReturn(Collections.emptyList());
        when(csRepo.findByContratistaIdWithDetails(4L)).thenReturn(Collections.emptyList());

        List<ContratistaMapPin> pins = service.getAllPins();

        assertThat(pins).hasSize(1);
        assertThat(pins.get(0).calificacionPromedio()).isEqualTo(0.0);
    }
}
