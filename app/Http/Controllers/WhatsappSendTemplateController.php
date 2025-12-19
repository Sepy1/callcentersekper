<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DepWhatsappService;

class WhatsappSendTemplateController extends Controller
{
    protected DepWhatsappService $dep;

    public function __construct(DepWhatsappService $dep)
    {
        $this->dep = $dep;
    }

    /**
     * SEND WHATSAPP TEMPLATE BY TEMPLATE_ID
     *
     * Body JSON:
     * {
     *   "phone": "6285xxxxxxx",
     *   "template_id": "1557389545282102",
     *   "language": "id",
     *   "params": ["Erik", "TCK-001"]
     * }
     */
    public function sendTemplate(Request $request)
    {
        $validated = $request->validate([
            'phone'       => 'required|string',
            'template_id' => 'required|string',
            'language'    => 'required|string',
            'params'      => 'nullable|array',
        ]);

        Log::info('SEND TEMPLATE REQUEST', $validated);

        $result = $this->dep->sendTemplateById(
            $validated['phone'],
            $validated['template_id'],
            $validated['language'],
            $validated['params'] ?? []
        );

        Log::info('SEND TEMPLATE RESPONSE', $result);

        return response()->json($result);
    }
}
