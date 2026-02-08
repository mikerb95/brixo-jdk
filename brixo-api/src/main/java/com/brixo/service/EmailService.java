package com.brixo.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.MimeMessageHelper;
import org.springframework.stereotype.Service;

import jakarta.mail.MessagingException;
import jakarta.mail.internet.MimeMessage;

/**
 * Servicio de envío de emails.
 * En modo desarrollo, simula el envío logueando el contenido.
 *
 * Equivale a la lógica de email en PasswordReset::sendResetLink del PHP legacy.
 */
@Service
public class EmailService {

    private static final Logger log = LoggerFactory.getLogger(EmailService.class);

    private final JavaMailSender mailSender;

    @Value("${spring.mail.username:no-reply@brixo.com}")
    private String fromAddress;

    @Value("${spring.profiles.active:dev}")
    private String activeProfile;

    public EmailService(JavaMailSender mailSender) {
        this.mailSender = mailSender;
    }

    /**
     * Envía un email HTML.
     *
     * @param to      destinatario
     * @param subject asunto
     * @param body    contenido HTML
     */
    public void sendHtml(String to, String subject, String body) {
        if (isDevMode()) {
            log.info("""
                    ══════════════════════════════════════
                    📧 EMAIL (dev mode — no enviado)
                    Para: {}
                    Asunto: {}
                    ──────────────────────────────────────
                    {}
                    ══════════════════════════════════════
                    """, to, subject, body);
            return;
        }

        try {
            MimeMessage message = mailSender.createMimeMessage();
            MimeMessageHelper helper = new MimeMessageHelper(message, true, "UTF-8");
            helper.setFrom(fromAddress);
            helper.setTo(to);
            helper.setSubject(subject);
            helper.setText(body, true);

            mailSender.send(message);
            log.info("Email enviado exitosamente a: {}", to);

        } catch (MessagingException e) {
            log.error("Error al enviar email a {}: {}", to, e.getMessage());
            throw new RuntimeException("No se pudo enviar el email", e);
        }
    }

    /**
     * Envía el email de restablecimiento de contraseña.
     */
    public void sendPasswordReset(String to, String resetUrl) {
        String subject = "Brixo — Restablecer contraseña";
        String body = """
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #333;">Restablecer contraseña</h2>
                    <p>Has solicitado restablecer tu contraseña en Brixo.</p>
                    <p>Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                    <p style="margin: 20px 0;">
                        <a href="%s"
                           style="background-color: #007bff; color: white; padding: 12px 24px;
                                  text-decoration: none; border-radius: 5px;">
                            Restablecer contraseña
                        </a>
                    </p>
                    <p style="color: #666; font-size: 14px;">
                        Este enlace expira en 1 hora.<br>
                        Si no solicitaste este cambio, ignora este email.
                    </p>
                    <hr style="border: none; border-top: 1px solid #eee;">
                    <p style="color: #999; font-size: 12px;">Brixo — Conectando necesidades con soluciones locales</p>
                </div>
                """.formatted(resetUrl);

        sendHtml(to, subject, body);
    }

    private boolean isDevMode() {
        return "dev".equalsIgnoreCase(activeProfile) || fromAddress.isBlank();
    }
}
