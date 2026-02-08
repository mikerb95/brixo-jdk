package com.brixo.service;

import com.brixo.dto.RegisterRequest;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Pattern;

/**
 * Servicio de registro de usuarios.
 *
 * Replica la lógica de Register::register del sistema PHP legacy:
 * - Validación de contraseña fuerte (8+ chars, mayúscula, minúscula, dígito, símbolo)
 * - Verificación de correo duplicado en las 3 tablas
 * - Upload de foto de perfil
 * - Creación de CLIENTE o CONTRATISTA según rol
 */
@Service
public class RegistroService {

    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;
    private final AdminRepository adminRepo;
    private final PasswordEncoder passwordEncoder;
    private final StorageService storageService;

    private static final Pattern UPPER = Pattern.compile("[A-Z]");
    private static final Pattern LOWER = Pattern.compile("[a-z]");
    private static final Pattern DIGIT = Pattern.compile("[0-9]");
    private static final Pattern SYMBOL = Pattern.compile("[^A-Za-z0-9]");

    public RegistroService(ClienteRepository clienteRepo,
                           ContratistaRepository contratistaRepo,
                           AdminRepository adminRepo,
                           PasswordEncoder passwordEncoder,
                           StorageService storageService) {
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
        this.adminRepo = adminRepo;
        this.passwordEncoder = passwordEncoder;
        this.storageService = storageService;
    }

    /**
     * Valida y registra un nuevo usuario.
     *
     * @return lista vacía si OK; lista de errores si falla la validación
     */
    @Transactional
    public List<String> register(RegisterRequest req) {
        List<String> errors = validate(req);
        if (!errors.isEmpty()) {
            return errors;
        }

        String hashedPassword = passwordEncoder.encode(req.contrasena());
        String fotoUrl = storageService.uploadProfilePhoto(req.fotoPerfil(), "profiles");

        if ("contratista".equalsIgnoreCase(req.rol())) {
            createContratista(req, hashedPassword, fotoUrl);
        } else {
            createCliente(req, hashedPassword, fotoUrl);
        }

        return List.of();
    }

    private List<String> validate(RegisterRequest req) {
        List<String> errors = new ArrayList<>();

        // Contraseña fuerte
        if (req.contrasena().length() < 8) {
            errors.add("La contraseña debe tener al menos 8 caracteres.");
        }
        if (!UPPER.matcher(req.contrasena()).find()) {
            errors.add("La contraseña debe tener al menos una letra mayúscula.");
        }
        if (!LOWER.matcher(req.contrasena()).find()) {
            errors.add("La contraseña debe tener al menos una letra minúscula.");
        }
        if (!DIGIT.matcher(req.contrasena()).find()) {
            errors.add("La contraseña debe tener al menos un dígito.");
        }
        if (!SYMBOL.matcher(req.contrasena()).find()) {
            errors.add("La contraseña debe tener al menos un carácter especial.");
        }

        // Confirmación
        if (!req.contrasena().equals(req.contrasenaConfirm())) {
            errors.add("Las contraseñas no coinciden.");
        }

        // Correo duplicado en las 3 tablas
        if (isEmailTaken(req.correo())) {
            errors.add("Ya existe una cuenta con ese correo electrónico.");
        }

        return errors;
    }

    /**
     * Verifica si un correo ya está registrado en cualquiera de las 3 tablas de usuario.
     */
    public boolean isEmailTaken(String correo) {
        return clienteRepo.existsByCorreo(correo)
                || contratistaRepo.existsByCorreo(correo)
                || adminRepo.existsByCorreo(correo);
    }

    private void createCliente(RegisterRequest req, String hashedPassword, String fotoUrl) {
        Cliente cliente = Cliente.builder()
                .nombre(req.nombre())
                .correo(req.correo())
                .contrasena(hashedPassword)
                .telefono(req.telefono())
                .ciudad(req.ciudad())
                .fotoPerfil(fotoUrl)
                .build();
        clienteRepo.save(cliente);
    }

    private void createContratista(RegisterRequest req, String hashedPassword, String fotoUrl) {
        Contratista contratista = Contratista.builder()
                .nombre(req.nombre())
                .correo(req.correo())
                .contrasena(hashedPassword)
                .telefono(req.telefono())
                .ciudad(req.ciudad())
                .ubicacionMapa(req.ubicacionMapa())
                .experiencia(req.experiencia())
                .descripcionPerfil(req.descripcionPerfil())
                .fotoPerfil(fotoUrl)
                .build();
        contratistaRepo.save(contratista);
    }
}
