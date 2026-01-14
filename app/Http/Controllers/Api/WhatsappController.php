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
                $res = $svc->sendTemplateById(
                    $data['phone'],
                    $data['template_id'],
                    $data['language'] ?? 'id',
                    $data['params'] ?? []
                );
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
}
