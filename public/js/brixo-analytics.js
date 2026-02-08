/**
 * Brixo First-Party Analytics Tracker
 * ─────────────────────────────────────
 * Script ligero (~2KB) de analítica propia. Cero dependencias de terceros.
 *
 * Flujo de datos:
 *   1. Genera un UUID v4 como visitor_id y lo almacena en cookie segura
 *   2. Captura: URL, referrer, resolución de pantalla, viewport, idioma, tipo de dispositivo
 *   3. Envía vía navigator.sendBeacon (no bloquea renderizado) a /api/v1/track
 *   4. Respeta el consentimiento de cookies del usuario
 *
 * Privacidad:
 *   - No envía datos personales identificables
 *   - La IP se anonimiza en el servidor antes de persistirse
 *   - Si el usuario rechaza cookies, no se rastrea
 *   - Compatible con DNT (Do Not Track)
 */

(function () {
    'use strict';

    // ── Configuración ───────────────────────────────────────────
    const CONFIG = {
        endpoint:       '/api/v1/track',
        cookieName:     'bx_vid',
        cookieDays:     365,
        consentKey:     'brixo_cookie_consent',
        sessionTimeout: 30,  // minutos de inactividad = nueva sesión
        debounceMs:     300, // debounce para eventos de resize
    };

    // ── Respetar privacidad ─────────────────────────────────────

    /**
     * Verifica si el usuario ha dado consentimiento para cookies analíticas.
     * Respeta Do Not Track del navegador.
     */
    function hasConsent() {
        // Respetar Do Not Track
        if (navigator.doNotTrack === '1' || window.doNotTrack === '1') {
            return false;
        }

        const consent = localStorage.getItem(CONFIG.consentKey);
        return consent === 'accepted';
    }

    // ── UUID v4 ─────────────────────────────────────────────────

    /**
     * Genera un UUID v4 usando crypto.getRandomValues (CSPRNG).
     * Formato: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     */
    function generateUUID() {
        if (crypto && crypto.randomUUID) {
            return crypto.randomUUID();
        }

        // Fallback para navegadores sin crypto.randomUUID
        const bytes = new Uint8Array(16);
        crypto.getRandomValues(bytes);

        // Set version (4) y variant (10xx)
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;

        const hex = Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('');
        return [
            hex.slice(0, 8),
            hex.slice(8, 12),
            hex.slice(12, 16),
            hex.slice(16, 20),
            hex.slice(20, 32),
        ].join('-');
    }

    // ── Gestión de Cookies ──────────────────────────────────────

    /**
     * Lee una cookie por nombre.
     */
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    /**
     * Establece una cookie con flags de seguridad.
     * - SameSite=Lax: Previene CSRF pero permite navegación normal
     * - Secure: Solo se envía por HTTPS (en producción)
     * - Path=/: Disponible en todo el sitio
     */
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setDate(expires.getDate() + days);

        let cookieStr = `${name}=${encodeURIComponent(value)}`;
        cookieStr += `; expires=${expires.toUTCString()}`;
        cookieStr += '; path=/';
        cookieStr += '; SameSite=Lax';

        // Solo agregar Secure si estamos en HTTPS
        if (location.protocol === 'https:') {
            cookieStr += '; Secure';
        }

        document.cookie = cookieStr;
    }

    /**
     * Obtiene o crea el visitor_id persistente.
     */
    function getVisitorId() {
        let vid = getCookie(CONFIG.cookieName);

        if (!vid) {
            vid = generateUUID();
            setCookie(CONFIG.cookieName, vid, CONFIG.cookieDays);
        }

        return vid;
    }

    // ── Session ID ──────────────────────────────────────────────

    /**
     * Genera un session_id basado en inactividad.
     * Una nueva sesión comienza si han pasado más de `sessionTimeout` minutos.
     */
    function getSessionId() {
        const now = Date.now();
        const key = 'bx_sid';
        const timeKey = 'bx_sid_ts';
        const stored = sessionStorage.getItem(key);
        const lastActivity = parseInt(sessionStorage.getItem(timeKey) || '0', 10);
        const elapsed = (now - lastActivity) / 1000 / 60; // minutos

        let sid;
        if (!stored || elapsed > CONFIG.sessionTimeout) {
            sid = generateUUID();
            sessionStorage.setItem(key, sid);
        } else {
            sid = stored;
        }

        sessionStorage.setItem(timeKey, now.toString());
        return sid;
    }

    // ── Detección de Dispositivo ────────────────────────────────

    /**
     * Clasifica el tipo de dispositivo basado en el ancho del viewport.
     */
    function getDeviceType() {
        const w = window.innerWidth;
        if (w < 768)  return 'mobile';
        if (w < 1024) return 'tablet';
        return 'desktop';
    }

    // ── Recolección de Datos ────────────────────────────────────

    /**
     * Construye el payload de analítica.
     * Captura SOLO datos no identificables personalmente.
     */
    function buildPayload(eventType, extraData) {
        return {
            // Identificadores anónimos
            visitor_id:  getVisitorId(),
            session_id:  getSessionId(),

            // Evento
            event:       eventType || 'pageview',
            timestamp:   new Date().toISOString(),

            // Página
            url:         location.href,
            path:        location.pathname,
            referrer:    document.referrer || null,
            title:       document.title || null,

            // Dispositivo (sin fingerprinting invasivo)
            screen:      `${screen.width}x${screen.height}`,
            viewport:    `${window.innerWidth}x${window.innerHeight}`,
            device_type: getDeviceType(),
            language:    navigator.language || null,

            // Datos extra opcionales
            ...(extraData || {}),
        };
    }

    // ── Envío de Datos ──────────────────────────────────────────

    /**
     * Envía el payload al endpoint propio.
     *
     * Usa navigator.sendBeacon como método principal:
     *   - No bloquea el renderizado ni la descarga de la página
     *   - Garantiza entrega incluso al cerrar la pestaña
     *   - No espera respuesta (fire-and-forget)
     *
     * Fallback a fetch con keepalive para navegadores sin sendBeacon.
     */
    function send(payload) {
        const data = JSON.stringify(payload);

        // Método principal: sendBeacon (no bloqueante)
        if (navigator.sendBeacon) {
            const blob = new Blob([data], { type: 'application/json' });
            const queued = navigator.sendBeacon(CONFIG.endpoint, blob);

            if (queued) return; // Encolado con éxito
        }

        // Fallback: fetch con keepalive
        try {
            fetch(CONFIG.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: data,
                keepalive: true, // Permite que el request sobreviva al cierre de página
            }).catch(function () {
                // Silenciar errores de red
            });
        } catch (e) {
            // Silenciar errores
        }
    }

    // ── Tracking de Eventos ─────────────────────────────────────

    /**
     * Registra un pageview.
     */
    function trackPageview() {
        if (!hasConsent()) return;
        send(buildPayload('pageview'));
    }

    /**
     * Registra un evento personalizado.
     * Ejemplo: BrixoAnalytics.track('click_cta', { button: 'cotizador' })
     */
    function trackEvent(eventName, data) {
        if (!hasConsent()) return;
        send(buildPayload(eventName, data));
    }

    // ── Engagement: Tiempo en Página ────────────────────────────

    /**
     * Al cerrar/cambiar de pestaña, envía el tiempo total de permanencia.
     */
    function trackEngagement() {
        const startTime = Date.now();

        function sendEngagement() {
            if (!hasConsent()) return;
            const duration = Math.round((Date.now() - startTime) / 1000);
            if (duration < 2) return; // Ignorar bounces < 2s

            send(buildPayload('engagement', {
                duration_seconds: duration,
            }));
        }

        // Enviar al cerrar pestaña o ir a otra página
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                sendEngagement();
            }
        });
    }

    // ── Inicialización ──────────────────────────────────────────

    function init() {
        // Track pageview al cargar
        trackPageview();

        // Track engagement (tiempo en página)
        trackEngagement();

        // Escuchar evento de consentimiento aceptado para activar retroactivamente
        window.addEventListener('cookieConsentAccepted', function () {
            trackPageview();
        });
    }

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── API Pública ─────────────────────────────────────────────

    /**
     * Expone funciones para tracking manual desde otros scripts.
     *
     * Uso:
     *   BrixoAnalytics.track('signup_click', { source: 'navbar' });
     *   BrixoAnalytics.pageview(); // Re-track (ej. en SPA navigation)
     */
    window.BrixoAnalytics = {
        track:    trackEvent,
        pageview: trackPageview,
        getVisitorId: getVisitorId,
    };

})();
