package com.brixo.config;

import com.brixo.entity.Admin;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import com.brixo.enums.UserRole;
import com.brixo.repository.AdminRepository;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.List;

/**
 * Servicio de autenticación que busca en las 3 tablas de usuario
 * (CLIENTE → CONTRATISTA → ADMIN) de forma secuencial,
 * replicando el comportamiento de Auth::login del sistema PHP legacy.
 */
@Service
public class BrixoUserDetailsService implements UserDetailsService {

    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;
    private final AdminRepository adminRepo;

    public BrixoUserDetailsService(ClienteRepository clienteRepo,
                                   ContratistaRepository contratistaRepo,
                                   AdminRepository adminRepo) {
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
        this.adminRepo = adminRepo;
    }

    @Override
    public UserDetails loadUserByUsername(String correo) throws UsernameNotFoundException {
        // 1. Buscar en CLIENTE
        var cliente = clienteRepo.findByCorreo(correo);
        if (cliente.isPresent()) {
            return toUserDetails(cliente.get());
        }

        // 2. Buscar en CONTRATISTA
        var contratista = contratistaRepo.findByCorreo(correo);
        if (contratista.isPresent()) {
            return toUserDetails(contratista.get());
        }

        // 3. Buscar en ADMIN
        var admin = adminRepo.findByCorreo(correo);
        if (admin.isPresent()) {
            return toUserDetails(admin.get());
        }

        throw new UsernameNotFoundException("No se encontró usuario con correo: " + correo);
    }

    private UserDetails toUserDetails(Cliente c) {
        return new BrixoUserPrincipal(
                c.getId(), c.getNombre(), c.getCorreo(), c.getContrasena(),
                c.getFotoPerfil(), UserRole.CLIENTE,
                List.of(new SimpleGrantedAuthority("ROLE_CLIENTE"))
        );
    }

    private UserDetails toUserDetails(Contratista c) {
        return new BrixoUserPrincipal(
                c.getId(), c.getNombre(), c.getCorreo(), c.getContrasena(),
                c.getFotoPerfil(), UserRole.CONTRATISTA,
                List.of(new SimpleGrantedAuthority("ROLE_CONTRATISTA"))
        );
    }

    private UserDetails toUserDetails(Admin a) {
        return new BrixoUserPrincipal(
                a.getId(), a.getNombre(), a.getCorreo(), a.getContrasena(),
                a.getFotoPerfil(), UserRole.ADMIN,
                List.of(new SimpleGrantedAuthority("ROLE_ADMIN"))
        );
    }

    /**
     * Principal personalizado que además del username/password
     * transporta el id, nombre, rol y foto — datos que el sistema original
     * almacenaba en la sesión PHP: {id, nombre, correo, rol, foto_perfil}.
     */
    public record BrixoUserPrincipal(
            Long id,
            String nombre,
            String correo,
            String contrasena,
            String fotoPerfil,
            UserRole rol,
            List<GrantedAuthority> authorities
    ) implements UserDetails {

        @Override public String getUsername()  { return correo; }
        @Override public String getPassword()  { return contrasena; }

        @Override
        public java.util.Collection<? extends GrantedAuthority> getAuthorities() {
            return authorities;
        }

        @Override public boolean isAccountNonExpired()     { return true; }
        @Override public boolean isAccountNonLocked()      { return true; }
        @Override public boolean isCredentialsNonExpired()  { return true; }
        @Override public boolean isEnabled()                { return true; }
    }
}
