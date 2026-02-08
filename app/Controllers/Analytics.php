<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Analytics Controller – Endpoint First-Party
 * ─────────────────────────────────────────────
 *
 * Recibe eventos de analítica desde el script de cliente (brixo-analytics.js)
 * vía navigator.sendBeacon. No usa librerías externas.
 *
 * Flujo:
 *   1. POST /api/v1/track recibe JSON con datos del evento
 *   2. Valida estructura y limita tamaño para prevenir spam
 *   3. Anonimiza la IP (elimina último octeto IPv4 o últimos 80 bits IPv6)
 *   4. Inserta en tabla `analytics_events` de forma eficiente
 *
 * Privacidad (Anonimización de IP):
 *   ┌─────────────────────────────────────────────────────────────╖
 *   │ IPv4: 192.168.1.100 → 192.168.1.0                         │
 *   │ IPv6: 2001:0db8:85a3::8a2e:0370:7334 → 2001:0db8:85a3::   │
 *   │                                                             │
 *   │ Se aplica ANTES de cualquier escritura en BD.               │
 *   │ El IP original JAMÁS se persiste.                           │
 *   │ Técnica: mismo enfoque que Google Analytics "IP Masking".   │
 *   ╘═════════════════════════════════════════════════════════════╛
 */
class Analytics extends BaseController
{
    /**
     * Eventos válidos que aceptamos.
     * Cualquier otro evento se rechaza como spam.
     */
    private const ALLOWED_EVENTS = [
        'pageview',
        'engagement',
        'click_cta',
        'signup_click',
        'cotizador_start',
        'cotizador_complete',
        'solicitud_created',
        'search',
        'error',
    ];

