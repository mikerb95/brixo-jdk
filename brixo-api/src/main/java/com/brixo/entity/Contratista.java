package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

/**
 * Cuenta de contratista (profesional que ofrece servicios).
 * Tabla legacy: CONTRATISTA
 */
@Entity
@Table(name = "CONTRATISTA")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Contratista {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_contratista")
    private Long id;

    @Column(nullable = false)
    private String nombre;

    @Column(nullable = false, unique = true)
    private String correo;

    @Column(nullable = false)
    private String contrasena;

    @Column(length = 50)
    private String telefono;

    @Column(length = 100)
    private String ciudad;

    @Column(name = "ubicacion_mapa")
    private String ubicacionMapa;

    @Column(name = "foto_perfil")
    private String fotoPerfil;

    @Column(columnDefinition = "TEXT")
    private String experiencia;

    @Column(columnDefinition = "TEXT")
    private String portafolio;

    @Column(name = "descripcion_perfil", columnDefinition = "TEXT")
    private String descripcionPerfil;

    @Column(columnDefinition = "TINYINT(1)")
    @Builder.Default
    private Boolean verificado = false;

    @CreationTimestamp
    @Column(name = "creado_en", updatable = false)
    private LocalDateTime creadoEn;

    // ── Relaciones ──

    @OneToMany(mappedBy = "contratista", cascade = CascadeType.ALL, orphanRemoval = true)
    @Builder.Default
    private List<Contrato> contratos = new ArrayList<>();

    @OneToMany(mappedBy = "contratista", cascade = CascadeType.ALL, orphanRemoval = true)
    @Builder.Default
    private List<Certificacion> certificaciones = new ArrayList<>();

    /** Relación M2M con SERVICIO — tiene columnas extra, se modela como entidad. */
    @OneToMany(mappedBy = "contratista", cascade = CascadeType.ALL, orphanRemoval = true)
    @Builder.Default
    private List<ContratistaServicio> servicios = new ArrayList<>();

    /** Relación M2M con UBICACION — sin columnas extra, se usa @ManyToMany directo. */
    @ManyToMany
    @JoinTable(
            name = "CONTRATISTA_UBICACION",
            joinColumns = @JoinColumn(name = "id_contratista"),
            inverseJoinColumns = @JoinColumn(name = "id_ubicacion")
    )
    @Builder.Default
    private Set<Ubicacion> ubicaciones = new HashSet<>();

    @OneToMany(mappedBy = "contratista")
    @Builder.Default
    private List<Solicitud> solicitudesAsignadas = new ArrayList<>();
}
