package com.brixo.repository;

import com.brixo.entity.Mensaje;
import com.brixo.enums.UserRole;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDateTime;
import java.util.List;

@Repository
public interface MensajeRepository extends JpaRepository<Mensaje, Long> {

    /**
     * Mensajes de un chat entre dos usuarios, ordenados cronológicamente.
     * Equivale a MensajeModel::getMensajesChat del sistema PHP.
     */
    @Query("""
            SELECT m FROM Mensaje m
            WHERE (m.remitenteId = :userId AND m.remitenteRol = :userRole
                   AND m.destinatarioId = :otherId AND m.destinatarioRol = :otherRole)
               OR (m.remitenteId = :otherId AND m.remitenteRol = :otherRole
                   AND m.destinatarioId = :userId AND m.destinatarioRol = :userRole)
            ORDER BY m.creadoEn ASC
            """)
    List<Mensaje> findChat(
            @Param("userId") Long userId,
            @Param("userRole") UserRole userRole,
            @Param("otherId") Long otherId,
            @Param("otherRole") UserRole otherRole
    );

    /**
     * Mensajes nuevos desde un timestamp (para AJAX polling).
     */
    @Query("""
            SELECT m FROM Mensaje m
            WHERE m.destinatarioId = :userId AND m.destinatarioRol = :userRole
              AND m.remitenteId = :otherId AND m.remitenteRol = :otherRole
              AND m.creadoEn > :since
            ORDER BY m.creadoEn ASC
            """)
    List<Mensaje> findNewMessages(
            @Param("userId") Long userId,
            @Param("userRole") UserRole userRole,
            @Param("otherId") Long otherId,
            @Param("otherRole") UserRole otherRole,
            @Param("since") LocalDateTime since
    );

    /**
     * Marcar mensajes como leídos.
     */
    @Modifying
    @Query("""
            UPDATE Mensaje m SET m.leido = true
            WHERE m.destinatarioId = :userId AND m.destinatarioRol = :userRole
              AND m.remitenteId = :otherId AND m.remitenteRol = :otherRole
              AND m.leido = false
            """)
    int marcarComoLeidos(
            @Param("userId") Long userId,
            @Param("userRole") UserRole userRole,
            @Param("otherId") Long otherId,
            @Param("otherRole") UserRole otherRole
    );

    /**
     * Obtener lista de conversaciones únicas del usuario.
     * Devuelve el último mensaje de cada conversación.
     */
    @Query(value = """
            SELECT m.* FROM MENSAJE m
            INNER JOIN (
                SELECT
                    LEAST(remitente_id, destinatario_id) AS u1,
                    GREATEST(remitente_id, destinatario_id) AS u2,
                    CONCAT(LEAST(remitente_rol, destinatario_rol), '-', GREATEST(remitente_rol, destinatario_rol)) AS roles,
                    MAX(id_mensaje) AS last_id
                FROM MENSAJE
                WHERE (remitente_id = :userId AND remitente_rol = :#{#userRole.name()})
                   OR (destinatario_id = :userId AND destinatario_rol = :#{#userRole.name()})
                GROUP BY u1, u2, roles
            ) latest ON m.id_mensaje = latest.last_id
            ORDER BY m.creado_en DESC
            """, nativeQuery = true)
    List<Mensaje> findConversaciones(
            @Param("userId") Long userId,
            @Param("userRole") UserRole userRole
    );
}
