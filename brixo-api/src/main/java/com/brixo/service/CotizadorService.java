package com.brixo.service;

import com.brixo.dto.CotizacionResult;
import com.brixo.entity.CotizacionConfirmada;
import com.brixo.enums.Complejidad;
import com.brixo.enums.EstadoCotizacion;
import com.brixo.repository.CotizacionConfirmadaRepository;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.Optional;

/**
 * Servicio del cotizador inteligente (IA).
 *
 * Replica la lógica de Cotizador del PHP legacy:
 * - Recibe descripción → llama al LLM → retorna cotización
 * - Confirma cotización → persiste en BD
 * - Pre-rellena solicitud desde cotización confirmada
 */
@Service
public class CotizadorService {

    private static final Logger log = LoggerFactory.getLogger(CotizadorService.class);

    private final LlmService llmService;
    private final CotizacionConfirmadaRepository cotizacionRepo;
    private final ObjectMapper mapper;

    public CotizadorService(LlmService llmService,
                            CotizacionConfirmadaRepository cotizacionRepo,
                            ObjectMapper mapper) {
        this.llmService = llmService;
        this.cotizacionRepo = cotizacionRepo;
        this.mapper = mapper;
    }

    /**
     * Genera una cotización a partir de la descripción del trabajo.
     */
    public Optional<CotizacionResult> generar(String descripcion) {
        return llmService.generarCotizacion(descripcion);
    }

    /**
     * Confirma y persiste una cotización en la base de datos.
     */
    @Transactional
    public CotizacionConfirmada confirmar(Long clienteId, CotizacionResult result, String descripcion) {
        CotizacionConfirmada cotizacion = CotizacionConfirmada.builder()
                .clienteId(clienteId)
                .descripcion(descripcion)
                .servicioPrincipal(result.servicioPrincipal())
                .materialesJson(toJson(result.materiales()))
                .personalJson(toJson(result.personal()))
                .complejidad(parseComplejidad(result.complejidad()))
                .estado(EstadoCotizacion.CONFIRMADA)
                .confirmadoEn(LocalDateTime.now())
                .build();

        return cotizacionRepo.save(cotizacion);
    }

    private Complejidad parseComplejidad(String value) {
        try {
            return Complejidad.valueOf(value.toUpperCase());
        } catch (IllegalArgumentException e) {
            return Complejidad.MEDIA;
        }
    }

    private String toJson(Object obj) {
        try {
            return mapper.writeValueAsString(obj);
        } catch (JsonProcessingException e) {
            log.error("Error serializando a JSON: {}", e.getMessage());
            return "[]";
        }
    }
}
