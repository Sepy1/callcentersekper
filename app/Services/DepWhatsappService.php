<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DepWhatsappService
{
    protected string $endpoint;
    protected ?string $token;
    protected int $timeout;

    public function __construct()
    {
        $this->endpoint = config('services.mahadata.endpoint', 'https://messaging.mahadata.io/v1/116214948217846/messages');
        $this->token = config('services.mahadata.token');
        $this->timeout = (int) config('services.mahadata.timeout', 15);
    }

    private function postMahadata(array $payload): array
    {
        Log::info('MAHADATA REQ', $payload);

        $client = Http::timeout($this->timeout);
        if (!empty($this->token)) {
            $client = $client->withToken($this->token);
        }

        $response = $client->post($this->endpoint, $payload);

        $body = null;
        try { $body = $response->json(); } catch (\Throwable $e) { $body = null; }

        Log::info('MAHADATA RES', ['status' => $response->status(), 'body' => $body ?? $response->body()]);

        $response->throw();

        if (!is_array($body)) {
            return [
                'status' => $response->status(),
                'body_raw' => $response->body(),
                'headers' => $response->headers(),
            ];
        }

        return [
            'status' => $response->status(),
            'body' => $body,
        ];
    }

    public function sendText(string $phone, string $message): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [ 'body' => $message ],
        ];

        return $this->postMahadata($payload);
    }

    public function sendTemplateByName(string $phone, string $templateName, string $language = 'id', array $params = [], ?string $buttonUrlParameter = null): array
    {
        $parameters = array_map(fn($p) => ['type' => 'text', 'text' => (string)$p], array_values($params));

        $components = [
            [
                'type' => 'body',
                'parameters' => $parameters,
            ],
        ];

        if ($buttonUrlParameter !== null && $buttonUrlParameter !== '') {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => [
                    ['type' => 'text', 'text' => $buttonUrlParameter],
                ],
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => $components,
            ]
        ];

        return $this->postMahadata($payload);
    }

    // keep compatibility: treat templateId as template name
    public function sendTemplateById(string $phone, string $templateId, string $language = 'id', array $params = [], ?string $buttonUrlParameter = null): array
    {
        return $this->sendTemplateByName($phone, $templateId, $language, $params, $buttonUrlParameter);
    }

    public function getTemplates(): array
    {
        return [ 'error' => 'not_implemented', 'message' => 'Template inquiry not supported via Mahadata proxy' ];
    }
}
