package com.brixo.service;

import com.brixo.dto.SolicitudRequest;
import com.brixo.entity.Cliente;
import com.brixo.entity.Solicitud;
import com.brixo.enums.EstadoSolicitud;
import com.brixo.repository.SolicitudRepository;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.math.BigDecimal;
import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("SolicitudService")
class SolicitudServiceTest {

    @Mock
    private SolicitudRepository solicitudRepo;

    @InjectMocks
    private SolicitudService service;

    private SolicitudRequest request;
    private Solicitud solicitud;

    @BeforeEach
    void setUp() {
        request = new SolicitudRequest("Plomería urgente", "Fuga en la cocina",
                new BigDecimal("150000"), "Bogotá, Chapinero");

        Cliente cliente = new Cliente();
        cliente.setId(1L);

        solicitud = Solicitud.builder()
                .id(10L)
                .titulo("Plomería urgente")
                .descripcion("Fuga en la cocina")
                .presupuesto(new BigDecimal("150000"))
                .ubicacion("Bogotá, Chapinero")
                .estado(EstadoSolicitud.ABIERTA)
                .cliente(cliente)
                .build();
    }

    @Test
    @DisplayName("crear() persiste solicitud con estado ABIERTA y FK del cliente")
    void crear_savesWithCorrectData() {
        when(solicitudRepo.save(any(Solicitud.class))).thenReturn(solicitud);

        Solicitud result = service.crear(1L, request);

        assertThat(result.getTitulo()).isEqualTo("Plomería urgente");
        assertThat(result.getEstado()).isEqualTo(EstadoSolicitud.ABIERTA);

        ArgumentCaptor<Solicitud> captor = ArgumentCaptor.forClass(Solicitud.class);
        verify(solicitudRepo).save(captor.capture());
        Solicitud saved = captor.getValue();
        assertThat(saved.getCliente().getId()).isEqualTo(1L);
        assertThat(saved.getPresupuesto()).isEqualByComparingTo("150000");
    }

    @Test
    @DisplayName("actualizar() modifica campos si el cliente es propietario")
    void actualizar_ownerCanUpdate() {
        when(solicitudRepo.findById(10L)).thenReturn(Optional.of(solicitud));
        when(solicitudRepo.save(any())).thenReturn(solicitud);

        SolicitudRequest update = new SolicitudRequest("Plomería actualizada",
                "Fuga corregida", new BigDecimal("200000"), "Bogotá, Usaquén");

        boolean ok = service.actualizar(10L, 1L, update);

        assertThat(ok).isTrue();
        verify(solicitudRepo).save(any());
    }

    @Test
    @DisplayName("actualizar() rechaza si el cliente NO es propietario")
    void actualizar_nonOwnerRejected() {
        when(solicitudRepo.findById(10L)).thenReturn(Optional.of(solicitud));

        boolean ok = service.actualizar(10L, 99L, request);

        assertThat(ok).isFalse();
        verify(solicitudRepo, never()).save(any());
    }

    @Test
    @DisplayName("actualizar() retorna false si la solicitud no existe")
    void actualizar_notFound() {
        when(solicitudRepo.findById(999L)).thenReturn(Optional.empty());

        boolean ok = service.actualizar(999L, 1L, request);

        assertThat(ok).isFalse();
    }

    @Test
    @DisplayName("eliminar() borra la solicitud del propietario")
    void eliminar_ownerCanDelete() {
        when(solicitudRepo.findById(10L)).thenReturn(Optional.of(solicitud));

        boolean ok = service.eliminar(10L, 1L);

        assertThat(ok).isTrue();
        verify(solicitudRepo).delete(solicitud);
    }

    @Test
    @DisplayName("eliminar() rechaza si otro usuario intenta borrar")
    void eliminar_nonOwnerRejected() {
        when(solicitudRepo.findById(10L)).thenReturn(Optional.of(solicitud));

        boolean ok = service.eliminar(10L, 99L);

        assertThat(ok).isFalse();
        verify(solicitudRepo, never()).delete(any());
    }

    @Test
    @DisplayName("tablonAbierto() delega a repository con estado ABIERTA")
    void tablonAbierto_delegatesToRepo() {
        when(solicitudRepo.findByEstadoOrderByCreadoEnDesc(EstadoSolicitud.ABIERTA))
                .thenReturn(List.of(solicitud));

        List<Solicitud> result = service.tablonAbierto();

        assertThat(result).hasSize(1);
        verify(solicitudRepo).findByEstadoOrderByCreadoEnDesc(EstadoSolicitud.ABIERTA);
    }

    @Test
    @DisplayName("findByCliente() retorna solicitudes del cliente")
    void findByCliente_delegatesToRepo() {
        when(solicitudRepo.findByClienteIdOrderByCreadoEnDesc(1L))
                .thenReturn(List.of(solicitud));

        List<Solicitud> result = service.findByCliente(1L);

        assertThat(result).hasSize(1);
        assertThat(result.get(0).getTitulo()).isEqualTo("Plomería urgente");
    }
}
