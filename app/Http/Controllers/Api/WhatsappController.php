<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsappController extends Controller
{
    public function templates(Request $request)
    {
        try {
            $svc = app(\App\Services\DepWhatsappService::class);
            $res = $svc->getTemplates();

            return response()->json($res, 200);
        } catch (\Throwable $e) {
            Log::error('WA templates proxy error', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Proxy endpoint to send a WhatsApp template or text via server credentials.
     * POST /api/wa/send
     * Body JSON: { phone, template_id?, template_name?, language?, params?: [] , text? }
     */
    public function send(Request $request)
    {
        $data = $request->only(['phone', 'template_id', 'template_name', 'language', 'params', 'text']);

        $validator = Validator::make($data, [
            'phone' => 'required|string',
            'template_id' => 'nullable|string',
            'template_name' => 'nullable|string',
            'language' => 'nullable|string',
            'params' => 'nullable|array',
            'text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
        }

        try {
            $svc = app(\App\Services\DepWhatsappService::class);

            if (!empty($data['text']) && empty($data['template_id']) && empty($data['template_name'])) {
                $res = $svc->sendText($data['phone'], $data['text']);
            } elseif (!empty($data['template_id'])) {
                // map legacy template_id to provider template name when known
                $mapped = $this->mapTemplateIdToName($data['template_id']);
                if ($mapped) {
                    $res = $svc->sendTemplateByName(
                        $data['phone'],
                        $mapped,
                        $data['language'] ?? 'id',
                        $data['params'] ?? []
                    );
                } else {
                    // fallback to id-based call for backward compatibility
                    $res = $svc->sendTemplateById(
                        $data['phone'],
                        $data['template_id'],
                        $data['language'] ?? 'id',
                        $data['params'] ?? []
                    );
                }
            } elseif (!empty($data['template_name'])) {
                $res = $svc->sendTemplateByName(
                    $data['phone'],
                    $data['template_name'],
                    $data['language'] ?? 'id',
                    $data['params'] ?? []
                );
            } else {
                return response()->json(['error' => 'Either text or template_id/template_name required'], 400);
            }

            return response()->json($res, 200);
        } catch (\Throwable $e) {
            Log::error('WA send proxy error', ['message' => $e->getMessage(), 'payload' => $data]);
            return response()->json(['error' => 'Internal server error', 'message' => $e->getMessage()], 500);
        }
    }

    private function mapTemplateIdToName(string $id): ?string
    {
        $map = [
            '1557389545282102' => 'notifikasi_tiket_open',
            '734693812466588' => 'notifikasi_tiket_close',
        ];

        return $map[$id] ?? null;
    }

}
