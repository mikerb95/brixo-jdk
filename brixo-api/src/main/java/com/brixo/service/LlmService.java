package com.brixo.service;

import com.brixo.dto.CotizacionResult;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Service;
import org.springframework.web.reactive.function.client.WebClient;

import java.util.*;

/**
 * Servicio multi-proveedor de LLM (Anthropic Claude, OpenAI, Groq).
 *
 * Replica la lógica de app/Libraries/LlmService.php del sistema PHP legacy.
 * Genera cotizaciones estructuradas en JSON a partir de descripciones de trabajo.
 */
@Service
public class LlmService {

    private static final Logger log = LoggerFactory.getLogger(LlmService.class);

    private final ObjectMapper mapper;

    @Value("${llm.provider:groq}")
    private String provider;

    @Value("${llm.api-key:}")
    private String apiKey;

    @Value("${llm.model:}")
    private String model;

    /** Prompt del sistema — fuerza respuesta JSON estructurada. */
    private static final String SYSTEM_PROMPT = """
            Eres un experto estimador de costos para servicios del hogar en Colombia.
            Respondes EXCLUSIVAMENTE en formato JSON válido, sin markdown, sin texto adicional.
            
            El JSON debe tener esta estructura exacta:
            {
              "servicio_principal": "nombre del servicio",
              "materiales": [
                {"nombre": "material", "cantidad": 1, "precio_unitario": 10000, "subtotal": 10000}
              ],
              "personal": [
                {"rol": "profesional", "cantidad": 1, "costo_hora": 25000, "horas": 8, "subtotal": 200000}
              ],
              "complejidad": "BAJA|MEDIA|ALTA",
              "total_estimado": 250000
            }
            
            Todos los precios en pesos colombianos (COP). Sé realista con los precios del mercado colombiano.
            """;

    public LlmService(ObjectMapper mapper) {
        this.mapper = mapper;
    }

    /**
     * Genera una cotización a partir de una descripción de trabajo.
     *
     * @param descripcion descripción libre del trabajo
     * @return resultado estructurado de la cotización
     */
    public Optional<CotizacionResult> generarCotizacion(String descripcion) {
        if (apiKey.isBlank()) {
            log.warn("LLM API key no configurada. Retornando cotización demo.");
            return Optional.of(demoCotizacion(descripcion));
        }

        try {
            String response = callLlm(descripcion);
            return parseResponse(response);
        } catch (Exception e) {
            log.error("Error al llamar LLM: {}", e.getMessage());
            return Optional.empty();
        }
    }

    private String callLlm(String userMessage) {
        return switch (provider.toLowerCase()) {
            case "anthropic", "claude" -> callAnthropic(userMessage);
            case "openai", "gpt"       -> callOpenAI(userMessage);
            case "groq"                -> callGroq(userMessage);
            default -> throw new IllegalArgumentException("Proveedor LLM no soportado: " + provider);
        };
    }

    // ═══════════════════════════════════════════
    // Anthropic Claude
    // ═══════════════════════════════════════════
    private String callAnthropic(String userMessage) {
        String effectiveModel = model.isBlank() ? "claude-sonnet-4-20250514" : model;

        Map<String, Object> body = Map.of(
                "model", effectiveModel,
                "max_tokens", 2048,
                "system", SYSTEM_PROMPT,
                "messages", List.of(Map.of("role", "user", "content", userMessage))
        );

        String response = WebClient.create("https://api.anthropic.com")
                .post()
                .uri("/v1/messages")
                .header("x-api-key", apiKey)
                .header("anthropic-version", "2023-06-01")
                .contentType(MediaType.APPLICATION_JSON)
                .bodyValue(body)
                .retrieve()
                .bodyToMono(String.class)
                .block();

        return extractAnthropicContent(response);
    }

