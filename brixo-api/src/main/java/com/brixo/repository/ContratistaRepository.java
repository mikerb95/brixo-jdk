package com.brixo.repository;

import com.brixo.entity.Contratista;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface ContratistaRepository extends JpaRepository<Contratista, Long> {

    Optional<Contratista> findByCorreo(String correo);

    boolean existsByCorreo(String correo);

    /** Contratistas que tienen ubicación de mapa registrada (para el mapa interactivo). */
    @Query("SELECT c FROM Contratista c WHERE c.ubicacionMapa IS NOT NULL AND c.ubicacionMapa <> ''")
    List<Contratista> findAllWithLocation();

    /** Contratistas verificados. */
    List<Contratista> findByVerificadoTrue();
}
