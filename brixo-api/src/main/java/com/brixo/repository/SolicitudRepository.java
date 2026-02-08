package com.brixo.repository;

import com.brixo.entity.Solicitud;
import com.brixo.enums.EstadoSolicitud;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface SolicitudRepository extends JpaRepository<Solicitud, Long> {

    List<Solicitud> findByClienteIdOrderByCreadoEnDesc(Long clienteId);

    List<Solicitud> findByContratistaIdOrderByCreadoEnDesc(Long contratistaId);

    /** Solicitudes abiertas (tablón de tareas para contratistas). */
    List<Solicitud> findByEstadoOrderByCreadoEnDesc(EstadoSolicitud estado);

    @Query("SELECT s FROM Solicitud s JOIN FETCH s.cliente WHERE s.id = :id")
    Solicitud findByIdWithCliente(@Param("id") Long id);
}
