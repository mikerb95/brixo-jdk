package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.time.LocalDate;

/**
 * Certificación profesional de un contratista.
 * Tabla legacy: CERTIFICACION
 */
@Entity
@Table(name = "CERTIFICACION")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Certificacion {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_certificado")
    private Long id;

    @Column(nullable = false)
    private String nombre;

    @Column(name = "entidad_emisora")
    private String entidadEmisora;

    @Column(name = "fecha_obtenida")
    private LocalDate fechaObtenida;

    // ── Relaciones ──

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "id_contratista", nullable = false)
    private Contratista contratista;
}
