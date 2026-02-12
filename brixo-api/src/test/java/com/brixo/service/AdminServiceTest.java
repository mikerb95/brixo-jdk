package com.brixo.service;

import com.brixo.dto.AdminUserRequest;
import com.brixo.entity.Admin;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import com.brixo.enums.UserRole;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("AdminService")
class AdminServiceTest {

    @Mock
    private ClienteRepository clienteRepo;
    @Mock
    private ContratistaRepository contratistaRepo;
    @Mock
    private AdminRepository adminRepo;
    @Mock
    private PasswordEncoder passwordEncoder;

    @InjectMocks
    private AdminService service;

    // ═══════════════════════════════════════════
    // getStats
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("getStats() retorna conteo de cada tabla")
    void getStats_returnsCorrectCounts() {
        when(clienteRepo.count()).thenReturn(50L);
        when(contratistaRepo.count()).thenReturn(20L);
        when(adminRepo.count()).thenReturn(3L);

        var stats = service.getStats();

        assertThat(stats.clientes()).isEqualTo(50);
        assertThat(stats.contratistas()).isEqualTo(20);
        assertThat(stats.admins()).isEqualTo(3);
    }

    // ═══════════════════════════════════════════
    // getAllUsers
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("getAllUsers() combina clientes, contratistas y admins")
    void getAllUsers_combinesAllTables() {
        Cliente c = Cliente.builder().id(1L).nombre("C1").correo("c@t.com").ciudad("Bogotá").build();
        Contratista ct = Contratista.builder().id(1L).nombre("CT1").correo("ct@t.com").ciudad("Cali").build();
        Admin a = Admin.builder().id(1L).nombre("A1").correo("a@t.com").build();

        when(clienteRepo.findAll()).thenReturn(List.of(c));
        when(contratistaRepo.findAll()).thenReturn(List.of(ct));
        when(adminRepo.findAll()).thenReturn(List.of(a));

        var users = service.getAllUsers();

        assertThat(users).hasSize(3);
        assertThat(users).extracting(AdminService.UserListItem::rol)
                .containsExactly(UserRole.CLIENTE, UserRole.CONTRATISTA, UserRole.ADMIN);
    }

    // ═══════════════════════════════════════════
    // crearUsuario
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("crearUsuario()")
    class CrearUsuario {

        @Test
        @DisplayName("Crea cliente exitosamente")
        void createsCliente() {
            when(passwordEncoder.encode(anyString())).thenReturn("$hash");

            var req = new AdminUserRequest("Nuevo", "nuevo@t.com", "password", "300123", "Bogotá", "cliente");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).isEmpty();
            verify(clienteRepo).save(any(Cliente.class));
        }

        @Test
        @DisplayName("Crea contratista exitosamente")
        void createsContratista() {
            when(passwordEncoder.encode(anyString())).thenReturn("$hash");

            var req = new AdminUserRequest("Pro", "pro@t.com", "password", "310000", "Medellín", "contratista");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).isEmpty();
            verify(contratistaRepo).save(any(Contratista.class));
        }

        @Test
        @DisplayName("Crea admin exitosamente")
        void createsAdmin() {
            when(passwordEncoder.encode(anyString())).thenReturn("$hash");

            var req = new AdminUserRequest("Admin2", "a2@t.com", "password", null, null, "admin");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).isEmpty();
            verify(adminRepo).save(any(Admin.class));
        }

        @Test
        @DisplayName("Rechaza si el correo ya está registrado")
        void rejectsDuplicateEmail() {
            // AdminService has its own isEmailTaken — need to check that
            when(clienteRepo.existsByCorreo("dup@t.com")).thenReturn(true);

            var req = new AdminUserRequest("X", "dup@t.com", "pass", null, null, "cliente");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).anyMatch(e -> e.contains("Ya existe"));
            verify(clienteRepo, never()).save(any());
        }

        @Test
        @DisplayName("Rechaza sin contraseña")
        void rejectsBlankPassword() {
            var req = new AdminUserRequest("X", "x@t.com", "", null, null, "cliente");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).anyMatch(e -> e.contains("contraseña"));
        }

        @Test
        @DisplayName("Rechaza tipo de usuario inválido")
        void rejectsInvalidType() {
            when(passwordEncoder.encode(anyString())).thenReturn("$hash");

            var req = new AdminUserRequest("X", "x@t.com", "pass", null, null, "superuser");
            List<String> errors = service.crearUsuario(req);

            assertThat(errors).anyMatch(e -> e.contains("inválido"));
        }
    }

    // ═══════════════════════════════════════════
    // eliminarUsuario
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("eliminarUsuario()")
    class EliminarUsuario {

        @Test
        @DisplayName("Elimina cliente exitosamente")
        void deletesCliente() {
            boolean result = service.eliminarUsuario(5L, UserRole.CLIENTE, 1L);
            assertThat(result).isTrue();
            verify(clienteRepo).deleteById(5L);
        }

        @Test
        @DisplayName("Protección: admin no puede auto-eliminarse")
        void preventsSelfDeletion() {
            boolean result = service.eliminarUsuario(1L, UserRole.ADMIN, 1L);
            assertThat(result).isFalse();
            verify(adminRepo, never()).deleteById(any());
        }

        @Test
        @DisplayName("Admin puede eliminar otro admin")
        void canDeleteOtherAdmin() {
            boolean result = service.eliminarUsuario(2L, UserRole.ADMIN, 1L);
            assertThat(result).isTrue();
            verify(adminRepo).deleteById(2L);
        }
    }
}
