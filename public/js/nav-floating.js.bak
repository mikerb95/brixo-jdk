(function () {
    const hero = document.querySelector('.hero');
    const floatingNav = document.getElementById('floating-nav');
    const heroNav = document.getElementById('hero-nav');
    const topNav = document.querySelector('.brixo-navbar');
    const isHome = !!hero; // usamos presencia de .hero para detectar index
    const alwaysVisible = document.body.classList.contains('always-show-floating-nav');

    function showFloatingNav(show) {
        if (!floatingNav) return;
        floatingNav.classList.toggle('visible', !!show);
        // Ocultamos la barra fija (y hero-nav en home) cuando aparece la flotante
        if (topNav) topNav.classList.toggle('d-none', !!show);
        if (isHome && heroNav) heroNav.classList.toggle('hidden', !!show);
        
        // Toggle body class for offset adjustments
        document.body.classList.toggle('floating-offset', !!show);
    }

    if (alwaysVisible) {
        showFloatingNav(true);
        return;
    }

    if (hero && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                const entry = entries[0];
                showFloatingNav(!entry.isIntersecting);
            },
            { root: null, rootMargin: '0px 0px 0px 0px', threshold: 0 }
        );
        observer.observe(hero);
    } else {
        // Páginas sin hero (info, 404, etc.):
        // mantenemos la navbar flotante oculta al inicio
        // y sólo la mostramos tras cierto scroll.
        const threshold = 120;
        function onScroll() {
            showFloatingNav(window.scrollY > threshold);
        }
        window.addEventListener('scroll', onScroll);
        window.addEventListener('resize', onScroll);
        onScroll();
    }
})();
