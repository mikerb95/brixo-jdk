package com.brixo.service;

import com.brixo.dto.AnalyticsEventRequest;
import com.brixo.entity.AnalyticsEvent;
import com.brixo.repository.AnalyticsEventRepository;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.Spy;
import org.mockito.junit.jupiter.MockitoExtension;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.verify;
import static org.mockito.Mockito.when;

@ExtendWith(MockitoExtension.class)
@DisplayName("AnalyticsService")
class AnalyticsServiceTest {

    @Mock
    private AnalyticsEventRepository eventRepository;
    @Spy
    private ObjectMapper mapper = new ObjectMapper();

    @InjectMocks
    private AnalyticsService service;

    // ═══════════════════════════════════════════
    // track()
    // ═══════════════════════════════════════════

    @Nested
    @DisplayName("track()")
    class Track {

        @Test
        @DisplayName("Persiste evento con IP anonimizada")
        void persistsEventWithAnonymizedIp() {
            var request = new AnalyticsEventRequest(
                    "v-123", "s-456", "pageview",
                    "https://brixo.com.mx/mapa", "/mapa",
                    null, "Mapa", "1920x1080", "1440x900",
                    "desktop", "es-CO", "Chrome", "Linux",
                    null);

            service.track(request, "192.168.1.100");

            ArgumentCaptor<AnalyticsEvent> captor = ArgumentCaptor.forClass(AnalyticsEvent.class);
            verify(eventRepository).save(captor.capture());
            AnalyticsEvent saved = captor.getValue();

            assertThat(saved.getVisitorId()).isEqualTo("v-123");
            assertThat(saved.getSessionId()).isEqualTo("s-456");
            assertThat(saved.getEventType()).isEqualTo("pageview");
            assertThat(saved.getPath()).isEqualTo("/mapa");
            assertThat(saved.getIpAnon()).isNotEqualTo("192.168.1.100"); // anonimizada
            assertThat(saved.getIpAnon()).hasSize(16); // SHA-256 truncado a 16 hex chars
        }

        @Test
        @DisplayName("Usa 'pageview' como eventType por defecto")
        void defaultsToPageview() {
            var request = new AnalyticsEventRequest(
                    "v", "s", null, "url", "/", null, null,
                    null, null, null, null, null, null, null);

            service.track(request, "10.0.0.1");

            ArgumentCaptor<AnalyticsEvent> captor = ArgumentCaptor.forClass(AnalyticsEvent.class);
            verify(eventRepository).save(captor.capture());
            assertThat(captor.getValue().getEventType()).isEqualTo("pageview");
        }

        @Test
        @DisplayName("Anonimiza IP null como 'unknown'")
        void anonymizesNullIp() {
            var request = new AnalyticsEventRequest(
                    "v", "s", "click", "url", "/", null, null,
                    null, null, null, null, null, null, null);

            service.track(request, null);

            ArgumentCaptor<AnalyticsEvent> captor = ArgumentCaptor.forClass(AnalyticsEvent.class);
            verify(eventRepository).save(captor.capture());
            assertThat(captor.getValue().getIpAnon()).isEqualTo("unknown");
        }

        @Test
        @DisplayName("Sanitiza path quitando query params")
        void sanitizesPath() {
            var request = new AnalyticsEventRequest(
                    "v", "s", "pageview", "url", "/mapa?lat=4.6&lon=-74",
                    null, null, null, null, null, null, null, null, null);

            service.track(request, "1.2.3.4");

            ArgumentCaptor<AnalyticsEvent> captor = ArgumentCaptor.forClass(AnalyticsEvent.class);
            verify(eventRepository).save(captor.capture());
            assertThat(captor.getValue().getPath()).isEqualTo("/mapa");
        }

        @Test
        @DisplayName("Serializa datos extra como JSON")
        void serializesExtra() {
            var request = new AnalyticsEventRequest(
                    "v", "s", "click", "url", "/",
                    null, null, null, null, null, null, null, null,
                    Map.of("button", "cta-signup"));

            service.track(request, "1.1.1.1");

            ArgumentCaptor<AnalyticsEvent> captor = ArgumentCaptor.forClass(AnalyticsEvent.class);
            verify(eventRepository).save(captor.capture());
            assertThat(captor.getValue().getExtraJson()).contains("cta-signup");
        }
    }

    // ═══════════════════════════════════════════
    // getDashboardStats()
    // ═══════════════════════════════════════════

    @Test
    @DisplayName("getDashboardStats() retorna estadísticas del período")
    void getDashboardStats_returnsAllMetrics() {
        when(eventRepository.countByCreatedAtAfter(any(LocalDateTime.class))).thenReturn(1200L);
        when(eventRepository.findDistinctVisitorsSince(any())).thenReturn(List.of("ip1", "ip2", "ip3"));
        when(eventRepository.countByDeviceTypeSince(any())).thenReturn(List.of());
        when(eventRepository.countByBrowserSince(any())).thenReturn(List.of());
        when(eventRepository.topPagesSince(any())).thenReturn(List.of());

        Map<String, Object> stats = service.getDashboardStats(30);

        assertThat(stats).containsEntry("totalPageViews", 1200L);
        assertThat(stats).containsEntry("uniqueVisitors", List.of("ip1", "ip2", "ip3"));
        assertThat(stats).containsEntry("periodDays", 30);
        assertThat(stats).containsKeys("deviceBreakdown", "browserBreakdown", "topPages");
    }
}
