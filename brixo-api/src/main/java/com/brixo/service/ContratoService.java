package com.brixo.service;

import com.brixo.entity.Contrato;
import com.brixo.enums.EstadoContrato;
import com.brixo.repository.ContratoRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

/**
 * Servicio de contratos entre clientes y contratistas.
 *
 * Replica la lógica distribuida en Panel y Solicitudes del PHP legacy.
 */
@Service
public class ContratoService {

    private final ContratoRepository contratoRepo;

    public ContratoService(ContratoRepository contratoRepo) {
        this.contratoRepo = contratoRepo;
    }

    public Optional<Contrato> findById(Long id) {
        return contratoRepo.findById(id);
    }

    public List<Contrato> findByCliente(Long clienteId) {
        return contratoRepo.findByClienteId(clienteId);
    }

    public List<Contrato> findByContratista(Long contratistaId) {
        return contratoRepo.findByContratistaId(contratistaId);
    }

    public List<Contrato> findByContratistaAndEstado(Long contratistaId, EstadoContrato estado) {
        return contratoRepo.findByContratistaIdAndEstado(contratistaId, estado);
    }

    public Contrato findByIdWithParties(Long id) {
        return contratoRepo.findByIdWithParties(id);
    }
}
