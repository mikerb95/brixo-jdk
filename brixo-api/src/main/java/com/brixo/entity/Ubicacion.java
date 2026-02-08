package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.math.BigDecimal;

/**
 * Ubicación geográfica con coordenadas.
 * Tabla legacy: UBICACION
 */
@Entity
@Table(name = "UBICACION")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Ubicacion {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_ubicacion")
    private Long id;

    @Column(length = 100)
    private String ciudad;

    @Column(length = 100)
    private String departamento;

    private String direccion;

    @Column(precision = 10, scale = 8)
    private BigDecimal latitud;

    @Column(precision = 11, scale = 8)
    private BigDecimal longitud;
}
