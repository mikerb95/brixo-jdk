package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.time.LocalDate;

/**
 * Reseña de un cliente sobre un contrato completado.
 * Tabla legacy: RESENA
 */
@Entity
@Table(name = "RESENA")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Resena {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_resena")
    private Long id;

    @Column(columnDefinition = "TEXT")
    private String comentario;

    private LocalDate fecha;

    /** Calificación 1–5. */
    @Column(nullable = false)
    private Integer calificacion;

    // ── Relaciones ──

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_contrato", nullable = false)
    private Contrato contrato;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_cliente", nullable = false)
    private Cliente cliente;
}
