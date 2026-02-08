<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Brixo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/brixo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <style>
        .reset-hero {
            background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
            min-height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        .password-strength-bar.weak { width: 33%; background: #dc3545; }
        .password-strength-bar.medium { width: 66%; background: #ffc107; }
        .password-strength-bar.strong { width: 100%; background: #28a745; }
    </style>
</head>
<body class="bg-light">
    <?= view('partials/navbar') ?>

    <section class="reset-hero text-white">
        <div class="container text-center">
            <i class="fas fa-lock fa-3x mb-3 opacity-75"></i>
            <h1 class="display-5 fw-bold">Nueva Contraseña</h1>
            <p class="lead opacity-75">Ingresa tu nueva contraseña segura</p>
        </div>
    </section>

    <div class="container py-5" style="max-width: 500px;">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center rounded-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div><?= esc($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" action="/password/update" id="resetForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">
                    <input type="hidden" name="email" value="<?= esc($email) ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Nueva contraseña</label>
                        <div class="position-relative">
                            <input type="password" class="form-control form-control-lg p-3 rounded-3" 
                                   id="password" name="password" 
                                   placeholder="Mínimo 8 caracteres" 
                                   required minlength="8">
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                    id="togglePassword" style="z-index: 10;">
                                <i class="fas fa-eye text-muted"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <small class="text-muted" id="strengthText">Ingresa al menos 8 caracteres</small>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label fw-semibold">Confirmar contraseña</label>
                        <input type="password" class="form-control form-control-lg p-3 rounded-3" 
                               id="password_confirm" name="password_confirm" 
                               placeholder="Repite tu nueva contraseña" 
                               required minlength="8">
                        <small class="text-muted" id="matchText"></small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold" id="submitBtn">
                        <i class="fas fa-check-circle me-2"></i>Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 bg-light mt-4">
            <div class="card-body text-center small text-muted">
                <i class="fas fa-shield-alt me-1"></i>
                <strong>Consejos de seguridad:</strong> Usa letras mayúsculas, minúsculas, números y símbolos
            </div>
        </div>
    </div>

    <?= view('partials/footer') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const matchText = document.getElementById('matchText');
        const togglePassword = document.getElementById('togglePassword');
        const submitBtn = document.getElementById('submitBtn');

        // Toggle password visibility
        togglePassword.addEventListener('click', function() {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            passwordConfirm.type = type;
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Password strength indicator
        password.addEventListener('input', function() {
            const val = this.value;
            let strength = 0;

            if (val.length >= 8) strength++;
            if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength++;
            if (val.match(/[0-9]/)) strength++;
            if (val.match(/[^a-zA-Z0-9]/)) strength++;

            strengthBar.className = 'password-strength-bar';
            
            if (val.length === 0) {
                strengthText.textContent = 'Ingresa al menos 8 caracteres';
                strengthText.className = 'text-muted';
            } else if (strength <= 1) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Contraseña débil';
                strengthText.className = 'text-danger small';
            } else if (strength <= 3) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Contraseña media';
                strengthText.className = 'text-warning small';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Contraseña fuerte';
                strengthText.className = 'text-success small';
            }

            checkMatch();
        });

        // Password match indicator
        passwordConfirm.addEventListener('input', checkMatch);

        function checkMatch() {
            if (passwordConfirm.value.length === 0) {
                matchText.textContent = '';
                return;
            }

            if (password.value === passwordConfirm.value) {
                matchText.textContent = '✓ Las contraseñas coinciden';
                matchText.className = 'text-success small';
                submitBtn.disabled = false;
            } else {
                matchText.textContent = '✗ Las contraseñas no coinciden';
                matchText.className = 'text-danger small';
                submitBtn.disabled = true;
            }
        }
    });
    </script>
</body>
</html>
