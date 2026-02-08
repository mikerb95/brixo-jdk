package com.brixo.entity;

import com.brixo.enums.EstadoSolicitud;
import jakarta.persistence.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;

import java.math.BigDecimal;
import java.time.LocalDateTime;

/**
 * Solicitud de servicio publicada por un cliente.
 * Tabla creada dinámicamente en el sistema legacy (Setup::solicitudes).
 */
@Entity
@Table(name = "SOLICITUD")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Solicitud {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_solicitud")
    private Long id;

    @Column(nullable = false)
    private String titulo;

    @Column(columnDefinition = "TEXT")
    private String descripcion;

    @Column(precision = 12, scale = 2)
    private BigDecimal presupuesto;

    private String ubicacion;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 20)
    @Builder.Default
    private EstadoSolicitud estado = EstadoSolicitud.ABIERTA;

    @CreationTimestamp
    @Column(name = "creado_en", updatable = false)
    private LocalDateTime creadoEn;

    // ── Relaciones ──

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_cliente", nullable = false)
    private Cliente cliente;

    /** Contratista asignado (nullable — una solicitud puede estar abierta sin asignar). */
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_contratista")
    private Contratista contratista;
}
