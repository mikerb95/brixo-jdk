package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.math.BigDecimal;

/**
 * Relación M2M entre contratista y servicio, con columnas extra
 * (precio_personalizado, descripcion_personalizada).
 * Tabla legacy: CONTRATISTA_SERVICIO
 */
@Entity
@Table(name = "CONTRATISTA_SERVICIO")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class ContratistaServicio {

    @EmbeddedId
    private ContratistaServicioId id;

    @ManyToOne(fetch = FetchType.LAZY)
    @MapsId("contratistaId")
    @JoinColumn(name = "id_contratista")
    private Contratista contratista;

    @ManyToOne(fetch = FetchType.LAZY)
    @MapsId("servicioId")
    @JoinColumn(name = "id_servicio")
    private Servicio servicio;

    @Column(name = "precio_personalizado", precision = 12, scale = 2)
    private BigDecimal precioPersonalizado;

    @Column(name = "descripcion_personalizada", columnDefinition = "TEXT")
    private String descripcionPersonalizada;
}
