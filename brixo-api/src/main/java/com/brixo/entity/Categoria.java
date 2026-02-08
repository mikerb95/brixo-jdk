package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.util.ArrayList;
import java.util.List;

/**
 * Categoría de servicios (Hogar, Construcción, Plomería, etc.).
 * Tabla legacy: CATEGORIA
 */
@Entity
@Table(name = "CATEGORIA")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Categoria {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_categoria")
    private Long id;

    @Column(nullable = false, length = 100)
    private String nombre;

    @Column(columnDefinition = "TEXT")
    private String descripcion;

    @Column(name = "imagen_url")
    private String imagenUrl;

    // ── Relaciones ──

    @OneToMany(mappedBy = "categoria", cascade = CascadeType.ALL, orphanRemoval = true)
    @Builder.Default
    private List<Servicio> servicios = new ArrayList<>();
}
