package com.brixo.service;

import com.brixo.dto.MensajeRequest;
import com.brixo.entity.Mensaje;
import com.brixo.enums.UserRole;
import com.brixo.repository.ClienteRepository;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.MensajeRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;

/**
 * Servicio de mensajería interna.
 *
 * Replica la lógica de Mensajes del PHP legacy:
 * - Lista de conversaciones
 * - Chat entre dos usuarios
 * - Envío de mensajes
 * - Marcar como leídos
 * - Polling de mensajes nuevos (AJAX)
 */
@Service
public class MensajeService {

    private final MensajeRepository mensajeRepo;
    private final ClienteRepository clienteRepo;
    private final ContratistaRepository contratistaRepo;

    public MensajeService(MensajeRepository mensajeRepo,
                          ClienteRepository clienteRepo,
                          ContratistaRepository contratistaRepo) {
        this.mensajeRepo = mensajeRepo;
        this.clienteRepo = clienteRepo;
        this.contratistaRepo = contratistaRepo;
    }

    /**
     * Lista de conversaciones del usuario autenticado.
     */
    public List<Mensaje> getConversaciones(Long userId, UserRole userRole) {
        return mensajeRepo.findConversaciones(userId, userRole);
    }

    /**
     * Mensajes de un chat entre dos usuarios.
     */
    @Transactional
    public List<Mensaje> getChat(Long userId, UserRole userRole,
                                 Long otherId, UserRole otherRole) {
        // Marcar como leídos al abrir el chat
        mensajeRepo.marcarComoLeidos(userId, userRole, otherId, otherRole);
        return mensajeRepo.findChat(userId, userRole, otherId, otherRole);
    }

    /**
     * Envía un mensaje.
     */
    @Transactional
    public Mensaje enviar(Long remitenteId, UserRole remitenteRol, MensajeRequest req) {
        UserRole destRol = UserRole.valueOf(req.destinatarioRol().toUpperCase());

        Mensaje mensaje = Mensaje.builder()
                .remitenteId(remitenteId)
                .remitenteRol(remitenteRol)
                .destinatarioId(req.destinatarioId())
                .destinatarioRol(destRol)
                .contenido(req.contenido())
                .leido(false)
                .build();

        return mensajeRepo.save(mensaje);
    }

    /**
     * Mensajes nuevos desde un timestamp (para AJAX polling).
     */
    public List<Mensaje> getNuevos(Long userId, UserRole userRole,
                                   Long otherId, UserRole otherRole,
                                   LocalDateTime since) {
        return mensajeRepo.findNewMessages(userId, userRole, otherId, otherRole, since);
    }

    /**
     * Obtiene el nombre de un usuario por su id y rol.
     * Usado para mostrar el nombre del interlocutor en la lista de conversaciones.
     */
    public String getNombreUsuario(Long id, UserRole rol) {
        return switch (rol) {
            case CLIENTE -> clienteRepo.findById(id)
                    .map(c -> c.getNombre())
                    .orElse("Cliente #" + id);
            case CONTRATISTA -> contratistaRepo.findById(id)
                    .map(c -> c.getNombre())
                    .orElse("Contratista #" + id);
            default -> "Usuario #" + id;
        };
    }
}
