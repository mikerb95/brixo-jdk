<?php

namespace App\Libraries;

/**
 * LlmService – Wrapper para APIs de LLM (Anthropic Claude / OpenAI / Groq).
 *
 * Lee las claves desde .env:
 *   LLM_PROVIDER   = anthropic | openai | groq
 *   LLM_API_KEY    = sk-... | gsk_...
 *   LLM_MODEL      = claude-sonnet-4-20250514 | gpt-4o-mini | llama-3.3-70b-versatile  (opcional)
 */
class LlmService
{
    /** @var string 'anthropic' | 'openai' | 'groq' */
    private string $provider;

    /** @var string API key */
    private string $apiKey;

    /** @var string Modelo a utilizar */
    private string $model;

    /** Prompt de sistema estricto para cotizaciones */
    private const SYSTEM_PROMPT = <<<'PROMPT'
Eres un asistente experto en construcción, remodelación y servicios del hogar.
Tu ÚNICA función es generar cotizaciones desglosadas a partir de la descripción
que proporciona el usuario.

REGLAS ESTRICTAS:
1. Responde ÚNICAMENTE con un objeto JSON válido. Sin texto antes ni después.
2. No uses bloques de código Markdown (```). Solo JSON puro.
3. El JSON DEBE ajustarse exactamente al siguiente esquema:

{
  "servicio_principal": "string – nombre corto del servicio",
  "materiales": [
    { "nombre": "string", "cantidad_estimada": "string con unidad" }
  ],
  "personal": [
    { "rol": "string – p.ej. Plomero, Albañil", "horas_estimadas": number }
  ],
  "complejidad": "bajo | medio | alto"
}

4. Si la descripción es ambigua, haz suposiciones razonables pero NO pidas más datos.
5. Incluye al menos 1 material y 1 rol de personal.
6. La complejidad debe ser exactamente uno de: "bajo", "medio" o "alto".
PROMPT;

    // ----------------------------------------------------------------
    // Constructor
    // ----------------------------------------------------------------

    public function __construct()
    {
        $this->provider = env('LLM_PROVIDER', 'groq');
        $this->apiKey   = env('LLM_API_KEY', '');

        $defaultModel = match ($this->provider) {
            'openai'    => 'gpt-4o-mini',
            'groq'      => 'llama-3.3-70b-versatile',
            default     => 'claude-sonnet-4-20250514',
        };
        $this->model = env('LLM_MODEL', $defaultModel);
    }

    // ----------------------------------------------------------------
    // Método público: genera cotización
    // ----------------------------------------------------------------

    /**
     * Envía la descripción del usuario al LLM y devuelve la cotización como array.
     *
     * @param  string $descripcionUsuario  Texto libre del cliente.
     * @return array{ok: bool, data?: array, error?: string}
     */
    public function generarCotizacion(string $descripcionUsuario): array
    {
        if (empty($this->apiKey)) {
            return $this->error('No se ha configurado LLM_API_KEY en .env');
        }

        $descripcionUsuario = trim($descripcionUsuario);
        if ($descripcionUsuario === '') {
            return $this->error('La descripción del servicio no puede estar vacía.');
        }

        // Llamar al proveedor adecuado
        $raw = match ($this->provider) {
            'openai'    => $this->callOpenAI($descripcionUsuario),
            'anthropic' => $this->callAnthropic($descripcionUsuario),
            'groq'      => $this->callGroq($descripcionUsuario),
            default     => null,
        };

        if ($raw === null) {
            return $this->error("Proveedor LLM no soportado: {$this->provider}");
        }

        if (isset($raw['_error'])) {
            return $this->error($raw['_error']);
        }

        // Extraer el texto de respuesta
        $content = $this->extractContent($raw);
        if ($content === null) {
            log_message('error', '[LlmService] Respuesta inesperada del LLM: ' . json_encode($raw));
            return $this->error('La IA devolvió una respuesta inesperada.');
        }

        // Limpiar posible bloque Markdown ```json ... ```
        $content = $this->limpiarMarkdown($content);

        // Decodificar JSON
        $cotizacion = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', '[LlmService] JSON inválido del LLM: ' . $content);
            return $this->error('La IA no devolvió un JSON válido. Intenta reformular tu solicitud.');
        }

        // Validar esquema mínimo
        $validacion = $this->validarEsquema($cotizacion);
        if ($validacion !== true) {
            log_message('error', '[LlmService] Esquema inválido: ' . $validacion);
            return $this->error("Formato de respuesta incorrecto: {$validacion}");
        }

        return ['ok' => true, 'data' => $cotizacion];
    }

    // ----------------------------------------------------------------
    // Llamadas a APIs
    // ----------------------------------------------------------------

    /**
     * Llama a la API de Anthropic (Messages API v1).
     */
    private function callAnthropic(string $userMessage): ?array
    {
        $url = 'https://api.anthropic.com/v1/messages';

        $payload = [
            'model'      => $this->model,
            'max_tokens' => 1024,
            'system'     => self::SYSTEM_PROMPT,
            'messages'   => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ];

        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ];

        return $this->curlPost($url, $payload, $headers);
    }

    /**
     * Llama a la API de OpenAI (Chat Completions).
     */
    private function callOpenAI(string $userMessage): ?array
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $payload = [
            'model'       => $this->model,
            'max_tokens'  => 1024,
            'temperature' => 0.3,
            'messages'    => [
                ['role' => 'system',  'content' => self::SYSTEM_PROMPT],
                ['role' => 'user',    'content' => $userMessage],
            ],
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        return $this->curlPost($url, $payload, $headers);
    }

    /**
     * Llama a la API de Groq (compatible con formato OpenAI).
     * Usa response_format json_object para garantizar JSON limpio.
     */
    private function callGroq(string $userMessage): ?array
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $payload = [
            'model'           => $this->model,
            'max_tokens'      => 1024,
            'temperature'     => 0.3,
            'response_format' => ['type' => 'json_object'],
            'messages'        => [
                ['role' => 'system',  'content' => self::SYSTEM_PROMPT],
                ['role' => 'user',    'content' => $userMessage],
            ],
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        return $this->curlPost($url, $payload, $headers);
    }

    // ----------------------------------------------------------------
    // Utilidades internas
    // ----------------------------------------------------------------

    /**
     * Ejecuta un POST con cURL y devuelve el body como array.
     */
    private function curlPost(string $url, array $payload, array $headers): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['_error' => "Error de conexión con la API: {$curlError}"];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['_error' => "Respuesta no-JSON de la API (HTTP {$httpCode})."];
        }

        // Manejar errores HTTP del proveedor
        if ($httpCode >= 400) {
            $msg = $decoded['error']['message'] ?? ($decoded['error']['type'] ?? "HTTP {$httpCode}");
            return ['_error' => "Error de la API ({$httpCode}): {$msg}"];
        }

        return $decoded;
    }

    /**
     * Extrae el texto de contenido según el formato de cada proveedor.
     */
    private function extractContent(array $response): ?string
    {
        // Anthropic: { content: [{ type: "text", text: "..." }] }
        if (isset($response['content'][0]['text'])) {
            return $response['content'][0]['text'];
        }

        // OpenAI / Groq: { choices: [{ message: { content: "..." } }] }
        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return null;
    }

    /**
     * Limpia bloques de código Markdown que algunos modelos incluyen.
     */
    private function limpiarMarkdown(string $text): string
    {
        // Quitar ```json ... ``` o ``` ... ```
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/i', '', $text);
        return trim($text);
    }

    /**
     * Valida que el array tenga el esquema esperado.
     *
     * @return true|string  true si es válido, o string con mensaje de error.
     */
    private function validarEsquema(array $data): true|string
    {
        if (empty($data['servicio_principal']) || !is_string($data['servicio_principal'])) {
            return 'Falta "servicio_principal" (string).';
        }

        if (empty($data['materiales']) || !is_array($data['materiales'])) {
            return 'Falta "materiales" (array).';
        }
        foreach ($data['materiales'] as $i => $mat) {
            if (empty($mat['nombre'])) {
                return "materiales[{$i}] no tiene \"nombre\".";
            }
            if (!isset($mat['cantidad_estimada'])) {
                return "materiales[{$i}] no tiene \"cantidad_estimada\".";
            }
        }

        if (empty($data['personal']) || !is_array($data['personal'])) {
            return 'Falta "personal" (array).';
        }
        foreach ($data['personal'] as $i => $per) {
            if (empty($per['rol'])) {
                return "personal[{$i}] no tiene \"rol\".";
            }
            if (!isset($per['horas_estimadas'])) {
                return "personal[{$i}] no tiene \"horas_estimadas\".";
            }
        }

        $validas = ['bajo', 'medio', 'alto'];
        if (empty($data['complejidad']) || !in_array($data['complejidad'], $validas, true)) {
            return '"complejidad" debe ser "bajo", "medio" o "alto".';
        }

        return true;
    }

    /**
     * Genera un array de error estandarizado.
     */
    private function error(string $msg): array
    {
        return ['ok' => false, 'error' => $msg];
    }
}
