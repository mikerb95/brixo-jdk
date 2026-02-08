package com.brixo.service;

import com.brixo.entity.PasswordResetToken;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.PasswordResetTokenRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.time.LocalDateTime;
import java.util.HexFormat;
import java.util.Optional;
import java.util.UUID;

/**
 * Servicio de restablecimiento de contraseña.
 *
 * Replica la lógica de PasswordReset del sistema PHP legacy:
 * - Genera token UUID, almacena hash SHA-256
 * - Token expira en 1 hora
 * - Envía email con enlace de reset
 * - Valida token y actualiza contraseña en la tabla correcta
 */
@Service
public class PasswordResetService {

    private static final Logger log = LoggerFactory.getLogger(PasswordResetService.class);
    private static final int TOKEN_EXPIRY_HOURS = 1;

    private final PasswordResetTokenRepository tokenRepo;
    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;
    private final AdminRepository adminRepo;
    private final PasswordEncoder passwordEncoder;
    private final EmailService emailService;

    public PasswordResetService(PasswordResetTokenRepository tokenRepo,
                                ClienteRepository clienteRepo,
                                ContratistaRepository contratistaRepo,
                                AdminRepository adminRepo,
                                PasswordEncoder passwordEncoder,
                                EmailService emailService) {
        this.tokenRepo = tokenRepo;
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
        this.adminRepo = adminRepo;
        this.passwordEncoder = passwordEncoder;
        this.emailService = emailService;
    }

    /**
     * Genera un token de reset y envía el email.
     *
     * @param email   correo del usuario
     * @param baseUrl URL base de la aplicación (e.g. http://localhost:8080)
     * @return true si el correo existe y se envió el email; false si no hay cuenta
     */
    @Transactional
    public boolean sendResetLink(String email, String baseUrl) {
        // Verificar que el correo existe en alguna tabla
        boolean exists = clienteRepo.existsByCorreo(email)
                || contratistaRepo.existsByCorreo(email)
                || adminRepo.existsByCorreo(email);

        if (!exists) {
            return false;
        }

        // Eliminar tokens anteriores del mismo email
        tokenRepo.deleteByEmail(email);

        // Generar token
        String rawToken = UUID.randomUUID().toString();
        String hashedToken = sha256(rawToken);

        PasswordResetToken resetToken = PasswordResetToken.builder()
                .email(email)
                .token(hashedToken)
                .expiresAt(LocalDateTime.now().plusHours(TOKEN_EXPIRY_HOURS))
                .build();
        tokenRepo.save(resetToken);

        // Enviar email
        String resetUrl = baseUrl + "/password/reset/" + rawToken;
        emailService.sendPasswordReset(email, resetUrl);

        log.info("Token de reset generado para: {}", email);
        return true;
    }

    /**
     * Valida un token de reset.
     *
     * @return el email asociado si el token es válido, vacío si no
     */
    public Optional<String> validateToken(String rawToken) {
        String hashed = sha256(rawToken);
        return tokenRepo.findByToken(hashed)
                .filter(t -> !t.isExpired())
                .map(PasswordResetToken::getEmail);
    }

    /**
     * Restablece la contraseña usando un token válido.
     *
     * @return true si se actualizó; false si el token era inválido
     */
    @Transactional
    public boolean resetPassword(String rawToken, String newPassword) {
        String hashed = sha256(rawToken);
        Optional<PasswordResetToken> tokenOpt = tokenRepo.findByToken(hashed);

        if (tokenOpt.isEmpty() || tokenOpt.get().isExpired()) {
            return false;
        }

        PasswordResetToken token = tokenOpt.get();
        String email = token.getEmail();
        String encoded = passwordEncoder.encode(newPassword);

        // Actualizar en la tabla correcta
        clienteRepo.findByCorreo(email).ifPresent(c -> {
            c.setContrasena(encoded);
            clienteRepo.save(c);
        });
        contratistaRepo.findByCorreo(email).ifPresent(c -> {
            c.setContrasena(encoded);
            contratistaRepo.save(c);
        });
        adminRepo.findByCorreo(email).ifPresent(a -> {
            a.setContrasena(encoded);
            adminRepo.save(a);
        });

        // Eliminar token usado
        tokenRepo.deleteByEmail(email);

        log.info("Contraseña restablecida para: {}", email);
        return true;
    }

    private String sha256(String input) {
        try {
            MessageDigest digest = MessageDigest.getInstance("SHA-256");
            byte[] hash = digest.digest(input.getBytes(StandardCharsets.UTF_8));
            return HexFormat.of().formatHex(hash);
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException("SHA-256 no disponible", e);
        }
    }
}
