<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiRequestLog;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    public function handle(Request $request, Closure $next)
    {
        // only log JSON/API traffic; safe to log all under this middleware
        $token = $request->attributes->get('api_token');

        $log = ApiRequestLog::create([
            'api_token_id' => $token->id ?? null,
            'method' => $request->method(),
            'path' => $request->path(),
            'headers' => json_encode($this->filterHeaders($request->headers->all())),
            'request_body' => $request->getContent(),
            'ip' => $request->ip(),
        ]);

        try {
            $response = $next($request);

            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
            $content = null;
            try {
                $content = method_exists($response, 'getContent') ? $response->getContent() : null;
            } catch (\Throwable $e) {
                $content = null;
            }

            $log->update([
                'response_status' => $status,
                'response_body' => $content,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $log->update(['response_status' => 500, 'response_body' => $e->getMessage()]);
            Log::error('LogApiRequest error', ['exception' => $e->getMessage()]);
            throw $e;
        }
    }

    private function filterHeaders(array $headers)
    {
        // avoid logging sensitive headers fully; mask Authorization
        $out = [];
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'authorization') {
                $out[$k] = ['[REDACTED]'];
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
