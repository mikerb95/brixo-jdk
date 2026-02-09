package com.brixo.controller;

import com.brixo.service.PasswordResetService;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

/**
 * Controlador de recuperación de contraseña.
 * Rutas:  /password/forgot, /password/reset/{token}, /password/update
 */
@Controller
@RequestMapping("/password")
public class PasswordResetController {

    private final PasswordResetService resetService;

    public PasswordResetController(PasswordResetService resetService) {
        this.resetService = resetService;
    }

    /** GET /password/forgot */
    @GetMapping("/forgot")
    public String showForgotForm() {
        return "auth/forgot_password";
    }

    /** POST /password/send-reset */
    @PostMapping("/send-reset")
    public String sendResetLink(@RequestParam String correo,
                                HttpServletRequest request,
                                RedirectAttributes flash) {
        try {
            String baseUrl = request.getScheme() + "://" + request.getServerName()
                    + (request.getServerPort() != 80 && request.getServerPort() != 443
                    ? ":" + request.getServerPort() : "");
            resetService.sendResetLink(correo, baseUrl);
            flash.addFlashAttribute("message",
                    "Si el correo existe, recibirás un enlace de recuperación en unos minutos.");
        } catch (Exception e) {
            flash.addFlashAttribute("error", e.getMessage());
        }
        return "redirect:/password/forgot";
    }

    /** GET /password/reset/{token} */
    @GetMapping("/reset/{token}")
    public String showResetForm(@PathVariable String token,
                                Model model,
                                RedirectAttributes flash) {
        var emailOpt = resetService.validateToken(token);
        if (emailOpt.isEmpty()) {
            flash.addFlashAttribute("error",
                    "El enlace de recuperación es inválido o ha expirado. Solicita uno nuevo.");
            return "redirect:/password/forgot";
        }
        model.addAttribute("token", token);
        model.addAttribute("email", emailOpt.get());
        return "auth/reset_password";
    }

    /** POST /password/update */
    @PostMapping("/update")
    public String processReset(@RequestParam String token,
                               @RequestParam String email,
                               @RequestParam String password,
                               @RequestParam("password_confirm") String passwordConfirm,
                               RedirectAttributes flash) {
        if (!password.equals(passwordConfirm)) {
            flash.addFlashAttribute("error", "Las contraseñas no coinciden.");
            return "redirect:/password/reset/" + token;
        }
        boolean ok = resetService.resetPassword(token, password);
        if (ok) {
            flash.addFlashAttribute("message",
                    "¡Contraseña actualizada! Ya puedes iniciar sesión con tu nueva contraseña.");
            return "redirect:/login";
        } else {
            flash.addFlashAttribute("error",
                    "El enlace de recuperación es inválido o ha expirado.");
            return "redirect:/password/forgot";
        }
    }
}