    // ═══════════════════════════════════════════
    // OpenAI
    // ═══════════════════════════════════════════
    private String callOpenAI(String userMessage) {
        String effectiveModel = model.isBlank() ? "gpt-4o-mini" : model;

        Map<String, Object> body = Map.of(
                "model", effectiveModel,
                "messages", List.of(
                        Map.of("role", "system", "content", SYSTEM_PROMPT),
                        Map.of("role", "user", "content", userMessage)
                ),
                "temperature", 0.3
        );

        String response = WebClient.create("https://api.openai.com")
                .post()
                .uri("/v1/chat/completions")
                .header("Authorization", "Bearer " + apiKey)
                .contentType(MediaType.APPLICATION_JSON)
                .bodyValue(body)
                .retrieve()
                .bodyToMono(String.class)
                .block();

        return extractOpenAIContent(response);
    }

    // ═══════════════════════════════════════════
    // Groq
    // ═══════════════════════════════════════════
    private String callGroq(String userMessage) {
        String effectiveModel = model.isBlank() ? "llama-3.3-70b-versatile" : model;

        Map<String, Object> body = Map.of(
                "model", effectiveModel,
                "messages", List.of(
                        Map.of("role", "system", "content", SYSTEM_PROMPT),
                        Map.of("role", "user", "content", userMessage)
                ),
                "temperature", 0.3
        );

        String response = WebClient.create("https://api.groq.com")
                .post()
                .uri("/openai/v1/chat/completions")
                .header("Authorization", "Bearer " + apiKey)
                .contentType(MediaType.APPLICATION_JSON)
                .bodyValue(body)
                .retrieve()
                .bodyToMono(String.class)
                .block();

        return extractOpenAIContent(response); // Groq usa el mismo formato que OpenAI
    }

    // ═══════════════════════════════════════════
    // Parseo de respuestas
    // ═══════════════════════════════════════════

    private String extractAnthropicContent(String response) {
        try {
            JsonNode root = mapper.readTree(response);
            return root.at("/content/0/text").asText();
        } catch (JsonProcessingException e) {
            log.error("Error parseando respuesta Anthropic: {}", e.getMessage());
            return response;
        }
    }

    private String extractOpenAIContent(String response) {
        try {
            JsonNode root = mapper.readTree(response);
            return root.at("/choices/0/message/content").asText();
        } catch (JsonProcessingException e) {
            log.error("Error parseando respuesta OpenAI/Groq: {}", e.getMessage());
            return response;
        }
    }

    private Optional<CotizacionResult> parseResponse(String raw) {
        try {
            // Limpiar posibles envolturas de markdown
            String clean = raw.replaceAll("```json\\s*", "").replaceAll("```\\s*", "").trim();

            JsonNode node = mapper.readTree(clean);

            List<CotizacionResult.MaterialItem> materiales = new ArrayList<>();
            for (JsonNode m : node.withArray("materiales")) {
                materiales.add(new CotizacionResult.MaterialItem(
                        m.get("nombre").asText(),
                        m.get("cantidad").asInt(),
                        m.get("precio_unitario").asDouble(),
                        m.get("subtotal").asDouble()
                ));
            }

            List<CotizacionResult.PersonalItem> personal = new ArrayList<>();
            for (JsonNode p : node.withArray("personal")) {
                personal.add(new CotizacionResult.PersonalItem(
                        p.get("rol").asText(),
                        p.get("cantidad").asInt(),
                        p.get("costo_hora").asDouble(),
                        p.get("horas").asInt(),
                        p.get("subtotal").asDouble()
                ));
            }

            return Optional.of(new CotizacionResult(
                    node.get("servicio_principal").asText(),
                    materiales,
                    personal,
                    node.get("complejidad").asText(),
                    node.get("total_estimado").asDouble()
            ));

        } catch (Exception e) {
            log.error("Error parseando respuesta LLM: {}", e.getMessage());
            return Optional.empty();
        }
    }

    /**
     * Cotización demo para cuando no hay API key configurada.
     */
    private CotizacionResult demoCotizacion(String descripcion) {
        return new CotizacionResult(
                "Servicio general del hogar",
                List.of(
                        new CotizacionResult.MaterialItem("Materiales básicos", 1, 50000, 50000),
                        new CotizacionResult.MaterialItem("Herramientas", 1, 30000, 30000)
                ),
                List.of(
                        new CotizacionResult.PersonalItem("Profesional", 1, 25000, 8, 200000)
                ),
                "MEDIA",
                280000
        );
    }
}
