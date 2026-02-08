package com.brixo.repository;

import com.brixo.entity.Servicio;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ServicioRepository extends JpaRepository<Servicio, Long> {

    List<Servicio> findByCategoriaId(Long categoriaId);

    /** Servicio con su categoría cargada (evita N+1). */
    @Query("SELECT s FROM Servicio s JOIN FETCH s.categoria WHERE s.id = :id")
    Servicio findByIdWithCategoria(@Param("id") Long id);
}
