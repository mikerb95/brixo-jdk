package com.brixo.controller;

import com.brixo.config.BrixoUserDetailsService.BrixoUserPrincipal;
import com.brixo.dto.AnalyticsEventRequest;
import com.brixo.service.AnalyticsService;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

/**
 * Controlador de analítica first-party.
 *
 * Rutas:
 *   POST /api/v1/track         — Ingesta de eventos (sendBeacon)
 *   GET  /analytics/dashboard  — Dashboard de analíticas (admin)
 */
@Controller
public class AnalyticsController {

    private final AnalyticsService analyticsService;

    public AnalyticsController(AnalyticsService analyticsService) {
        this.analyticsService = analyticsService;
    }

    /** POST /api/v1/track — Fire-and-forget event tracking. */
    @PostMapping("/api/v1/track")
    @ResponseBody
    public ResponseEntity<Void> track(@RequestBody AnalyticsEventRequest event,
                                      HttpServletRequest request) {
        try {
            String remoteIp = request.getRemoteAddr();
            analyticsService.track(event, remoteIp);
        } catch (Exception e) {
            // Never break UX for analytics errors
        }
        return ResponseEntity.noContent().build();
    }

    /** GET /analytics/dashboard — Admin analytics dashboard. */
    @GetMapping("/analytics/dashboard")
    public String dashboard(@AuthenticationPrincipal BrixoUserPrincipal user,
                            @RequestParam(defaultValue = "30") int days,
                            Model model) {
        model.addAttribute("user", user);
        model.addAttribute("days", Math.max(1, Math.min(365, days)));
        model.addAttribute("stats", analyticsService.getDashboardStats(days));
        return "analytics/dashboard";
    }
}
