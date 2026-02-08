package com.brixo.repository;

import com.brixo.entity.Certificacion;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface CertificacionRepository extends JpaRepository<Certificacion, Long> {

    List<Certificacion> findByContratistaId(Long contratistaId);
}
