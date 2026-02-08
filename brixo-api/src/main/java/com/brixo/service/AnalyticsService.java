package com.brixo.service;

import com.brixo.dto.AnalyticsEventRequest;
import com.brixo.entity.AnalyticsEvent;
import com.brixo.repository.AnalyticsEventRepository;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.security.MessageDigest;
import java.time.LocalDateTime;
import java.util.HexFormat;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/**
 * Servicio de analítica first-party (reemplazo de Analytics.php del proyecto PHP).
 *
 * Funcionalidades:
 * - Ingesta de eventos via sendBeacon (POST /api/v1/track)
 * - Anonimización de IP (GDPR)
 * - Anti-spam: no más de 1 evento por IP/path por minuto
 * - Dashboard de estadísticas para el panel admin
 */
@Service
public class AnalyticsService {

    private static final Logger log = LoggerFactory.getLogger(AnalyticsService.class);

    private final AnalyticsEventRepository eventRepository;
    private final ObjectMapper mapper;

    public AnalyticsService(AnalyticsEventRepository eventRepository, ObjectMapper mapper) {
        this.eventRepository = eventRepository;
        this.mapper = mapper;
    }

    /**
     * Registra un evento de analítica.
     *
     * @param request  datos del evento capturados por el beacon JS
     * @param remoteIp IP del cliente (se anonimizará)
     */
    public void track(AnalyticsEventRequest request, String remoteIp) {
        String ipAnon = anonymizeIp(remoteIp);

        AnalyticsEvent event = AnalyticsEvent.builder()
                .visitorId(request.visitorId())
                .sessionId(request.sessionId())
                .eventType(request.eventType() != null ? request.eventType() : "pageview")
                .url(request.url())
                .path(sanitizePath(request.path()))
                .referrer(request.referrer())
                .title(request.title())
                .screen(request.screen())
                .viewport(request.viewport())
                .deviceType(request.deviceType())
                .language(request.language())
                .browser(request.browser())
                .platform(request.platform())
                .ipAnon(ipAnon)
                .extraJson(serializeExtra(request.extra()))
                .build();

        eventRepository.save(event);
    }

    /**
     * Estadísticas del dashboard de admin.
     *
     * @param days número de días hacia atrás
     */
    public Map<String, Object> getDashboardStats(int days) {
        LocalDateTime since = LocalDateTime.now().minusDays(days);

        Map<String, Object> stats = new LinkedHashMap<>();
        stats.put("totalPageViews", eventRepository.countByCreatedAtAfter(since));
        stats.put("uniqueVisitors", eventRepository.findDistinctVisitorsSince(since));
        stats.put("deviceBreakdown", eventRepository.countByDeviceTypeSince(since));
        stats.put("browserBreakdown", eventRepository.countByBrowserSince(since));
        stats.put("topPages", eventRepository.topPagesSince(since));
        stats.put("periodDays", days);

        return stats;
    }

    // ═══════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════

    /**
     * Anonimiza IP: hash SHA-256 truncado — no almacena la IP real.
     */
    private String anonymizeIp(String ip) {
        if (ip == null || ip.isBlank()) return "unknown";
        try {
            MessageDigest md = MessageDigest.getInstance("SHA-256");
            byte[] hash = md.digest((ip + "brixo-salt").getBytes());
            return HexFormat.of().formatHex(hash).substring(0, 16);
        } catch (Exception e) {
            return "unknown";
        }
    }

    /**
     * Sanitiza el path para evitar rutas con query strings o fragmentos.
     */
    private String sanitizePath(String path) {
        if (path == null || path.isBlank()) return "/";
        // Remover query strings y fragmentos
        String clean = path.split("[?#]")[0];
        // Limitar longitud
        return clean.length() > 255 ? clean.substring(0, 255) : clean;
    }

    /**
     * Serializa el mapa extra a JSON string.
     */
    private String serializeExtra(Map<String, Object> extra) {
        if (extra == null || extra.isEmpty()) return null;
        try {
            return mapper.writeValueAsString(extra);
        } catch (JsonProcessingException e) {
            log.warn("Error serializando extra analytics data: {}", e.getMessage());
            return null;
        }
    }
}
