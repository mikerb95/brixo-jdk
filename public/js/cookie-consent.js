/**
 * Cookie Consent Manager
 * Gestiona el banner de consentimiento de cookies y las preferencias del usuario
 */

(function() {
    'use strict';

    const COOKIE_CONSENT_KEY = 'brixo_cookie_consent';
    const COOKIE_EXPIRY_DAYS = 365;

    /**
     * Obtiene el valor de consentimiento guardado
     */
    function getConsent() {
        return localStorage.getItem(COOKIE_CONSENT_KEY);
    }

    /**
     * Guarda el consentimiento del usuario
     */
    function setConsent(value) {
        localStorage.setItem(COOKIE_CONSENT_KEY, value);
        
        // También guardar en cookie por compatibilidad
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + COOKIE_EXPIRY_DAYS);
        document.cookie = `${COOKIE_CONSENT_KEY}=${value}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
    }

    /**
     * Oculta el banner de cookies
     */
    function hideBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.style.animation = 'slideDown 0.3s ease-out';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        }
    }

    /**
     * Muestra el banner de cookies
     */
    function showBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.style.display = 'block';
        }
    }

    /**
     * Inicializa las cookies analíticas (First-Party Analytics)
     */
    function initializeAnalytics() {
        // La analítica first-party se activa automáticamente
        // desde brixo-analytics.js al detectar el consentimiento.
        // Disparar un re-track por si el script ya cargó.
        if (window.BrixoAnalytics) {
            window.BrixoAnalytics.pageview();
        }
    }

    /**
     * Limpia las cookies analíticas
     */
    function clearAnalyticsCookies() {
        // Eliminar cookie de visitor_id de analítica propia
        document.cookie = 'bx_vid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        // Limpiar session storage
        sessionStorage.removeItem('bx_sid');
        sessionStorage.removeItem('bx_sid_ts');
    }

    /**
     * Maneja la aceptación de cookies
     */
    function handleAccept() {
        setConsent('accepted');
        initializeAnalytics();
        hideBanner();
        
        // Disparar evento personalizado
        window.dispatchEvent(new CustomEvent('cookieConsentAccepted'));
    }

    /**
     * Maneja el rechazo de cookies
     */
    function handleReject() {
        setConsent('rejected');
        clearAnalyticsCookies();
        hideBanner();
        
        // Disparar evento personalizado
        window.dispatchEvent(new CustomEvent('cookieConsentRejected'));
    }

    /**
     * Inicialización cuando el DOM esté listo
     */
    function init() {
        const consent = getConsent();

        // Si ya hay consentimiento guardado
        if (consent === 'accepted') {
            initializeAnalytics();
            return;
        } else if (consent === 'rejected') {
            clearAnalyticsCookies();
            return;
        }

        // Si no hay consentimiento, mostrar el banner después de 1 segundo
        setTimeout(() => {
            showBanner();
        }, 1000);

        // Agregar event listeners a los botones
        const acceptBtn = document.getElementById('cookieAccept');
        const rejectBtn = document.getElementById('cookieReject');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', handleAccept);
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', handleReject);
        }
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Exponer funciones públicas si es necesario
    window.BrixoCookieConsent = {
        getConsent: getConsent,
        accept: handleAccept,
        reject: handleReject,
        showBanner: showBanner
    };

})();

// Añadir animación de salida al CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
