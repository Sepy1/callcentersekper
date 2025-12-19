<?php

namespace App\Http\Controllers;

use App\Services\DepWhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function send(Request $request, DepWhatsappService $service)
{
    \Log::info('REQUEST MASUK', $request->all());

    $request->validate([
        'phone'   => 'required',
        'message' => 'required|string|max:700',
    ]);

    $result = $service->sendText(
        $request->phone,
        $request->message
    );

    \Log::info('HASIL DEP', $result);

    // If the service returned a diagnostic array with an explicit HTTP status
    // and raw body (for non-JSON responses), forward that to the caller.
    if (is_array($result) && isset($result['status']) && isset($result['body_raw'])) {
        $status = (int) $result['status'];
        $bodyRaw = $result['body_raw'];

        // Try to set Content-Type from headers if provided, otherwise default
        $contentType = 'text/plain';
        if (!empty($result['headers']) && is_array($result['headers'])) {
            $headers = $result['headers'];
            if (isset($headers['Content-Type'][0])) {
                $contentType = $headers['Content-Type'][0];
            } elseif (isset($headers['content-type'][0])) {
                $contentType = $headers['content-type'][0];
            }
        }

        return response($bodyRaw, $status)
            ->header('Content-Type', $contentType);
    }

    // Default: return JSON (successful parsed DEP response or diagnostic array)
    return response()->json($result);
}
}
