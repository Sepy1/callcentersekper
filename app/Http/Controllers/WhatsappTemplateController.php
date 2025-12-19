<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\DepWhatsappService;

class WhatsappTemplateController extends Controller
{
    protected string $baseUrl;
    protected string $systemId;
    protected DepWhatsappService $depService;

    public function __construct(DepWhatsappService $depService)
    {
        $this->baseUrl   = config('services.dep.base_url');
        $this->systemId  = config('services.dep.system_id');
        $this->depService = $depService;
    }

    /**
     * CREATE WHATSAPP TEMPLATE (IOH)
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'language'      => 'required|string',
            'category'      => 'required|string',
            'body'          => 'required|string',
        ]);

        $endpoint  = '/';
        $op        = 'wa_template_create';
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s') . '.000Z';

        $uniqueId = substr(bin2hex(random_bytes(8)), 0, 16);
        $iv       = substr(bin2hex(random_bytes(8)), 0, 16);

        $payload = [
            'template_name' => $request->template_name,
            'language'      => $request->language,
            'category'      => strtoupper($request->category),
            'components'    => [
                [
                    'type' => 'BODY',
                    'text' => $request->body,
                ],
            ],
        ];

        // === ENCRYPT ===
        $saltedSecret = hash_hmac(
            'sha256',
            config('services.dep.secret_key'),
            config('services.dep.salt')
        );

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
            return response()->json(['error' => 'Encryption failed'], 500);
        }

        $param = base64_encode($encrypted);

        $authToken = hash(
            'sha256',
            $saltedSecret . $uniqueId . $timestamp . $op . $param
        );

        $signature = hash(
            'sha256',
            $endpoint . $timestamp . $saltedSecret
        );

        $body = [
            'system_id'  => $this->systemId,
            'unique_id'  => $uniqueId,
            'op'         => $op,
            'timestamp'  => $timestamp,
            'iv'         => $iv,
            'param'      => $param,
            'auth_token' => $authToken,
        ];

        Log::info('WA TEMPLATE CREATE REQUEST', $body);

        $response = Http::timeout(20)
            ->withHeaders([
                'Content-Type'   => 'application/json',
                'DEP-System-ID'  => $this->systemId,
                'DEP-Timestamp' => $timestamp,
                'DEP-Signature' => $signature,
            ])
            ->post($this->baseUrl . $endpoint, $body);

        $raw = $response->json();

        $decrypted = $this->depService->decryptResponse($raw);

        Log::info('WA TEMPLATE CREATE DECRYPTED', $decrypted);

        return response()->json([
            'http_status' => $response->status(),
            'decrypted'   => $decrypted,
        ], $response->status());
    }
}
