package com.brixo.repository;

import com.brixo.entity.ContratistaServicio;
import com.brixo.entity.ContratistaServicioId;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ContratistaServicioRepository extends JpaRepository<ContratistaServicio, ContratistaServicioId> {

    /** Servicios ofrecidos por un contratista, con servicio y categoría cargados. */
    @Query("""
            SELECT cs FROM ContratistaServicio cs
            JOIN FETCH cs.servicio s
            JOIN FETCH s.categoria
            WHERE cs.contratista.id = :contratistaId
            """)
    List<ContratistaServicio> findByContratistaIdWithDetails(@Param("contratistaId") Long contratistaId);

    /**
     * Todas las ofertas: join contratista → servicio → categoría → ubicación.
     * Equivale al getAllOffers() del modelo PHP con join de 5 tablas.
     */
    @Query("""
            SELECT cs FROM ContratistaServicio cs
            JOIN FETCH cs.contratista c
            JOIN FETCH cs.servicio s
            JOIN FETCH s.categoria
            """)
    List<ContratistaServicio> findAllWithFullDetails();
}
