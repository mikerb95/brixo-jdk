package com.brixo.repository;

import com.brixo.entity.Contrato;
import com.brixo.enums.EstadoContrato;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ContratoRepository extends JpaRepository<Contrato, Long> {

    List<Contrato> findByClienteId(Long clienteId);

    List<Contrato> findByContratistaId(Long contratistaId);

    List<Contrato> findByContratistaIdAndEstado(Long contratistaId, EstadoContrato estado);

    @Query("SELECT c FROM Contrato c JOIN FETCH c.cliente JOIN FETCH c.contratista WHERE c.id = :id")
    Contrato findByIdWithParties(@Param("id") Long id);
}
