package com.brixo.service;

import com.brixo.dto.RegisterRequest;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
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

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyString;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("RegistroService")
class RegistroServiceTest {

    @Mock
    private ClienteRepository clienteRepo;
    @Mock
    private ContratistaRepository contratistaRepo;
    @Mock
    private AdminRepository adminRepo;
    @Mock
    private PasswordEncoder passwordEncoder;
    @Mock
    private StorageService storageService;

    @InjectMocks
    private RegistroService service;

    // ── Helper: valid base request ──

    private RegisterRequest validClienteRequest() {
        return new RegisterRequest(
                "Juan Pérez", "juan@test.com",
                "Password1!", "Password1!",
                "cliente", "3001234567", "Bogotá",
                null, null, null, null);
    }

    private RegisterRequest validContratistaRequest() {
        return new RegisterRequest(
                "Ana López", "ana@test.com",
                "Secure9#x", "Secure9#x",
                "contratista", "3109876543", "Medellín",
                "6.2442,-75.5812", "5 años", "Experta en electricidad", null);
    }

    // ═══════════════════════════════════════════
    // Password validation
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("Validación de contraseña")
    class PasswordValidation {

        @Test
        @DisplayName("Rechaza contraseña corta (<8 chars)")
        void rejectsShortPassword() {
            var req = new RegisterRequest("Test", "t@t.com", "Ab1!", "Ab1!",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("8 caracteres"));
        }

        @Test
        @DisplayName("Rechaza contraseña sin mayúscula")
        void rejectsNoUppercase() {
            var req = new RegisterRequest("Test", "t@t.com", "password1!", "password1!",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("mayúscula"));
        }

        @Test
        @DisplayName("Rechaza contraseña sin minúscula")
        void rejectsNoLowercase() {
            var req = new RegisterRequest("Test", "t@t.com", "PASSWORD1!", "PASSWORD1!",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("minúscula"));
        }

        @Test
        @DisplayName("Rechaza contraseña sin dígito")
        void rejectsNoDigit() {
            var req = new RegisterRequest("Test", "t@t.com", "Password!", "Password!",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("dígito"));
        }

        @Test
        @DisplayName("Rechaza contraseña sin carácter especial")
        void rejectsNoSpecialChar() {
            var req = new RegisterRequest("Test", "t@t.com", "Password1a", "Password1a",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("especial"));
        }

        @Test
        @DisplayName("Rechaza contraseñas que no coinciden")
        void rejectsMismatch() {
            var req = new RegisterRequest("Test", "t@t.com", "Password1!", "Different1!",
                    "cliente", null, null, null, null, null, null);
            List<String> errors = service.register(req);
            assertThat(errors).anyMatch(e -> e.contains("no coinciden"));
        }
    }

    // ═══════════════════════════════════════════
    // Email uniqueness
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("Rechaza correo duplicado en tabla clientes")
    void rejectsDuplicateEmailCliente() {
        when(clienteRepo.existsByCorreo("juan@test.com")).thenReturn(true);

        List<String> errors = service.register(validClienteRequest());

        assertThat(errors).anyMatch(e -> e.contains("Ya existe"));
        verify(clienteRepo, never()).save(any());
    }

    @Test
    @DisplayName("Rechaza correo duplicado en tabla contratistas")
    void rejectsDuplicateEmailContratista() {
        when(clienteRepo.existsByCorreo("juan@test.com")).thenReturn(false);
        when(contratistaRepo.existsByCorreo("juan@test.com")).thenReturn(true);

        List<String> errors = service.register(validClienteRequest());

        assertThat(errors).anyMatch(e -> e.contains("Ya existe"));
    }

    @Test
    @DisplayName("isEmailTaken() verifica las 3 tablas")
    void isEmailTaken_checksAllTables() {
        when(clienteRepo.existsByCorreo("x@x.com")).thenReturn(false);
        when(contratistaRepo.existsByCorreo("x@x.com")).thenReturn(false);
        when(adminRepo.existsByCorreo("x@x.com")).thenReturn(true);

        assertThat(service.isEmailTaken("x@x.com")).isTrue();
    }

    // ═══════════════════════════════════════════
    // Successful registration
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("Registro exitoso de cliente persiste con contraseña hasheada")
    void registerCliente_success() {
        when(passwordEncoder.encode(anyString())).thenReturn("$2a$hashed");
        when(storageService.uploadProfilePhoto(any(), anyString())).thenReturn(null);

        List<String> errors = service.register(validClienteRequest());

        assertThat(errors).isEmpty();

        ArgumentCaptor<Cliente> captor = ArgumentCaptor.forClass(Cliente.class);
        verify(clienteRepo).save(captor.capture());
        Cliente saved = captor.getValue();
        assertThat(saved.getNombre()).isEqualTo("Juan Pérez");
        assertThat(saved.getCorreo()).isEqualTo("juan@test.com");
        assertThat(saved.getContrasena()).isEqualTo("$2a$hashed");
    }

    @Test
    @DisplayName("Registro exitoso de contratista persiste campos extras")
    void registerContratista_success() {
        when(passwordEncoder.encode(anyString())).thenReturn("$2a$hashed");
        when(storageService.uploadProfilePhoto(any(), anyString())).thenReturn(null);

        List<String> errors = service.register(validContratistaRequest());

        assertThat(errors).isEmpty();

        ArgumentCaptor<Contratista> captor = ArgumentCaptor.forClass(Contratista.class);
        verify(contratistaRepo).save(captor.capture());
        Contratista saved = captor.getValue();
        assertThat(saved.getNombre()).isEqualTo("Ana López");
        assertThat(saved.getUbicacionMapa()).isEqualTo("6.2442,-75.5812");
        assertThat(saved.getExperiencia()).isEqualTo("5 años");
    }

    @Test
    @DisplayName("Registro no guarda contratista cuando se pide cliente")
    void registerCliente_doesNotSaveContratista() {
        when(passwordEncoder.encode(anyString())).thenReturn("hash");
        when(storageService.uploadProfilePhoto(any(), anyString())).thenReturn(null);

        service.register(validClienteRequest());

        verify(clienteRepo).save(any(Cliente.class));
        verify(contratistaRepo, never()).save(any());
    }
}
