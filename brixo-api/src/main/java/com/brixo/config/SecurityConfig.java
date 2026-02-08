package com.brixo.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;

/**
 * Configuración de Spring Security.
 *
 * Replica el comportamiento del AuthFilter de CodeIgniter 4:
 * - Rutas públicas:  /, /login, /register, /password/*, /map, /api/v1/track, assets
 * - Rutas protegidas: /panel/**, /perfil/**, /mensajes/**, /solicitud/**,
 *                     /admin/**, /reportes/**, /analytics/**
 */
@Configuration
@EnableWebSecurity
public class SecurityConfig {

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }

    @Bean
    public SecurityFilterChain filterChain(HttpSecurity http) throws Exception {
        http
            .authorizeHttpRequests(auth -> auth
                // ── Recursos estáticos ──
                .requestMatchers("/css/**", "/js/**", "/images/**", "/favicon.ico").permitAll()

                // ── Rutas públicas ──
                .requestMatchers(
                    "/", "/login", "/register",
                    "/password/**",
                    "/map", "/mapa",
                    "/especialidades", "/especialidades/**",
                    "/perfil/ver/**",
                    "/api/v1/track",
                    "/showcase",
                    "/slides", "/remote", "/presenter", "/main-panel", "/demo",
                    "/api/slide", "/api/demo"
                ).permitAll()

                // ── Páginas estáticas (info) ──
                .requestMatchers(
                    "/sobre-nosotros", "/como-funciona", "/seguridad", "/ayuda",
                    "/unete-pro", "/historias-exito", "/recursos", "/carreras",
                    "/prensa", "/blog", "/politica-cookies"
                ).permitAll()

                // ── Admin — requiere rol ADMIN ──
                .requestMatchers("/admin/**").hasRole("ADMIN")

                // ── Todo lo demás requiere autenticación ──
                .anyRequest().authenticated()
            )
            .formLogin(form -> form
                .loginPage("/login")
                .loginProcessingUrl("/login")
                .usernameParameter("correo")
                .passwordParameter("contrasena")
                .defaultSuccessUrl("/panel", true)
                .failureUrl("/login?error=true")
                .permitAll()
            )
            .logout(logout -> logout
                .logoutUrl("/logout")
                .logoutSuccessUrl("/")
                .invalidateHttpSession(true)
                .deleteCookies("SESSION")
                .permitAll()
            )
            .csrf(csrf -> csrf
                // Deshabilitar CSRF para endpoints de API (analytics, AJAX)
                .ignoringRequestMatchers("/api/**", "/mensajes/enviar", "/mensajes/nuevos/**")
            );

        return http.build();
    }
}
