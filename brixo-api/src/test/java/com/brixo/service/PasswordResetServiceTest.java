package com.brixo.service;

import com.brixo.entity.Cliente;
import com.brixo.entity.PasswordResetToken;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.PasswordResetTokenRepository;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.time.LocalDateTime;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("PasswordResetService")
class PasswordResetServiceTest {

    @Mock
    private PasswordResetTokenRepository tokenRepo;
    @Mock
    private ClienteRepository clienteRepo;
    @Mock
    private ContratistaRepository contratistaRepo;
    @Mock
    private AdminRepository adminRepo;
    @Mock
    private PasswordEncoder passwordEncoder;
    @Mock
    private EmailService emailService;

    @InjectMocks
    private PasswordResetService service;

    // ═══════════════════════════════════════════
    // sendResetLink
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("sendResetLink()")
    class SendResetLink {

        @Test
        @DisplayName("Retorna false si el email no existe en ninguna tabla")
        void returnsFalseForUnknownEmail() {
            when(clienteRepo.existsByCorreo("ghost@test.com")).thenReturn(false);
            when(contratistaRepo.existsByCorreo("ghost@test.com")).thenReturn(false);
            when(adminRepo.existsByCorreo("ghost@test.com")).thenReturn(false);

            boolean result = service.sendResetLink("ghost@test.com", "http://localhost");

            assertThat(result).isFalse();
            verify(tokenRepo, never()).save(any());
            verify(emailService, never()).sendPasswordReset(anyString(), anyString());
        }

        @Test
        @DisplayName("Genera token, lo guarda y envía email cuando el correo existe")
        void sendsResetEmailForKnownUser() {
            when(clienteRepo.existsByCorreo("user@test.com")).thenReturn(true);

            boolean result = service.sendResetLink("user@test.com", "http://localhost:8080");

            assertThat(result).isTrue();
            verify(tokenRepo).deleteByEmail("user@test.com");

            ArgumentCaptor<PasswordResetToken> captor = ArgumentCaptor.forClass(PasswordResetToken.class);
            verify(tokenRepo).save(captor.capture());
            PasswordResetToken saved = captor.getValue();
            assertThat(saved.getEmail()).isEqualTo("user@test.com");
            assertThat(saved.getToken()).isNotBlank(); // SHA-256 hash
            assertThat(saved.getExpiresAt()).isAfter(LocalDateTime.now());

            verify(emailService).sendPasswordReset(eq("user@test.com"), contains("/password/reset/"));
        }

        @Test
        @DisplayName("Elimina tokens previos antes de crear uno nuevo")
        void deletesPreviousTokens() {
            when(contratistaRepo.existsByCorreo("pro@test.com")).thenReturn(true);

            service.sendResetLink("pro@test.com", "http://localhost");

            var inOrder = inOrder(tokenRepo);
            inOrder.verify(tokenRepo).deleteByEmail("pro@test.com");
            inOrder.verify(tokenRepo).save(any());
        }
    }

    // ═══════════════════════════════════════════
    // validateToken
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("validateToken()")
    class ValidateToken {

        @Test
        @DisplayName("Retorna email para token válido no expirado")
        void returnsEmailForValidToken() {
            PasswordResetToken token = PasswordResetToken.builder()
                    .email("user@test.com")
                    .token("dummy-hash")
                    .expiresAt(LocalDateTime.now().plusHours(1))
                    .build();
            // validateToken hashes the raw token, so we mock findByToken broadly
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.of(token));

            Optional<String> email = service.validateToken("raw-uuid-token");

            assertThat(email).isPresent().contains("user@test.com");
        }

        @Test
        @DisplayName("Retorna empty para token expirado")
        void returnsEmptyForExpiredToken() {
            PasswordResetToken token = PasswordResetToken.builder()
                    .email("user@test.com")
                    .token("expired-hash")
                    .expiresAt(LocalDateTime.now().minusHours(1))
                    .build();
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.of(token));

            Optional<String> email = service.validateToken("raw-token");

            assertThat(email).isEmpty();
        }

        @Test
        @DisplayName("Retorna empty para token no encontrado")
        void returnsEmptyForUnknownToken() {
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.empty());

            assertThat(service.validateToken("nonexistent")).isEmpty();
        }
    }

    // ═══════════════════════════════════════════
    // resetPassword
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("resetPassword()")
    class ResetPassword {

        @Test
        @DisplayName("Retorna false para token inválido")
        void returnsFalseForInvalidToken() {
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.empty());

            assertThat(service.resetPassword("bad-token", "NewPass1!")).isFalse();
        }

        @Test
        @DisplayName("Retorna false para token expirado")
        void returnsFalseForExpiredToken() {
            PasswordResetToken token = PasswordResetToken.builder()
                    .email("user@test.com")
                    .token("hash")
                    .expiresAt(LocalDateTime.now().minusMinutes(5))
                    .build();
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.of(token));

            assertThat(service.resetPassword("expired", "NewPass1!")).isFalse();
        }

        @Test
        @DisplayName("Actualiza contraseña del cliente y elimina token")
        void resetsClientePassword() {
            PasswordResetToken token = PasswordResetToken.builder()
                    .email("client@test.com")
                    .token("valid-hash")
                    .expiresAt(LocalDateTime.now().plusHours(1))
                    .build();
            when(tokenRepo.findByToken(anyString())).thenReturn(Optional.of(token));
            when(passwordEncoder.encode("NewPass1!")).thenReturn("$2a$encoded");

            Cliente cliente = new Cliente();
            cliente.setCorreo("client@test.com");
            when(clienteRepo.findByCorreo("client@test.com")).thenReturn(Optional.of(cliente));
            when(contratistaRepo.findByCorreo("client@test.com")).thenReturn(Optional.empty());
            when(adminRepo.findByCorreo("client@test.com")).thenReturn(Optional.empty());

            boolean result = service.resetPassword("raw-token", "NewPass1!");

            assertThat(result).isTrue();
            assertThat(cliente.getContrasena()).isEqualTo("$2a$encoded");
            verify(clienteRepo).save(cliente);
            verify(tokenRepo).deleteByEmail("client@test.com");
        }
    }
}