    /**
     * POST /api/v1/track
     *
     * Recibe el JSON del tracker y lo persiste.
     * Responde 204 No Content (fire-and-forget).
     */
    public function track(): ResponseInterface
    {
        // ── Solo aceptar POST ───────────────────────────────────
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405);
        }

        // ── Parsear body JSON ───────────────────────────────────
        $raw = $this->request->getBody();

        // Limitar tamaño del payload (anti-spam: max 4KB)
        if (strlen($raw) > 4096) {
            return $this->response->setStatusCode(413); // Payload Too Large
        }

        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return $this->response->setStatusCode(400);
        }

        // ── Validar campos requeridos ───────────────────────────
        $validation = $this->validatePayload($data);
        if ($validation !== true) {
            return $this->response->setStatusCode(422); // Unprocessable Entity
        }

        // ── Anonimizar IP ───────────────────────────────────────
        $rawIp = $this->request->getIPAddress();
        $anonIp = $this->anonymizeIp($rawIp);

        // ── Extraer User-Agent (solo para clasificación) ────────
        $ua = $this->request->getUserAgent();
        $browser = $ua->getBrowser() ?: 'Unknown';
        $platform = $ua->getPlatform() ?: 'Unknown';

        // ── Detectar bots (anti-spam básico) ────────────────────
        if ($ua->isRobot()) {
            return $this->response->setStatusCode(204); // Ignorar bots silenciosamente
        }

        // ── Sanitizar datos ─────────────────────────────────────
        $event = [
            'visitor_id'  => $this->sanitizeUuid($data['visitor_id']),
            'session_id'  => $this->sanitizeUuid($data['session_id']),
            'event_type'  => $data['event'],
            'url'         => mb_substr($data['url'] ?? '', 0, 2048),
            'path'        => mb_substr($data['path'] ?? '', 0, 512),
            'referrer'    => mb_substr($data['referrer'] ?? '', 0, 2048),
            'title'       => mb_substr($data['title'] ?? '', 0, 512),
            'screen'      => mb_substr($data['screen'] ?? '', 0, 20),
            'viewport'    => mb_substr($data['viewport'] ?? '', 0, 20),
            'device_type' => in_array($data['device_type'] ?? '', ['mobile', 'tablet', 'desktop'])
                             ? $data['device_type'] : 'unknown',
            'language'    => mb_substr($data['language'] ?? '', 0, 10),
            'browser'     => mb_substr($browser, 0, 100),
            'platform'    => mb_substr($platform, 0, 100),
            'ip_anon'     => $anonIp,
            'extra_json'  => null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        // Campos extra según tipo de evento
        $extra = $this->extractExtra($data);
        if (!empty($extra)) {
            $event['extra_json'] = json_encode($extra, JSON_UNESCAPED_UNICODE);
        }

        // ── Persistir en BD ─────────────────────────────────────
        $this->saveEvent($event);

        // ── Respuesta 204 (sin body, fire-and-forget) ───────────
        return $this->response->setStatusCode(204);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVACIDAD: Anonimización de IP
    // ─────────────────────────────────────────────────────────────

    /**
     * Anonimiza una dirección IP eliminando la parte que identifica al host.
     *
     * Técnica:
     *   - IPv4: Reemplaza el último octeto con 0
     *     Ejemplo: 192.168.1.100 → 192.168.1.0
     *     Esto preserva la geolocalización a nivel ciudad/región
     *     pero impide identificar al usuario individual.
     *
     *   - IPv6: Reemplaza los últimos 80 bits (5 grupos) con ceros
     *     Ejemplo: 2001:0db8:85a3:0000:0000:8a2e:0370:7334
     *            → 2001:0db8:85a3::
     *     Preserva el prefijo de red (/48) pero anonimiza el host.
     *
     * Base legal: Este enfoque cumple con las recomendaciones de:
     *   - GDPR (Reglamento General de Protección de Datos)
     *   - CNIL (Autoridad Francesa de Protección de Datos)
     *   - Es la misma técnica que usa Google Analytics con ip_anonymize=true
     *
     * @param string $ip Dirección IP original del request
     * @return string IP anonimizada
     */
    private function anonymizeIp(string $ip): string
    {
        // IPv4: 192.168.1.100 → 192.168.1.0
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0';
            return implode('.', $parts);
        }

        // IPv6: Expandir y truncar los últimos 80 bits
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $packed = inet_pton($ip);
            if ($packed === false) {
                return '0.0.0.0';
            }

            // Poner a cero los últimos 10 bytes (80 bits)
            $bytes = str_split($packed);
            for ($i = 6; $i < 16; $i++) {
                $bytes[$i] = "\0";
            }

            return inet_ntop(implode('', $bytes));
        }

        // Fallback: IP no reconocida
        return '0.0.0.0';
    }

    // ─────────────────────────────────────────────────────────────
    // Validación Anti-Spam
    // ─────────────────────────────────────────────────────────────

    /**
     * Valida la estructura del payload recibido.
     *
     * Criterios de rechazo (anti-spam):
     *   1. Campos requeridos faltantes
     *   2. Tipo de evento no permitido
     *   3. UUID con formato inválido
     *   4. URL con protocolo sospechoso
     *
     * @return true|string True si es válido, string con error si no
     */
    private function validatePayload(array $data): bool|string
    {
        // Campos requeridos
        $required = ['visitor_id', 'session_id', 'event', 'url', 'path'];
        foreach ($required as $field) {
            if (empty($data[$field]) || !is_string($data[$field])) {
                return "Missing required field: {$field}";
            }
        }

        // Validar tipo de evento
        if (!in_array($data['event'], self::ALLOWED_EVENTS, true)) {
            return 'Invalid event type';
        }

        // Validar formato UUID (v4) para visitor_id y session_id
        $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        if (!preg_match($uuidPattern, $data['visitor_id'])) {
            return 'Invalid visitor_id format';
        }
        if (!preg_match($uuidPattern, $data['session_id'])) {
            return 'Invalid session_id format';
        }

        // Validar que la URL no tenga protocolo sospechoso
        $url = $data['url'];
        if (!preg_match('/^https?:\/\//', $url)) {
            return 'Invalid URL protocol';
        }

        return true;
    }

    /**
     * Sanitiza un UUID eliminando caracteres peligrosos.
     */
    private function sanitizeUuid(string $uuid): string
    {
        return preg_replace('/[^0-9a-f\-]/i', '', mb_substr($uuid, 0, 36));
    }

    /**
     * Extrae datos extra según el tipo de evento.
     * Solo permite campos seguros y conocidos.
     */
    private function extractExtra(array $data): array
    {
        $extra = [];
        $allowed = ['duration_seconds', 'button', 'source', 'query', 'error_message'];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                // Sanitizar: solo strings cortos o números
                if (is_numeric($data[$field])) {
                    $extra[$field] = (int) $data[$field];
                } elseif (is_string($data[$field])) {
                    $extra[$field] = mb_substr($data[$field], 0, 255);
                }
            }
        }

        return $extra;
    }

    // ─────────────────────────────────────────────────────────────
    // Persistencia
    // ─────────────────────────────────────────────────────────────

    /**
     * Guarda el evento en la tabla analytics_events.
     *
     * Estrategia de escritura eficiente:
     *   - INSERT INTO directo (no usa ORM para minimizar overhead)
     *   - La tabla usa InnoDB con AUTO_INCREMENT
     *   - Índices sobre (event_type, created_at) para queries de reporte
     *   - Índice sobre (visitor_id) para análisis de usuarios únicos
     *   - Particionamiento por mes se puede agregar cuando la tabla crezca
     *
     * Para alto volumen (>1000 req/s), considerar:
     *   - Batch inserts con Redis como buffer
     *   - Tabla de staging + CRON que mueva a tabla definitiva
     *   - INSERT DELAYED (deprecated en MySQL 8, usar async en su lugar)
     */
    private function saveEvent(array $event): void
    {
        try {
            $db = db_connect();
            $this->ensureAnalyticsTable($db);

            $db->table('analytics_events')->insert($event);
        } catch (\Throwable $e) {
            // Nunca romper la experiencia del usuario por un error de analítica
            log_message('error', 'Analytics insert failed: ' . $e->getMessage());
        }
    }

    /**
     * Crea la tabla analytics_events si no existe (idempotente).
     *
     * Diseño de la tabla:
     * ┌─────────────────────────────────────────────────────────────╖
     * │ id           │ BIGINT AUTO_INCREMENT (soporta billones)    │
     * │ visitor_id   │ UUID del visitante (cookie bx_vid)          │
     * │ session_id   │ UUID de la sesión actual                    │
     * │ event_type   │ pageview, engagement, click_cta, etc.       │
     * │ url          │ URL completa de la página visitada           │
     * │ path         │ Solo el pathname (para agrupar)              │
     * │ referrer     │ De dónde viene el usuario                    │
     * │ title        │ <title> de la página                         │
     * │ screen       │ Resolución: "1920x1080"                     │
     * │ viewport     │ Viewport: "1366x768"                        │
     * │ device_type  │ mobile / tablet / desktop                    │
     * │ language     │ Idioma del navegador: "es-CO"                │
     * │ browser      │ Nombre del navegador                         │
     * │ platform     │ Sistema operativo                            │
     * │ ip_anon      │ IP anonimizada: "192.168.1.0"               │
     * │ extra_json   │ Datos extra del evento (JSON nullable)       │
     * │ created_at   │ Timestamp del evento                         │
     * ╘═════════════════════════════════════════════════════════════╛
     */
    private function ensureAnalyticsTable($db): void
    {
        $db->query("
            CREATE TABLE IF NOT EXISTS analytics_events (
                id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                visitor_id  CHAR(36)     NOT NULL,
                session_id  CHAR(36)     NOT NULL,
                event_type  VARCHAR(50)  NOT NULL,
                url         VARCHAR(2048) DEFAULT NULL,
                path        VARCHAR(512)  DEFAULT NULL,
                referrer    VARCHAR(2048) DEFAULT NULL,
                title       VARCHAR(512)  DEFAULT NULL,
                screen      VARCHAR(20)   DEFAULT NULL,
                viewport    VARCHAR(20)   DEFAULT NULL,
                device_type VARCHAR(10)   DEFAULT 'unknown',
                language    VARCHAR(10)   DEFAULT NULL,
                browser     VARCHAR(100)  DEFAULT NULL,
                platform    VARCHAR(100)  DEFAULT NULL,
                ip_anon     VARCHAR(45)   NOT NULL,
                extra_json  JSON          DEFAULT NULL,
                created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

                INDEX idx_event_date   (event_type, created_at),
                INDEX idx_visitor      (visitor_id),
                INDEX idx_session      (session_id),
                INDEX idx_path         (path(100)),
                INDEX idx_created      (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Dashboard de Analíticas
     * GET /analytics/dashboard
     *
     * Muestra una página con métricas visuales de los datos recolectados.
     */
    public function dashboard()
    {
        // Protección: Solo usuarios admin pueden ver el dashboard de analíticas
        $user = session()->get('user');
        if (!$user || ($user['rol'] ?? '') !== 'admin') {
            return redirect()->to('/login')
                ->with('login_error', 'Solo los administradores pueden acceder al dashboard de analíticas.');
        }

        // Obtener período seleccionado (por defecto últimos 30 días)
        $days = (int) ($this->request->getGet('days') ?? 30);
        $days = max(1, min(365, $days)); // Entre 1 y 365 días

        $data = [
            'title' => 'Dashboard de Analíticas',
            'days' => $days,
            'stats' => $this->getStats($days),
            'pageviews' => $this->getPageviews($days),
            'popularPages' => $this->getPopularPages($days),
            'deviceBreakdown' => $this->getDeviceBreakdown($days),
            'events' => $this->getEvents($days),
            'browsers' => $this->getBrowsers($days),
        ];

        return view('analytics/dashboard', $data);
    }

    /**
     * Obtiene estadísticas generales
     */
    private function getStats(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // Visitantes únicos
        $uniqueVisitors = $db->table('analytics_events')
            ->selectCount('DISTINCT visitor_id', 'count')
            ->where('created_at >=', $sinceDate)
            ->get()
            ->getRow()
            ->count ?? 0;

        // Sesiones únicas
        $uniqueSessions = $db->table('analytics_events')
            ->selectCount('DISTINCT session_id', 'count')
            ->where('created_at >=', $sinceDate)
            ->get()
            ->getRow()
            ->count ?? 0;

        // Páginas vistas (eventos pageview)
        $pageviews = $db->table('analytics_events')
            ->selectCount('id', 'count')
            ->where('event_type', 'pageview')
            ->where('created_at >=', $sinceDate)
            ->get()
            ->getRow()
            ->count ?? 0;

        // Duración promedio (eventos engagement)
        $avgDuration = $db->query("
            SELECT AVG(JSON_EXTRACT(extra_json, '$.duration_seconds')) as avg_duration
            FROM analytics_events
            WHERE event_type = 'engagement'
            AND created_at >= ?
            AND extra_json IS NOT NULL
        ", [$sinceDate])->getRow()->avg_duration ?? 0;

        return [
            'unique_visitors' => (int) $uniqueVisitors,
            'unique_sessions' => (int) $uniqueSessions,
            'pageviews' => (int) $pageviews,
            'avg_duration' => round((float) $avgDuration, 1),
        ];
    }

    /**
     * Obtiene páginas vistas por día (para gráfico)
     */
    private function getPageviews(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as views
            FROM analytics_events
            WHERE event_type = 'pageview'
            AND created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$sinceDate])->getResultArray();

        return $results;
    }

    /**
     * Obtiene las páginas más populares
     */
    private function getPopularPages(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $db->query("
            SELECT 
                path,
                title,
                COUNT(*) as views,
                COUNT(DISTINCT visitor_id) as unique_visitors
            FROM analytics_events
            WHERE event_type = 'pageview'
            AND created_at >= ?
            AND path IS NOT NULL
            GROUP BY path, title
            ORDER BY views DESC
            LIMIT 20
        ", [$sinceDate])->getResultArray();

        return $results;
    }

    /**
     * Obtiene distribución por tipo de dispositivo
     */
    private function getDeviceBreakdown(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $db->query("
            SELECT 
                device_type,
                COUNT(*) as count,
                (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM analytics_events WHERE created_at >= ?)) as percentage
            FROM analytics_events
            WHERE created_at >= ?
            GROUP BY device_type
            ORDER BY count DESC
        ", [$sinceDate, $sinceDate])->getResultArray();

        return $results;
    }

    /**
     * Obtiene eventos personalizados más frecuentes
     */
    private function getEvents(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $db->query("
            SELECT 
                event_type,
                COUNT(*) as count
            FROM analytics_events
            WHERE created_at >= ?
            AND event_type NOT IN ('pageview', 'engagement')
            GROUP BY event_type
            ORDER BY count DESC
            LIMIT 10
        ", [$sinceDate])->getResultArray();

        return $results;
    }

    /**
     * Obtiene distribución por navegador
     */
    private function getBrowsers(int $days): array
    {
        $db = \Config\Database::connect();
        $sinceDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $db->query("
            SELECT 
                COALESCE(browser, 'Desconocido') as browser,
                COUNT(*) as count,
                (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM analytics_events WHERE created_at >= ?)) as percentage
            FROM analytics_events
            WHERE created_at >= ?
            GROUP BY browser
            ORDER BY count DESC
            LIMIT 10
        ", [$sinceDate, $sinceDate])->getResultArray();

        return $results;
    }
}
