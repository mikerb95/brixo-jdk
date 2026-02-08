package com.brixo.entity;

import com.brixo.enums.EstadoContrato;
import jakarta.persistence.*;
import lombok.*;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

/**
 * Contrato entre un cliente y un contratista.
 * Tabla legacy: CONTRATO
 */
@Entity
@Table(name = "CONTRATO")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Contrato {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_contrato")
    private Long id;

    @Column(name = "fecha_inicio")
    private LocalDate fechaInicio;

    @Column(name = "fecha_fin")
    private LocalDate fechaFin;

    @Column(name = "costo_total", precision = 12, scale = 2)
    private BigDecimal costoTotal;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 20)
    @Builder.Default
    private EstadoContrato estado = EstadoContrato.PENDIENTE;

    // ── Relaciones ──

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_contratista", nullable = false)
    private Contratista contratista;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_cliente", nullable = false)
    private Cliente cliente;

    @OneToMany(mappedBy = "contrato", cascade = CascadeType.ALL, orphanRemoval = true)
    @Builder.Default
    private List<Resena> resenas = new ArrayList<>();
}
