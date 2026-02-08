package com.brixo.service;

import com.brixo.dto.SolicitudRequest;
import com.brixo.entity.Solicitud;
import com.brixo.enums.EstadoSolicitud;
import com.brixo.repository.SolicitudRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

/**
 * Servicio de solicitudes de servicio.
 *
 * Replica la lógica de Solicitud (CRUD) y el tablón de tareas para contratistas del PHP legacy.
 */
@Service
public class SolicitudService {

    private final SolicitudRepository solicitudRepo;

    public SolicitudService(SolicitudRepository solicitudRepo) {
        this.solicitudRepo = solicitudRepo;
    }

    /**
     * Crea una nueva solicitud para un cliente.
     */
    @Transactional
    public Solicitud crear(Long clienteId, SolicitudRequest req) {
        Solicitud sol = Solicitud.builder()
                .titulo(req.titulo())
                .descripcion(req.descripcion())
                .presupuesto(req.presupuesto())
                .ubicacion(req.ubicacion())
                .estado(EstadoSolicitud.ABIERTA)
                .build();

        // Se establece la relación FK manualmente (sin cargar la entidad Cliente)
        var clienteRef = new com.brixo.entity.Cliente();
        clienteRef.setId(clienteId);
        sol.setCliente(clienteRef);

        return solicitudRepo.save(sol);
    }

    /**
     * Actualiza una solicitud existente.
     * Verifica que pertenezca al cliente que la edita.
     */
    @Transactional
    public boolean actualizar(Long solicitudId, Long clienteId, SolicitudRequest req) {
        Optional<Solicitud> opt = solicitudRepo.findById(solicitudId);
        if (opt.isEmpty() || !opt.get().getCliente().getId().equals(clienteId)) {
            return false;
        }

        Solicitud sol = opt.get();
        sol.setTitulo(req.titulo());
        sol.setDescripcion(req.descripcion());
        sol.setPresupuesto(req.presupuesto());
        sol.setUbicacion(req.ubicacion());
        solicitudRepo.save(sol);
        return true;
    }

    /**
     * Elimina una solicitud (solo si pertenece al cliente).
     */
    @Transactional
    public boolean eliminar(Long solicitudId, Long clienteId) {
        Optional<Solicitud> opt = solicitudRepo.findById(solicitudId);
        if (opt.isEmpty() || !opt.get().getCliente().getId().equals(clienteId)) {
            return false;
        }
        solicitudRepo.delete(opt.get());
        return true;
    }

    public Optional<Solicitud> findById(Long id) {
        return solicitudRepo.findById(id);
    }

    /** Solicitudes de un cliente. */
    public List<Solicitud> findByCliente(Long clienteId) {
        return solicitudRepo.findByClienteIdOrderByCreadoEnDesc(clienteId);
    }

    /** Solicitudes asignadas a un contratista. */
    public List<Solicitud> findByContratista(Long contratistaId) {
        return solicitudRepo.findByContratistaIdOrderByCreadoEnDesc(contratistaId);
    }

    /** Tablón de tareas: solicitudes abiertas para contratistas. */
    public List<Solicitud> tablonAbierto() {
        return solicitudRepo.findByEstadoOrderByCreadoEnDesc(EstadoSolicitud.ABIERTA);
    }
}
