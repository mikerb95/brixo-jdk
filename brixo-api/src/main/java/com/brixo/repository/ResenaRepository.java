package com.brixo.repository;

import com.brixo.entity.Resena;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ResenaRepository extends JpaRepository<Resena, Long> {

    /**
     * Reseñas de un contratista (a través de sus contratos), con datos del cliente.
     * Equivale a ResenaModel::getByContratista del sistema PHP.
     */
    @Query("""
            SELECT r FROM Resena r
            JOIN FETCH r.contrato c
            JOIN FETCH r.cliente
            WHERE c.contratista.id = :contratistaId
            ORDER BY r.fecha DESC
            """)
    List<Resena> findByContratistaId(@Param("contratistaId") Long contratistaId);

    /** Promedio de calificación de un contratista. */
    @Query("""
            SELECT COALESCE(AVG(r.calificacion), 0)
            FROM Resena r
            JOIN r.contrato c
            WHERE c.contratista.id = :contratistaId
            """)
    Double getAverageRatingByContratistaId(@Param("contratistaId") Long contratistaId);
}
