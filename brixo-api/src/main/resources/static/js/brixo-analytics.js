/**
 * Brixo First-Party Analytics Tracker
 * ------------------------------------
 * Lightweight (~2KB) first-party analytics. Zero third-party dependencies.
 *
 * Flow:
 *   1. Generates UUID v4 as visitor_id, stores in secure cookie
 *   2. Captures: URL, referrer, screen resolution, viewport, language, device type
 *   3. Sends via navigator.sendBeacon (non-blocking) to /api/v1/track
 *   4. Respects user cookie consent
 *
 * Privacy:
 *   - No PII sent
 *   - IP anonymized server-side before persistence
 *   - No tracking if user rejects cookies
 *   - DNT compatible
 */
(function () {
    'use strict';

    const CONFIG = {
        endpoint:       '/api/v1/track',
        cookieName:     'bx_vid',
        cookieDays:     365,
        consentKey:     'brixo_cookie_consent',
        sessionTimeout: 30,
        debounceMs:     300,
    };

    function hasConsent() {
        if (navigator.doNotTrack === '1' || window.doNotTrack === '1') return false;
        const consent = localStorage.getItem(CONFIG.consentKey);
        return consent === 'accepted';
    }

    function generateUUID() {
        if (crypto && crypto.randomUUID) return crypto.randomUUID();
        const bytes = new Uint8Array(16);
        crypto.getRandomValues(bytes);
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        const hex = Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('');
        return [hex.slice(0, 8), hex.slice(8, 12), hex.slice(12, 16), hex.slice(16, 20), hex.slice(20, 32)].join('-');
    }

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setDate(expires.getDate() + days);
        let cookieStr = `${name}=${encodeURIComponent(value)}`;
        cookieStr += `; expires=${expires.toUTCString()}`;
        cookieStr += '; path=/';
        cookieStr += '; SameSite=Lax';
        if (location.protocol === 'https:') cookieStr += '; Secure';
        document.cookie = cookieStr;
    }

    function getVisitorId() {
        let vid = getCookie(CONFIG.cookieName);
        if (!vid) { vid = generateUUID(); setCookie(CONFIG.cookieName, vid, CONFIG.cookieDays); }
        return vid;
    }

    function getSessionId() {
        const now = Date.now();
        const key = 'bx_sid', timeKey = 'bx_sid_ts';
        const stored = sessionStorage.getItem(key);
        const lastActivity = parseInt(sessionStorage.getItem(timeKey) || '0', 10);
        const elapsed = (now - lastActivity) / 1000 / 60;
        let sid;
        if (!stored || elapsed > CONFIG.sessionTimeout) {
            sid = generateUUID();
            sessionStorage.setItem(key, sid);
        } else { sid = stored; }
        sessionStorage.setItem(timeKey, now.toString());
        return sid;
    }

    function getDeviceType() {
        const w = window.innerWidth;
        if (w < 768)  return 'mobile';
        if (w < 1024) return 'tablet';
        return 'desktop';
    }

    function buildPayload(eventType, extraData) {
        return {
            visitor_id:  getVisitorId(),
            session_id:  getSessionId(),
            event:       eventType || 'pageview',
            timestamp:   new Date().toISOString(),
            url:         location.href,
            path:        location.pathname,
            referrer:    document.referrer || null,
            title:       document.title || null,
            screen:      `${screen.width}x${screen.height}`,
            viewport:    `${window.innerWidth}x${window.innerHeight}`,
            device_type: getDeviceType(),
            language:    navigator.language || null,
            ...(extraData || {}),
        };
    }

    function send(payload) {
        const data = JSON.stringify(payload);
        if (navigator.sendBeacon) {
            const blob = new Blob([data], { type: 'application/json' });
            if (navigator.sendBeacon(CONFIG.endpoint, blob)) return;
        }
        try {
            fetch(CONFIG.endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: data, keepalive: true }).catch(function () {});
        } catch (e) {}
    }

    function trackPageview() { if (!hasConsent()) return; send(buildPayload('pageview')); }
    function trackEvent(eventName, data) { if (!hasConsent()) return; send(buildPayload(eventName, data)); }

    function trackEngagement() {
        const startTime = Date.now();
        function sendEngagement() {
            if (!hasConsent()) return;
            const duration = Math.round((Date.now() - startTime) / 1000);
            if (duration < 2) return;
            send(buildPayload('engagement', { duration_seconds: duration }));
        }
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') sendEngagement();
        });
    }

    function init() {
        trackPageview();
        trackEngagement();
        window.addEventListener('cookieConsentAccepted', function () { trackPageview(); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else { init(); }

    window.BrixoAnalytics = { track: trackEvent, pageview: trackPageview, getVisitorId: getVisitorId };
})();
