document.addEventListener('DOMContentLoaded', function() {
    const footerContainer = document.getElementById('brixo-footer-container');
    
    if (footerContainer) {
        const currentYear = new Date().getFullYear();
        
        const footerHTML = `
        <footer class="site-footer mt-auto">
            <div class="container" style="max-width: 1200px;">
                <div class="row g-5">
                    <!-- Brand & About -->
                    <div class="col-lg-4 col-md-6">
                        <h4 class="fw-bold mb-3" style="font-size: 1.3rem; letter-spacing: -0.02em;">Brixo</h4>
                        <p style="font-size: 0.9rem; line-height: 1.7; opacity: 0.7; max-width: 320px;">
                            Conectando hogares con los mejores profesionales.
                            Calidad, confianza y seguridad en cada servicio.
                        </p>
                        <div class="d-flex gap-3 mt-3">
                            <a href="https://github.com/mikerb95" target="_blank" style="opacity:0.5; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5" title="GitHub">
                                <i class="fab fa-github" style="font-size:1.2rem; color:#fff"></i>
                            </a>
                            <a href="https://codeigniter.com" target="_blank" style="opacity:0.5; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5" title="CodeIgniter">
                                <i class="fas fa-fire" style="font-size:1.2rem; color:#fff"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <h4 class="fw-bold mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5;">Explorar</h4>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                            <li class="mb-2"><a href="/">Inicio</a></li>
                            <li class="mb-2"><a href="/map">Mapa</a></li>
                            <li class="mb-2"><a href="/especialidades">Especialidades</a></li>
                            <li class="mb-2"><a href="/panel">Mi Panel</a></li>
                        </ul>
                    </div>

                    <!-- Info -->
                    <div class="col-lg-2 col-md-6">
                        <h4 class="fw-bold mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5;">Info</h4>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                            <li class="mb-2"><a href="/sobre-nosotros">Sobre Nosotros</a></li>
                            <li class="mb-2"><a href="/como-funciona">Cómo funciona</a></li>
                            <li class="mb-2"><a href="/seguridad">Seguridad</a></li>
                            <li class="mb-2"><a href="/ayuda">Ayuda</a></li>
                        </ul>
                    </div>

                    <!-- Equipo -->
                    <div class="col-lg-4 col-md-6">
                        <h4 class="fw-bold mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5;">Equipo</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://github.com/mikerb95" target="_blank" class="btn btn-sm rounded-pill" style="border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.7)'">
                                <i class="fab fa-github me-1"></i> mikerb95
                            </a>
                            <a href="https://github.com/Jerson7molina" target="_blank" class="btn btn-sm rounded-pill" style="border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.7)'">
                                <i class="fab fa-github me-1"></i> Jerson7molina
                            </a>
                            <a href="https://github.com/papidani1" target="_blank" class="btn btn-sm rounded-pill" style="border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.7)'">
                                <i class="fab fa-github me-1"></i> papidani1
                            </a>
                            <a href="https://github.com/DavidPino20" target="_blank" class="btn btn-sm rounded-pill" style="border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.7)'">
                                <i class="fab fa-github me-1"></i> DavidPino20
                            </a>
                            <a href="https://github.com/edwinmor24" target="_blank" class="btn btn-sm rounded-pill" style="border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.6)';this.style.color='#fff'" onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='rgba(255,255,255,0.7)'">
                                <i class="fab fa-github me-1"></i> edwinmor24
                            </a>
                        </div>
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.1); margin: 2.5rem 0 1.5rem;">

                <div class="d-flex flex-wrap justify-content-between align-items-center" style="font-size: 0.8rem; opacity: 0.5;">
                    <span>&copy; ${currentYear} Brixo. Todos los derechos reservados.</span>
                    <div class="d-flex gap-3">
                        <a href="/seguridad" style="color: inherit;">Privacidad</a>
                        <a href="/politica-cookies" style="color: inherit;">Cookies</a>
                        <a href="/ayuda" style="color: inherit;">Términos</a>
                    </div>
                </div>
            </div>
        </footer>
        `;
        
        footerContainer.innerHTML = footerHTML;
    }
});
