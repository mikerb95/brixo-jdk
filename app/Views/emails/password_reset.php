<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #485166 0%, #2c3444 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #485166;
            font-size: 22px;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: #485166;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background: #2c3444;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #485166;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .link-fallback {
            font-size: 12px;
            color: #666;
            word-break: break-all;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Recuperación de Contraseña</h1>
        </div>
        
        <div class="content">
            <h2>Hola <?= esc($nombre) ?>,</h2>
            
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>Brixo</strong>.</p>
            
            <p>Si fuiste tú quien solicitó este cambio, haz clic en el siguiente botón para crear una nueva contraseña:</p>
            
            <div style="text-align: center;">
                <a href="<?= esc($resetLink) ?>" class="button">Restablecer Contraseña</a>
            </div>

            <div class="info-box">
                <strong>⏱️ Importante:</strong> Este enlace expirará en <strong>1 hora</strong> por seguridad.
            </div>

            <p>Si no puedes hacer clic en el botón, copia y pega este enlace en tu navegador:</p>
            <div class="link-fallback">
                <?= esc($resetLink) ?>
            </div>

            <div class="info-box">
                <strong>🛡️ ¿No solicitaste este cambio?</strong><br>
                Si no fuiste tú, ignora este correo. Tu contraseña actual sigue siendo segura y no se realizará ningún cambio.
            </div>
        </div>

        <div class="footer">
            <p><strong>Brixo</strong> - Tu plataforma de servicios para el hogar</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
