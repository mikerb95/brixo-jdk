package com.brixo.service;

import com.brixo.entity.Categoria;
import com.brixo.entity.ContratistaServicio;
import com.brixo.entity.Servicio;
import com.brixo.repository.CategoriaRepository;
import com.brixo.repository.ContratistaServicioRepository;
import com.brixo.repository.ServicioRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

/**
 * Servicio de especialidades (categorías y servicios).
 *
 * Replica la lógica de Especialidades del PHP legacy.
 */
@Service
public class EspecialidadesService {

    private final CategoriaRepository categoriaRepo;
    private final ServicioRepository servicioRepo;
    private final ContratistaServicioRepository csRepo;

    public EspecialidadesService(CategoriaRepository categoriaRepo,
                                ServicioRepository servicioRepo,
                                ContratistaServicioRepository csRepo) {
        this.categoriaRepo = categoriaRepo;
        this.servicioRepo = servicioRepo;
        this.csRepo = csRepo;
    }

    /**
     * Todas las categorías con sus servicios.
     */
    public List<Categoria> getAllCategorias() {
        return categoriaRepo.findAll();
    }

    /**
     * Una categoría con sus servicios.
     */
    public Optional<Categoria> getCategoriaById(Long id) {
        return categoriaRepo.findById(id);
    }

    /**
     * Servicios de una categoría.
     */
    public List<Servicio> getServiciosByCategoria(Long categoriaId) {
        return servicioRepo.findByCategoriaId(categoriaId);
    }

    /**
     * Todas las ofertas de contratistas con detalles completos.
     */
    public List<ContratistaServicio> getAllOffers() {
        return csRepo.findAllWithFullDetails();
    }
}
