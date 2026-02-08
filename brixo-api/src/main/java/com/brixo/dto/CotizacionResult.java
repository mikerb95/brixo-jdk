package com.brixo.dto;

import java.util.List;

/**
 * Resultado estructurado de una cotización generada por el LLM.
 */
public record CotizacionResult(
        String servicioPrincipal,
        List<MaterialItem> materiales,
        List<PersonalItem> personal,
        String complejidad,
        double totalEstimado
) {
    public record MaterialItem(String nombre, int cantidad, double precioUnitario, double subtotal) {}
    public record PersonalItem(String rol, int cantidad, double costoHora, int horas, double subtotal) {}
}
