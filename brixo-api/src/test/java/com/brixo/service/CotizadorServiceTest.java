package com.brixo.service;

import com.brixo.dto.CotizacionResult;
import com.brixo.entity.CotizacionConfirmada;
import com.brixo.enums.Complejidad;
import com.brixo.enums.EstadoCotizacion;
import com.brixo.repository.CotizacionConfirmadaRepository;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.Spy;
import org.mockito.junit.jupiter.MockitoExtension;

import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
@DisplayName("CotizadorService")
class CotizadorServiceTest {

    @Mock
    private LlmService llmService;
    @Mock
    private CotizacionConfirmadaRepository cotizacionRepo;
    @Spy
    private ObjectMapper mapper = new ObjectMapper();

    @InjectMocks
    private CotizadorService service;

    @Test
    @DisplayName("generar() delega al LlmService")
    void generar_delegatesToLlm() {
        CotizacionResult result = new CotizacionResult(
                "Plomería", List.of(), List.of(), "ALTA", 500000.0);
        when(llmService.generarCotizacion("Reparar tubería"))
                .thenReturn(Optional.of(result));

        Optional<CotizacionResult> response = service.generar("Reparar tubería");

        assertThat(response).isPresent();
        assertThat(response.get().servicioPrincipal()).isEqualTo("Plomería");
    }

    @Test
    @DisplayName("generar() retorna empty cuando LLM falla")
    void generar_emptyOnFailure() {
        when(llmService.generarCotizacion("???")).thenReturn(Optional.empty());

        Optional<CotizacionResult> response = service.generar("???");

        assertThat(response).isEmpty();
    }

    @Test
    @DisplayName("confirmar() persiste cotización con estado CONFIRMADA")
    void confirmar_savesWithCorrectState() {
        CotizacionResult result = new CotizacionResult(
                "Electricidad", List.of(), List.of(), "MEDIA", 350000.0);

        when(cotizacionRepo.save(any(CotizacionConfirmada.class)))
                .thenAnswer(inv -> inv.getArgument(0));

        CotizacionConfirmada saved = service.confirmar(1L, result, "Cambiar interruptores");

        assertThat(saved.getEstado()).isEqualTo(EstadoCotizacion.CONFIRMADA);
        assertThat(saved.getServicioPrincipal()).isEqualTo("Electricidad");
        assertThat(saved.getClienteId()).isEqualTo(1L);
        assertThat(saved.getComplejidad()).isEqualTo(Complejidad.MEDIA);

        verify(cotizacionRepo).save(any());
    }

    @Test
    @DisplayName("confirmar() parsea complejidad inválida como MEDIA")
    void confirmar_invalidComplejidadDefaultsToMedia() {
        CotizacionResult result = new CotizacionResult(
                "Pintura", List.of(), List.of(), "INVALIDA", 200000.0);

        when(cotizacionRepo.save(any(CotizacionConfirmada.class)))
                .thenAnswer(inv -> inv.getArgument(0));

        CotizacionConfirmada saved = service.confirmar(1L, result, "Pintar");

        assertThat(saved.getComplejidad()).isEqualTo(Complejidad.MEDIA);
    }
}
