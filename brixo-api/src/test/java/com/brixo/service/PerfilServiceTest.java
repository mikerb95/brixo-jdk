package com.brixo.service;

import com.brixo.dto.PerfilUpdateRequest;
import com.brixo.entity.*;
import com.brixo.enums.UserRole;
import com.brixo.repository.*;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.util.Collections;
import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("PerfilService")
class PerfilServiceTest {

    @Mock
    private ClienteRepository clienteRepo;
    @Mock
    private ContratistaRepository contratistaRepo;
    @Mock
    private ContratistaServicioRepository csRepo;
    @Mock
    private CertificacionRepository certRepo;
    @Mock
    private ResenaRepository resenaRepo;
    @Mock
    private StorageService storageService;

    @InjectMocks
    private PerfilService service;

    // ═══════════════════════════════════════════
    // find methods
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("findCliente() delega a repository")
    void findCliente_delegates() {
        Cliente c = new Cliente();
        c.setNombre("Test");
        when(clienteRepo.findById(1L)).thenReturn(Optional.of(c));

        assertThat(service.findCliente(1L)).isPresent()
                .get().extracting(Cliente::getNombre).isEqualTo("Test");
    }

    @Test
    @DisplayName("findContratista() delega a repository")
    void findContratista_delegates() {
        Contratista ct = new Contratista();
        ct.setNombre("Pro");
        when(contratistaRepo.findById(5L)).thenReturn(Optional.of(ct));

        assertThat(service.findContratista(5L)).isPresent()
                .get().extracting(Contratista::getNombre).isEqualTo("Pro");
    }

    // ═══════════════════════════════════════════
    // getPublicProfile
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("getPublicProfile() ensambla perfil completo del contratista")
    void getPublicProfile_assemblesFullProfile() {
        Contratista ct = Contratista.builder().id(10L).nombre("Carlos").build();
        when(contratistaRepo.findById(10L)).thenReturn(Optional.of(ct));
        when(csRepo.findByContratistaIdWithDetails(10L)).thenReturn(Collections.emptyList());
        when(certRepo.findByContratistaId(10L)).thenReturn(Collections.emptyList());
        when(resenaRepo.findByContratistaId(10L)).thenReturn(Collections.emptyList());
        when(resenaRepo.getAverageRatingByContratistaId(10L)).thenReturn(4.2);

        Optional<PerfilService.ContratistaProfile> profile = service.getPublicProfile(10L);

        assertThat(profile).isPresent();
        assertThat(profile.get().contratista().getNombre()).isEqualTo("Carlos");
        assertThat(profile.get().calificacionPromedio()).isEqualTo(4.2);
    }

    @Test
    @DisplayName("getPublicProfile() retorna empty para ID inexistente")
    void getPublicProfile_emptyWhenNotFound() {
        when(contratistaRepo.findById(99L)).thenReturn(Optional.empty());

        assertThat(service.getPublicProfile(99L)).isEmpty();
    }

    // ═══════════════════════════════════════════
    // updateProfile
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("updateProfile()")
    class UpdateProfile {

        @Test
        @DisplayName("Actualiza nombre y teléfono del cliente")
        void updatesClienteFields() {
            Cliente c = new Cliente();
            c.setId(1L);
            c.setNombre("Old");
            when(clienteRepo.findById(1L)).thenReturn(Optional.of(c));
            when(storageService.uploadProfilePhoto(any(), anyString())).thenReturn(null);

            var req = new PerfilUpdateRequest(
                    "New Name", "3001112222", "Bogotá",
                    null, null, null, null, null);
            service.updateProfile(1L, UserRole.CLIENTE, req);

            assertThat(c.getNombre()).isEqualTo("New Name");
            assertThat(c.getTelefono()).isEqualTo("3001112222");
            verify(clienteRepo).save(c);
        }

        @Test
        @DisplayName("Actualiza campos exclusivos del contratista")
        void updatesContratistaFields() {
            Contratista ct = new Contratista();
            ct.setId(5L);
            when(contratistaRepo.findById(5L)).thenReturn(Optional.of(ct));
            when(storageService.uploadProfilePhoto(any(), anyString())).thenReturn(null);

            var req = new PerfilUpdateRequest(
                    "Nuevo", "3109999", "Medellín",
                    "6.2,-75.5", "10 años", "Desc nueva", "https://portafolio.com", null);
            service.updateProfile(5L, UserRole.CONTRATISTA, req);

            assertThat(ct.getNombre()).isEqualTo("Nuevo");
            assertThat(ct.getUbicacionMapa()).isEqualTo("6.2,-75.5");
            assertThat(ct.getExperiencia()).isEqualTo("10 años");
            assertThat(ct.getPortafolio()).isEqualTo("https://portafolio.com");
            verify(contratistaRepo).save(ct);
        }

        @Test
        @DisplayName("Actualiza foto de perfil cuando se sube archivo")
        void updatesProfilePhoto() {
            Cliente c = new Cliente();
            c.setId(1L);
            when(clienteRepo.findById(1L)).thenReturn(Optional.of(c));
            when(storageService.uploadProfilePhoto(any(), eq("profiles")))
                    .thenReturn("https://s3.amazonaws.com/profiles/abc.jpg");

            var req = new PerfilUpdateRequest(
                    null, null, null, null, null, null, null, null);
            service.updateProfile(1L, UserRole.CLIENTE, req);

            assertThat(c.getFotoPerfil()).isEqualTo("https://s3.amazonaws.com/profiles/abc.jpg");
        }

        @Test
        @DisplayName("Lanza excepción para rol ADMIN")
        void throwsForAdminRole() {
            var req = new PerfilUpdateRequest(
                    "X", null, null, null, null, null, null, null);
            assertThatThrownBy(() -> service.updateProfile(1L, UserRole.ADMIN, req))
                    .isInstanceOf(IllegalArgumentException.class)
                    .hasMessageContaining("Rol no soportado");
        }
    }
}
