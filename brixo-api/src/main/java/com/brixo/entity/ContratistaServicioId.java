package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;

import java.io.Serializable;

/**
 * Clave compuesta para la tabla intermedia CONTRATISTA_SERVICIO.
 */
@Embeddable
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@EqualsAndHashCode
public class ContratistaServicioId implements Serializable {

    @Column(name = "id_contratista")
    private Long contratistaId;

    @Column(name = "id_servicio")
    private Long servicioId;
}
