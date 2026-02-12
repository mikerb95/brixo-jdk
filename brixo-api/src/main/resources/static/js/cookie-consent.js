/**
 * Cookie Consent Manager
 * Gestiona el banner de consentimiento de cookies y las preferencias del usuario
 */
(function () {
    'use strict';

    const COOKIE_CONSENT_KEY = 'brixo_cookie_consent';
    const COOKIE_EXPIRY_DAYS = 365;

    function getConsent() {
        return localStorage.getItem(COOKIE_CONSENT_KEY);
    }

    function setConsent(value) {
        localStorage.setItem(COOKIE_CONSENT_KEY, value);
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + COOKIE_EXPIRY_DAYS);
        document.cookie = `${COOKIE_CONSENT_KEY}=${value}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
    }

    function hideBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.style.animation = 'slideDown 0.3s ease-out';
            setTimeout(() => { banner.style.display = 'none'; }, 300);
        }
    }

    function showBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) banner.style.display = 'block';
    }

    function initializeAnalytics() {
        if (window.BrixoAnalytics) window.BrixoAnalytics.pageview();
    }

    function clearAnalyticsCookies() {
        document.cookie = 'bx_vid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        sessionStorage.removeItem('bx_sid');
        sessionStorage.removeItem('bx_sid_ts');
    }

    function handleAccept() {
        setConsent('accepted');
        initializeAnalytics();
        hideBanner();
        window.dispatchEvent(new CustomEvent('cookieConsentAccepted'));
    }

    function handleReject() {
        setConsent('rejected');
        clearAnalyticsCookies();
        hideBanner();
        window.dispatchEvent(new CustomEvent('cookieConsentRejected'));
    }

    function init() {
        const consent = getConsent();
        if (consent === 'accepted') { initializeAnalytics(); return; }
        if (consent === 'rejected') { clearAnalyticsCookies(); return; }

        setTimeout(() => showBanner(), 1000);

        const acceptBtn = document.getElementById('cookieAccept');
        const rejectBtn = document.getElementById('cookieReject');
        if (acceptBtn) acceptBtn.addEventListener('click', handleAccept);
        if (rejectBtn) rejectBtn.addEventListener('click', handleReject);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.BrixoCookieConsent = { getConsent, accept: handleAccept, reject: handleReject, showBanner };
})();

// Slide-down animation for banner dismissal
const _ccStyle = document.createElement('style');
_ccStyle.textContent = `@keyframes slideDown{from{transform:translateY(0);opacity:1}to{transform:translateY(100%);opacity:0}}`;
document.head.appendChild(_ccStyle);
