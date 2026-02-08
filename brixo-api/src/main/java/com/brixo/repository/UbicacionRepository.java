package com.brixo.repository;

import com.brixo.entity.Ubicacion;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface UbicacionRepository extends JpaRepository<Ubicacion, Long> {

    List<Ubicacion> findByCiudad(String ciudad);

    List<Ubicacion> findByDepartamento(String departamento);
}
