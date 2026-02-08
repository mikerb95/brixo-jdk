package com.brixo.service;

import com.brixo.dto.PerfilUpdateRequest;
import com.brixo.entity.Certificacion;
import com.brixo.entity.Cliente;
import com.brixo.entity.Contratista;
import com.brixo.enums.UserRole;
import com.brixo.repository.*;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

/**
 * Servicio de perfiles de usuario.
 *
 * Replica la lógica de Panel::editarPerfil/actualizarPerfil y Perfil::ver del PHP legacy.
 */
@Service
public class PerfilService {

    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;
    private final ContratistaServicioRepository csRepo;
    private final CertificacionRepository certRepo;
    private final ResenaRepository resenaRepo;
    private final StorageService storageService;

    public PerfilService(ClienteRepository clienteRepo,
                         ContratistaRepository contratistaRepo,
                         ContratistaServicioRepository csRepo,
                         CertificacionRepository certRepo,
                         ResenaRepository resenaRepo,
                         StorageService storageService) {
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
        this.csRepo = csRepo;
        this.certRepo = certRepo;
        this.resenaRepo = resenaRepo;
        this.storageService = storageService;
    }

    public Optional<Cliente> findCliente(Long id) {
        return clienteRepo.findById(id);
    }

    public Optional<Contratista> findContratista(Long id) {
        return contratistaRepo.findById(id);
    }

    /**
     * Obtiene el perfil público de un contratista con todos sus datos relacionados.
     */
    public Optional<ContratistaProfile> getPublicProfile(Long contratistaId) {
        return contratistaRepo.findById(contratistaId)
                .map(c -> new ContratistaProfile(
                        c,
                        csRepo.findByContratistaIdWithDetails(contratistaId),
                        certRepo.findByContratistaId(contratistaId),
                        resenaRepo.findByContratistaId(contratistaId),
                        resenaRepo.getAverageRatingByContratistaId(contratistaId)
                ));
    }

    /**
     * Actualiza el perfil de un cliente.
     */
    @Transactional
    public void updateClienteProfile(Long id, PerfilUpdateRequest req) {
        clienteRepo.findById(id).ifPresent(c -> {
            if (req.nombre() != null) c.setNombre(req.nombre());
            if (req.telefono() != null) c.setTelefono(req.telefono());
            if (req.ciudad() != null) c.setCiudad(req.ciudad());

            String fotoUrl = storageService.uploadProfilePhoto(req.fotoPerfil(), "profiles");
            if (fotoUrl != null) c.setFotoPerfil(fotoUrl);

            clienteRepo.save(c);
        });
    }

    /**
     * Actualiza el perfil de un contratista.
     */
    @Transactional
    public void updateContratistaProfile(Long id, PerfilUpdateRequest req) {
        contratistaRepo.findById(id).ifPresent(c -> {
            if (req.nombre() != null) c.setNombre(req.nombre());
            if (req.telefono() != null) c.setTelefono(req.telefono());
            if (req.ciudad() != null) c.setCiudad(req.ciudad());
            if (req.ubicacionMapa() != null) c.setUbicacionMapa(req.ubicacionMapa());
            if (req.experiencia() != null) c.setExperiencia(req.experiencia());
            if (req.descripcionPerfil() != null) c.setDescripcionPerfil(req.descripcionPerfil());
            if (req.portafolio() != null) c.setPortafolio(req.portafolio());

            String fotoUrl = storageService.uploadProfilePhoto(req.fotoPerfil(), "profiles");
            if (fotoUrl != null) c.setFotoPerfil(fotoUrl);

            contratistaRepo.save(c);
        });
    }

    /**
     * Actualiza el perfil según el rol.
     */
    @Transactional
    public void updateProfile(Long id, UserRole rol, PerfilUpdateRequest req) {
        switch (rol) {
            case CLIENTE -> updateClienteProfile(id, req);
            case CONTRATISTA -> updateContratistaProfile(id, req);
            default -> throw new IllegalArgumentException("Rol no soportado para edición: " + rol);
        }
    }

    /**
     * Registro con todos los datos del perfil público de un contratista.
     */
    public record ContratistaProfile(
            Contratista contratista,
            List<com.brixo.entity.ContratistaServicio> servicios,
            List<Certificacion> certificaciones,
            List<com.brixo.entity.Resena> resenas,
            Double calificacionPromedio
    ) {}
}
