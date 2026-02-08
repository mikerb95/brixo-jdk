<!-- Cookie Consent Banner -->
<div id="cookieConsent" class="cookie-consent shadow-lg" style="display: none;">
    <div class="cookie-consent-content">
        <div class="cookie-icon">
            <i class="fas fa-cookie-bite"></i>
        </div>
        <div class="cookie-text">
            <h5 class="fw-bold mb-2">🍪 Uso de Cookies</h5>
            <p class="mb-0 small">
                Utilizamos cookies propias para mejorar tu experiencia y analizar el tráfico de forma anónima. 
                No compartimos datos con terceros. 
                Al hacer clic en "Aceptar", aceptas el uso de cookies analíticas. 
                <a href="/politica-cookies" class="text-white text-decoration-underline">Más información</a>
            </p>
        </div>
        <div class="cookie-actions">
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4 me-2" id="cookieReject">
                Rechazar
            </button>
            <button type="button" class="btn btn-sm btn-light rounded-pill px-4 fw-bold" id="cookieAccept">
                <i class="fas fa-check me-1"></i>Aceptar
            </button>
        </div>
    </div>
</div>

<style>
.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
    color: white;
    padding: 1.5rem;
    z-index: 9999;
    border-top: 3px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.cookie-consent-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.cookie-icon {
    font-size: 2.5rem;
    opacity: 0.9;
    flex-shrink: 0;
}

.cookie-text {
    flex: 1;
    min-width: 300px;
}

.cookie-text h5 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.cookie-text p {
    line-height: 1.5;
    opacity: 0.95;
}

.cookie-text a {
    font-weight: 600;
}

.cookie-text a:hover {
    opacity: 0.8;
}

.cookie-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.cookie-actions .btn {
    transition: all 0.2s ease;
}

.cookie-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.cookie-actions .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .cookie-consent {
        padding: 1rem;
    }
    
    .cookie-consent-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .cookie-icon {
        font-size: 2rem;
    }
    
    .cookie-text {
        min-width: auto;
    }
    
    .cookie-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .cookie-actions .btn {
        width: 100%;
    }
}
</style>
