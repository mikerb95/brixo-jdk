package com.brixo.entity;

import jakarta.persistence.*;
import lombok.*;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Evento de analítica first-party (privacidad, GDPR-compliant).
 * Tabla legacy: analytics_events
 */
@Entity
@Table(name = "analytics_events")
@Getter @Setter
@NoArgsConstructor @AllArgsConstructor
@Builder
public class AnalyticsEvent {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "visitor_id", length = 36)
    private String visitorId;

    @Column(name = "session_id", length = 36)
    private String sessionId;

    @Column(name = "event_type", nullable = false, length = 50)
    private String eventType;

    private String url;

    private String path;

    private String referrer;

    private String title;

    @Column(length = 20)
    private String screen;

    @Column(length = 20)
    private String viewport;

    @Column(name = "device_type", length = 20)
    private String deviceType;

    @Column(length = 10)
    private String language;

    @Column(length = 50)
    private String browser;

    @Column(length = 50)
    private String platform;

    /** IP anonimizada (último octeto reemplazado con 0). */
    @Column(name = "ip_anon", length = 45)
    private String ipAnon;

    /** Datos extra en formato JSON. */
    @Column(name = "extra_json", columnDefinition = "JSON")
    private String extraJson;

    @CreationTimestamp
    @Column(name = "created_at", updatable = false)
    private LocalDateTime createdAt;
}
