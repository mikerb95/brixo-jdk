package com.brixo.service;

import com.brixo.dto.AdminUserRequest;
import com.brixo.entity.Admin;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import com.brixo.enums.UserRole;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

/**
 * Servicio de administración de usuarios (CRUD).
 *
 * Replica la lógica de Admin del PHP legacy:
 * - Listado de todos los usuarios (clientes + contratistas + admins)
 * - Creación, edición y eliminación
 * - Verificación de correo duplicado en las 3 tablas
 * - Protección contra auto-eliminación del admin
 */
@Service
public class AdminService {

    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;
    private final AdminRepository adminRepo;
    private final PasswordEncoder passwordEncoder;

    public AdminService(ClienteRepository clienteRepo,
                        ContratistaRepository contratistaRepo,
                        AdminRepository adminRepo,
                        PasswordEncoder passwordEncoder) {
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
        this.adminRepo = adminRepo;
        this.passwordEncoder = passwordEncoder;
    }

    /**
     * Vista unificada de todos los usuarios del sistema.
     */
    public record UserListItem(Long id, String nombre, String correo, UserRole rol, String ciudad) {}

    public List<UserListItem> getAllUsers() {
        List<UserListItem> users = new ArrayList<>();

        clienteRepo.findAll().forEach(c ->
                users.add(new UserListItem(c.getId(), c.getNombre(), c.getCorreo(), UserRole.CLIENTE, c.getCiudad()))
        );
        contratistaRepo.findAll().forEach(c ->
                users.add(new UserListItem(c.getId(), c.getNombre(), c.getCorreo(), UserRole.CONTRATISTA, c.getCiudad()))
        );
        adminRepo.findAll().forEach(a ->
                users.add(new UserListItem(a.getId(), a.getNombre(), a.getCorreo(), UserRole.ADMIN, null))
        );

        return users;
    }

    /**
     * Contadores para el dashboard admin.
     */
    public record DashboardStats(long clientes, long contratistas, long admins) {}

    public DashboardStats getStats() {
        return new DashboardStats(
                clienteRepo.count(),
                contratistaRepo.count(),
                adminRepo.count()
        );
    }

    /**
     * Crea un usuario de cualquier tipo.
     *
     * @return lista vacía si OK; lista de errores si falla
     */
    @Transactional
    public List<String> crearUsuario(AdminUserRequest req) {
        List<String> errors = new ArrayList<>();

        if (isEmailTaken(req.correo())) {
            errors.add("Ya existe una cuenta con ese correo.");
            return errors;
        }

        if (req.contrasena() == null || req.contrasena().isBlank()) {
            errors.add("La contraseña es obligatoria para nuevos usuarios.");
            return errors;
        }

        String hashed = passwordEncoder.encode(req.contrasena());

        switch (req.tipoUsuario().toLowerCase()) {
            case "cliente" -> clienteRepo.save(Cliente.builder()
                    .nombre(req.nombre()).correo(req.correo())
                    .contrasena(hashed).telefono(req.telefono()).ciudad(req.ciudad())
                    .build());
            case "contratista" -> contratistaRepo.save(Contratista.builder()
                    .nombre(req.nombre()).correo(req.correo())
                    .contrasena(hashed).telefono(req.telefono()).ciudad(req.ciudad())
                    .build());
            case "admin" -> adminRepo.save(Admin.builder()
                    .nombre(req.nombre()).correo(req.correo())
                    .contrasena(hashed).activo(true)
                    .build());
            default -> errors.add("Tipo de usuario inválido: " + req.tipoUsuario());
        }

        return errors;
    }

    /**
     * Actualiza un usuario existente.
     */
    @Transactional
    public List<String> actualizarUsuario(Long id, UserRole rol, AdminUserRequest req) {
        List<String> errors = new ArrayList<>();

        switch (rol) {
            case CLIENTE -> clienteRepo.findById(id).ifPresentOrElse(c -> {
                updateClienteFields(c, req);
                clienteRepo.save(c);
            }, () -> errors.add("Cliente no encontrado."));

            case CONTRATISTA -> contratistaRepo.findById(id).ifPresentOrElse(c -> {
                updateContratistaFields(c, req);
                contratistaRepo.save(c);
            }, () -> errors.add("Contratista no encontrado."));

            case ADMIN -> adminRepo.findById(id).ifPresentOrElse(a -> {
                a.setNombre(req.nombre());
                a.setCorreo(req.correo());
                if (req.contrasena() != null && !req.contrasena().isBlank()) {
                    a.setContrasena(passwordEncoder.encode(req.contrasena()));
                }
                adminRepo.save(a);
            }, () -> errors.add("Admin no encontrado."));
        }

        return errors;
    }

    /**
     * Elimina un usuario. No permite que un admin se elimine a sí mismo.
     */
    @Transactional
    public boolean eliminarUsuario(Long id, UserRole rol, Long currentAdminId) {
        if (rol == UserRole.ADMIN && id.equals(currentAdminId)) {
            return false; // Protección contra auto-eliminación
        }

        switch (rol) {
            case CLIENTE -> clienteRepo.deleteById(id);
            case CONTRATISTA -> contratistaRepo.deleteById(id);
            case ADMIN -> adminRepo.deleteById(id);
        }
        return true;
    }

    /**
     * Buscar un usuario por tipo e id.
     */
    public Optional<?> findUsuario(Long id, UserRole rol) {
        return switch (rol) {
            case CLIENTE -> clienteRepo.findById(id);
            case CONTRATISTA -> contratistaRepo.findById(id);
            case ADMIN -> adminRepo.findById(id);
        };
    }

    private boolean isEmailTaken(String correo) {
        return clienteRepo.existsByCorreo(correo)
                || contratistaRepo.existsByCorreo(correo)
                || adminRepo.existsByCorreo(correo);
    }

    private void updateClienteFields(Cliente c, AdminUserRequest req) {
        c.setNombre(req.nombre());
        c.setCorreo(req.correo());
        c.setTelefono(req.telefono());
        c.setCiudad(req.ciudad());
        if (req.contrasena() != null && !req.contrasena().isBlank()) {
            c.setContrasena(passwordEncoder.encode(req.contrasena()));
        }
    }

    private void updateContratistaFields(Contratista c, AdminUserRequest req) {
        c.setNombre(req.nombre());
        c.setCorreo(req.correo());
        c.setTelefono(req.telefono());
        c.setCiudad(req.ciudad());
        if (req.contrasena() != null && !req.contrasena().isBlank()) {
            c.setContrasena(passwordEncoder.encode(req.contrasena()));
        }
    }
}
