package com.brixo.dto;

import java.util.Map;

/**
 * Evento de analítica recibido desde el tracker JS (sendBeacon POST).
 */
public record AnalyticsEventRequest(
        String visitorId,
        String sessionId,
        String eventType,
        String url,
        String path,
        String referrer,
        String title,
        String screen,
        String viewport,
        String deviceType,
        String language,
        String browser,
        String platform,
        Map<String, Object> extra
) {}
