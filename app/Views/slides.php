<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentación - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #000;
            color: #fff;
        }

        .slide {
            display: none;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .slide.active {
            display: flex;
        }

        .slide img {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
        }

        .fullscreen-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            cursor: pointer;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Pantalla Completa">⛶</button>
    <div id="slides">
        <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
            <div class="slide <?= $i === 1 ? 'active' : '' ?>" data-slide="<?= $i ?>">
                <img src="/presentation/Slide<?= $i ?>.PNG" alt="Slide <?= $i ?>"
                    onerror="this.src='/presentation/Slide<?= $i ?>.png'; this.onerror=null;" loading="lazy">
            </div>
        <?php endfor; ?>
    </div>

    <script>
        let currentSlide = 1;
        const totalSlides = <?= $totalSlides ?>;

        function updateSlide() {
            fetch('/api/slide')
                .then(response => response.json())
                .then(data => {
                    const newSlide = data.slide;
                    if (newSlide !== currentSlide) {
                        document.querySelector('.slide.active').classList.remove('active');
                        document.querySelector(`[data-slide="${newSlide}"]`).classList.add('active');
                        currentSlide = newSlide;
                    }
                });
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        setInterval(updateSlide, 1000); // Polling cada segundo
    </script>
</body>

</html>