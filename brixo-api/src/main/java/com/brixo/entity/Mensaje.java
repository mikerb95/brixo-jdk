package com.brixo.entity;

import com.brixo.enums.UserRole;
import jakarta.persistence.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Mensaje entre usuarios (cliente ↔ contratista).
 * Usa rol polimórfico (remitente_rol / destinatario_rol) para identificar la tabla de origen.
 * Tabla creada dinámicamente en el sistema legacy (Setup::mensajes).
 */
@Entity
@Table(name = "MENSAJE")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class Mensaje {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_mensaje")
    private Long id;

    @Column(name = "remitente_id", nullable = false)
    private Long remitenteId;

    @Enumerated(EnumType.STRING)
    @Column(name = "remitente_rol", nullable = false, length = 20)
    private UserRole remitenteRol;

    @Column(name = "destinatario_id", nullable = false)
    private Long destinatarioId;

    @Enumerated(EnumType.STRING)
    @Column(name = "destinatario_rol", nullable = false, length = 20)
    private UserRole destinatarioRol;

    @Column(nullable = false, columnDefinition = "TEXT")
    private String contenido;

    @Column(columnDefinition = "TINYINT(1)")
    @Builder.Default
    private Boolean leido = false;

    @CreationTimestamp
    @Column(name = "creado_en", updatable = false)
    private LocalDateTime creadoEn;
}
