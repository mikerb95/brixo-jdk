<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo — Brixo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background: #000;
            color: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* ── Slide mode: full-screen image ── */
        #slide-layer {
            position: fixed;
            inset: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            transition: opacity 0.35s ease;
        }

        #slide-layer.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #slide-layer img {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
        }

        /* ── URL mode: full-screen iframe ── */
        #iframe-layer {
            position: fixed;
            inset: 0;
            z-index: 5;
            background: #fff;
            transition: opacity 0.35s ease;
        }

        #iframe-layer.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #iframe-layer iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* ── Fullscreen button (barely visible, top-right corner) ── */
        .fs-trigger {
            position: fixed;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            z-index: 9999;
            cursor: pointer;
            background: transparent;
            border: none;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }

        .fs-trigger:hover::after {
            content: '⛶';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            border-radius: 8px;
            color: #fff;
            font-size: 18px;
        }

        /* ── Subtle transition indicator ── */
        #mode-indicator {
            position: fixed;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s ease;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(8px);
            color: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.1);
        }

        #mode-indicator.show {
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Fullscreen trigger (invisible corner) -->
    <button class="fs-trigger" onclick="toggleFullscreen()" title="Pantalla Completa"></button>

    <!-- Slide Layer -->
    <div id="slide-layer">
        <img id="slide-img" src="/presentation/Slide1.PNG" alt="Slide"
             onerror="this.src=this.src.replace('.PNG','.png'); this.onerror=null;">
    </div>

    <!-- Iframe Layer -->
    <div id="iframe-layer" class="hidden">
        <iframe id="demo-iframe" src="about:blank" allow="fullscreen; geolocation" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"></iframe>
    </div>

    <!-- Mode transition indicator -->
    <div id="mode-indicator"></div>

    <script>
        const totalSlides = <?= $totalSlides ?>;
        let currentMode = 'slides';
        let currentSlide = 1;
        let currentUrl = '';

        const slideLayer  = document.getElementById('slide-layer');
        const iframeLayer = document.getElementById('iframe-layer');
        const slideImg    = document.getElementById('slide-img');
        const demoIframe  = document.getElementById('demo-iframe');
        const indicator   = document.getElementById('mode-indicator');

        let indicatorTimeout = null;

        function showIndicator(text) {
            indicator.textContent = text;
            indicator.classList.add('show');
            clearTimeout(indicatorTimeout);
            indicatorTimeout = setTimeout(() => {
                indicator.classList.remove('show');
            }, 2000);
        }

        function setSlideMode(slideNum) {
            slideLayer.classList.remove('hidden');
            iframeLayer.classList.add('hidden');
            slideImg.src = `/presentation/Slide${slideNum}.PNG`;
            currentMode = 'slides';
            currentSlide = slideNum;
        }

        function setUrlMode(url) {
            iframeLayer.classList.remove('hidden');
            slideLayer.classList.add('hidden');

            // Only reload iframe if URL actually changed
            if (currentUrl !== url) {
                demoIframe.src = url;
                currentUrl = url;
                showIndicator('🌐 Demo en vivo');
            }
            currentMode = 'url';
        }

        function pollState() {
            // Poll both APIs in parallel
            Promise.all([
                fetch('/api/demo').then(r => r.json()),
                fetch('/api/slide').then(r => r.json())
            ]).then(([demoState, slideState]) => {

                if (demoState.mode === 'url' && demoState.url) {
                    // URL mode
                    if (currentMode !== 'url' || currentUrl !== demoState.url) {
                        setUrlMode(demoState.url);
                    }
                } else {
                    // Slides mode
                    const newSlide = slideState.slide || demoState.slide || 1;
                    if (currentMode !== 'slides') {
                        showIndicator(`📊 Slide ${newSlide}`);
                    }
                    if (currentMode !== 'slides' || currentSlide !== newSlide) {
                        setSlideMode(newSlide);
                    }
                }
            }).catch(err => {
                console.warn('Poll error:', err);
            });
        }

        // Initial load
        pollState();

        // Poll every 800ms for responsive transitions
        setInterval(pollState, 800);

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen();
            }
        }

        // Keyboard: F for fullscreen, Escape exits
        document.addEventListener('keydown', e => {
            if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                toggleFullscreen();
            }
        });
    </script>
</body>

</html>
