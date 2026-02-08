package com.brixo.entity;

import com.brixo.enums.Complejidad;
import com.brixo.enums.EstadoCotizacion;
import jakarta.persistence.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.JdbcTypeCode;
import org.hibernate.type.SqlTypes;

import java.time.LocalDateTime;

/**
 * Cotización generada por IA y confirmada por el cliente.
 * Tabla creada dinámicamente en el sistema legacy (Cotizador::ensureCotizacionesTable).
 */
@Entity
@Table(name = "COTIZACION_CONFIRMADA")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class CotizacionConfirmada {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "id_cliente")
    private Long clienteId;

    @Column(columnDefinition = "TEXT")
    private String descripcion;

    @Column(name = "servicio_principal")
    private String servicioPrincipal;

    /** JSON array de materiales estimados por el LLM. */
    @Column(name = "materiales_json", columnDefinition = "JSON")
    @JdbcTypeCode(SqlTypes.JSON)
    private String materialesJson;

    /** JSON array de personal estimado por el LLM. */
    @Column(name = "personal_json", columnDefinition = "JSON")
    @JdbcTypeCode(SqlTypes.JSON)
    private String personalJson;

    @Enumerated(EnumType.STRING)
    @Column(length = 10)
    private Complejidad complejidad;

    @Enumerated(EnumType.STRING)
    @Column(length = 15)
    @Builder.Default
    private EstadoCotizacion estado = EstadoCotizacion.PENDIENTE;

    @CreationTimestamp
    @Column(name = "creado_en", updatable = false)
    private LocalDateTime creadoEn;

    @Column(name = "confirmado_en")
    private LocalDateTime confirmadoEn;
}
