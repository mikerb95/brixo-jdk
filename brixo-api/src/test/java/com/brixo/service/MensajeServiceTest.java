package com.brixo.service;

import com.brixo.dto.MensajeRequest;
import com.brixo.entity.Mensaje;
import com.brixo.enums.UserRole;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.MensajeRepository;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
@DisplayName("MensajeService")
class MensajeServiceTest {

    @Mock
    private MensajeRepository mensajeRepo;
    @Mock
    private ClienteRepository clienteRepo;
    @Mock
    private ContratistaRepository contratistaRepo;

    @InjectMocks
    private MensajeService service;

    @Test
    @DisplayName("enviar() persiste mensaje con remitente, destinatario y contenido")
    void enviar_savesCorrectly() {
        MensajeRequest req = new MensajeRequest(2L, "CONTRATISTA", "Hola, necesito ayuda");
        Mensaje saved = Mensaje.builder()
                .id(1L)
                .remitenteId(1L)
                .remitenteRol(UserRole.CLIENTE)
                .destinatarioId(2L)
                .destinatarioRol(UserRole.CONTRATISTA)
                .contenido("Hola, necesito ayuda")
                .leido(false)
                .build();

        when(mensajeRepo.save(any(Mensaje.class))).thenReturn(saved);

        Mensaje result = service.enviar(1L, UserRole.CLIENTE, req);

        assertThat(result.getContenido()).isEqualTo("Hola, necesito ayuda");
        assertThat(result.getLeido()).isFalse();

        ArgumentCaptor<Mensaje> captor = ArgumentCaptor.forClass(Mensaje.class);
        verify(mensajeRepo).save(captor.capture());
        Mensaje captured = captor.getValue();
        assertThat(captured.getRemitenteId()).isEqualTo(1L);
        assertThat(captured.getDestinatarioRol()).isEqualTo(UserRole.CONTRATISTA);
    }

    @Test
    @DisplayName("getNombreUsuario() retorna nombre del cliente")
    void getNombreUsuario_cliente() {
        Cliente cliente = new Cliente();
        cliente.setNombre("María López");
        when(clienteRepo.findById(1L)).thenReturn(Optional.of(cliente));

        String nombre = service.getNombreUsuario(1L, UserRole.CLIENTE);

        assertThat(nombre).isEqualTo("María López");
    }

    @Test
    @DisplayName("getNombreUsuario() retorna nombre del contratista")
    void getNombreUsuario_contratista() {
        Contratista contratista = new Contratista();
        contratista.setNombre("Carlos Ruiz");
        when(contratistaRepo.findById(5L)).thenReturn(Optional.of(contratista));

        String nombre = service.getNombreUsuario(5L, UserRole.CONTRATISTA);

        assertThat(nombre).isEqualTo("Carlos Ruiz");
    }

    @Test
    @DisplayName("getNombreUsuario() retorna placeholder si no existe")
    void getNombreUsuario_notFound() {
        when(clienteRepo.findById(99L)).thenReturn(Optional.empty());

        String nombre = service.getNombreUsuario(99L, UserRole.CLIENTE);

        assertThat(nombre).isEqualTo("Cliente #99");
    }

    @Test
    @DisplayName("getChat() marca mensajes como leídos antes de retornar")
    void getChat_marksAsRead() {
        service.getChat(1L, UserRole.CLIENTE, 2L, UserRole.CONTRATISTA);

        verify(mensajeRepo).marcarComoLeidos(1L, UserRole.CLIENTE, 2L, UserRole.CONTRATISTA);
        verify(mensajeRepo).findChat(1L, UserRole.CLIENTE, 2L, UserRole.CONTRATISTA);
    }
}
