package com.brixo.repository;

import com.brixo.entity.CotizacionConfirmada;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface CotizacionConfirmadaRepository extends JpaRepository<CotizacionConfirmada, Long> {

    List<CotizacionConfirmada> findByClienteIdOrderByCreadoEnDesc(Long clienteId);
}
