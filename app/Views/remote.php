<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Control Remoto - Presentación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .remote-container {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 90vw;
            width: 100%;
        }

        .slide-counter {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 15px;
        }

        .control-btn {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            font-size: 2.5rem;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            margin: 0 1rem;
        }

        .control-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .control-btn:active {
            transform: scale(0.95);
        }

        .btn-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin-top: 2rem;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .btn-row {
                flex-direction: row;
            }
        }

        .slide-indicators {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .indicator.active {
            background: #fff;
            transform: scale(1.2);
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

        @media (max-width: 576px) {
            .control-btn {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }

            .btn-row {
                gap: 1rem;
            }

            .slide-counter {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .control-btn {
                width: 30vh;
                height: 30vh;
                font-size: 3rem;
            }

            .btn-row {
                gap: 3vh;
            }

            .remote-container {
                padding: 1rem;
            }

            .slide-counter {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Pantalla Completa">⛶</button>
    <div class="remote-container">
        <h1 class="mb-4">🎛️ Control Remoto</h1>
        <div class="slide-counter">
            <span id="current-slide">1</span> / <?= $totalSlides ?>
        </div>
        <div class="btn-row">
            <button class="control-btn" onclick="changeSlide(-1)" id="prev-btn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="control-btn" onclick="changeSlide(1)" id="next-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="slide-indicators" id="indicators">
            <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
                <div class="indicator <?= $i === 1 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        let currentSlide = 1;
        const totalSlides = <?= $totalSlides ?>;

        function updateDisplay() {
            document.getElementById('current-slide').textContent = currentSlide;
            document.querySelectorAll('.indicator').forEach((ind, index) => {
                ind.classList.toggle('active', index + 1 === currentSlide);
            });
        }

        function changeSlide(direction) {
            const newSlide = currentSlide + direction;
            if (newSlide >= 1 && newSlide <= totalSlides) {
                // Vibración para feedback táctil
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }

                fetch('/api/slide', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slide: newSlide })
                })
                    .then(response => response.json())
                    .then(data => {
                        currentSlide = data.slide;
                        updateDisplay();
                    });
            }
        }

        // Actualizar display inicial
        fetch('/api/slide')
            .then(response => response.json())
            .then(data => {
                currentSlide = data.slide;
                updateDisplay();
            });

        // Polling para actualizar si cambia desde otro lugar
        setInterval(() => {
            fetch('/api/slide')
                .then(response => response.json())
                .then(data => {
                    if (data.slide !== currentSlide) {
                        currentSlide = data.slide;
                        updateDisplay();
                    }
                });
        }, 1000);

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        // Soporte para gestos táctiles (swipe)
        let startX = 0;
        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        document.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const diffX = startX - endX;
            if (Math.abs(diffX) > 50) { // Umbral para swipe
                if (diffX > 0) {
                    changeSlide(1); // Swipe left -> next
                } else {
                    changeSlide(-1); // Swipe right -> prev
                }
            }
        });
    </script>
</body>

</html>