<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat con <?= esc($nombreOtro) ?> - Brixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f0f2f5;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 75%;
            padding: 10px 15px;
            border-radius: 15px;
            position: relative;
            word-wrap: break-word;
        }

        .message.sent {
            align-self: flex-end;
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 2px;
        }

        .message.received {
            align-self: flex-start;
            background-color: #e9ecef;
            color: #333;
            border-bottom-left-radius: 2px;
        }

        .message-time {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.7;
            text-align: right;
        }

        .chat-input-area {
            padding: 15px;
            border-top: 1px solid #eee;
            background: white;
        }
    </style>
</head>

<body>

    <div class="chat-container">
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <a href="<?= base_url('/mensajes') ?>" class="btn btn-link text-dark me-2"><i
                        class="fas fa-arrow-left"></i></a>
                <div>
                    <h5 class="mb-0"><?= esc($nombreOtro) ?></h5>
                    <small class="text-muted text-capitalize"><?= esc($otroRol) ?></small>
                </div>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php foreach ($mensajes as $msg): ?>
                <div
                    class="message <?= ($msg['remitente_id'] == $miId && $msg['remitente_rol'] == $miRol) ? 'sent' : 'received' ?>">
                    <?= esc($msg['contenido']) ?>
                    <div class="message-time"><?= date('H:i', strtotime($msg['creado_en'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-input-area">
            <form id="chatForm" class="d-flex gap-2">
                <input type="hidden" name="destinatario_id" value="<?= $otroId ?>">
                <input type="hidden" name="destinatario_rol" value="<?= $otroRol ?>">
                <input type="text" name="contenido" id="messageInput" class="form-control"
                    placeholder="Escribe un mensaje..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            const chatMessages = document.getElementById('chatMessages');
            const otroId = <?= $otroId ?>;
            const otroRol = '<?= $otroRol ?>';

            // Scroll to bottom
            function scrollToBottom() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            scrollToBottom();

            // Enviar mensaje
            $('#chatForm').on('submit', function (e) {
                e.preventDefault();
                const input = $('#messageInput');
                const contenido = input.val().trim();

                if (!contenido) return;

                $.post('<?= base_url('/mensajes/enviar') ?>', $(this).serialize(), function (response) {
                    if (response.status === 'success') {
                        // Agregar mensaje visualmente
                        const now = new Date();
                        const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                        $('#chatMessages').append(`
                            <div class="message sent">
                                ${$('<div>').text(contenido).html()}
                                <div class="message-time">${time}</div>
                            </div>
                        `);

                        input.val('');
                        scrollToBottom();
                    }
                });
            });

            // Polling para nuevos mensajes
            setInterval(function () {
                $.get(`<?= base_url('/mensajes/nuevos') ?>/${otroId}/${otroRol}`, function (mensajes) {
                    if (mensajes && mensajes.length > 0) {
                        mensajes.forEach(function (msg) {
                            const date = new Date(msg.creado_en); // Asumiendo que viene en formato compatible
                            const time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                            $('#chatMessages').append(`
                                <div class="message received">
                                    ${$('<div>').text(msg.contenido).html()}
                                    <div class="message-time">${time}</div>
                                </div>
                            `);
                        });
                        scrollToBottom();
                    }
                });
            }, 3000); // Cada 3 segundos
        });
    </script>
</body>

</html>