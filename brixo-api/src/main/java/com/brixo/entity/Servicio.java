package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.math.BigDecimal;

/**
 * Servicio específico dentro de una categoría.
 * Tabla legacy: SERVICIO
 */
@Entity
@Table(name = "SERVICIO")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Servicio {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_servicio")
    private Long id;

    @Column(nullable = false)
    private String nombre;

    @Column(columnDefinition = "TEXT")
    private String descripcion;

    @Column(name = "precio_estimado", precision = 12, scale = 2)
    private BigDecimal precioEstimado;

    @Column(name = "imagen_url")
    private String imagenUrl;

    // ── Relaciones ──

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_categoria")
    private Categoria categoria;
}
