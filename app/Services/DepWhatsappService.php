<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DepWhatsappService
{
    protected string $baseUrl;
    protected string $systemId;
    protected string $secretKey;
    protected string $salt;

    public function __construct()
    {
        $this->baseUrl   = config('services.dep.base_url');
        $this->systemId  = config('services.dep.system_id');
        $this->secretKey = config('services.dep.secret_key');
        $this->salt      = config('services.dep.salt');
    }

    /* =====================================================
     * CORE REQUEST BUILDER
     * ===================================================== */
    private function buildEncryptedRequest(string $op, mixed $payload): array
    {
        $endpoint  = '/';
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s') . '.000Z';
        $uniqueId  = substr(bin2hex(random_bytes(8)), 0, 16);
        $iv        = substr(bin2hex(random_bytes(8)), 0, 16);

        $saltedSecret = hash_hmac('sha256', $this->secretKey, $this->salt);

        $key = hash(
            'sha256',
            $saltedSecret . $uniqueId . $timestamp . $op,
            true
        );

        $encrypted = openssl_encrypt(
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new \RuntimeException('DEP encryption failed: ' . openssl_error_string());
        }

        $param = base64_encode($encrypted);

        return [
            'endpoint' => $endpoint,
            'headers' => [
                'Content-Type'   => 'application/json',
                'DEP-System-ID'  => $this->systemId,
                'DEP-Timestamp' => $timestamp,
                'DEP-Signature' => hash('sha256', $endpoint . $timestamp . $saltedSecret),
            ],
            'body' => [
                'system_id'  => $this->systemId,
                'unique_id'  => $uniqueId,
                'op'         => $op,
                'timestamp'  => $timestamp,
                'iv'         => $iv,
                'param'      => $param,
                'auth_token' => hash(
                    'sha256',
                    $saltedSecret . $uniqueId . $timestamp . $op . $param
                ),
            ],
        ];
    }

    /* =====================================================
     * CORE HTTP SENDER
     * ===================================================== */
    private function sendToDep(string $op, mixed $payload): array
    {
        $req = $this->buildEncryptedRequest($op, $payload);

        Log::info("DEP REQUEST [$op]", $req['body']);

        $response = Http::timeout(config('services.dep.timeout', 15))
            ->withHeaders($req['headers'])
            ->post($this->baseUrl . $req['endpoint'], $req['body']);

        $raw = $response->json();

        Log::info("DEP RESPONSE [$op]", [
            'status' => $response->status(),
            'raw'    => $raw,
        ]);

        if (!is_array($raw)) {
            return [
                'error' => 'Empty or invalid DEP response',
                'http_status' => $response->status(),
                'raw' => $response->body(),
            ];
        }

        if (!isset($raw['param'])) {
    return [
        'status' => 'SUCCESS',
        'raw'    => $raw,
    ];
}

return $this->decryptResponse($raw);
    }

    /* =====================================================
     * PUBLIC API METHODS
     * ===================================================== */

    public function sendText(string $phone, string $message): array
    {
        return $this->sendToDep('send_message', [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phone,
            'type'              => 'text',
            'data' => [
                'preview_url' => false,
                'body'        => $message,
            ],
        ]);
    }

    public function getTemplates(): array
    {
        return $this->sendToDep('wa_template_inquiry', new \stdClass());
    }

    

    /* =====================================================
     * DECRYPT RESPONSE
     * ===================================================== */
    private function decryptResponse(array $response): array
    {
        if (
            empty($response['param']) ||
            empty($response['iv']) ||
            empty($response['unique_id']) ||
            empty($response['timestamp']) ||
            empty($response['op'])
        ) {
            return [
                'error' => 'Invalid DEP response structure',
                'raw'   => $response,
            ];
        }

        $saltedSecret = hash_hmac('sha256', $this->secretKey, $this->salt);

        $key = hash(
            'sha256',
            $saltedSecret
            . $response['unique_id']
            . $response['timestamp']
            . $response['op'],
            true
        );

        $plain = openssl_decrypt(
            base64_decode($response['param']),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $response['iv']
        );

        if ($plain === false) {
            return [
                'error' => 'DEP decrypt failed',
                'openssl_error' => openssl_error_string(),
            ];
        }

        return json_decode($plain, true) ?? [
            'error' => 'DEP decrypted but JSON invalid',
            'plain' => $plain,
        ];
    }


 public function sendTemplateById(
    string $phone,
    string $templateId,
    string $language,
    array $params = []
): array {

    // 1️⃣ inquiry template
    $templates = $this->getTemplates();

    if (!isset($templates['data'])) {
        return [
            'error' => 'Invalid template inquiry',
            'raw' => $templates,
        ];
    }

    $template = collect($templates['data'])
        ->firstWhere('template_id', $templateId);

    if (!$template) {
        return [
            'error' => 'Template ID not found',
            'template_id' => $templateId,
        ];
    }

    $templateName = $template['template_name'];

    // 2️⃣ SEND (HYBRID IOH + WA)
    return $this->sendToDep('broadcast', [
        'template_id' => $templateId, // 🔑 IOH WAJIB
        'data' => [
            [
                'to' => $phone,
                'template' => [
                    'name' => $templateName, // 🔑 WA WAJIB
                    'language' => [
                        'code' => $language
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => array_map(
                                fn ($p) => [
                                    'type' => 'text',
                                    'text' => (string) $p
                                ],
                                $params
                            )
                        ]
                    ]
                ]
            ]
        ]
    ]);
}

public function sendTemplateByName(
    string $phone,
    string $templateName,
    string $language,
    array $params = []
): array {

    return $this->sendToDep('broadcast', [
        'data' => [
            [
                'to' => $phone,
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $language
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => array_map(
                                fn ($p) => [
                                    'type' => 'text',
                                    'text' => (string) $p
                                ],
                                $params
                            )
                        ]
                    ]
                ]
            ]
        ]
    ]);
}
}
