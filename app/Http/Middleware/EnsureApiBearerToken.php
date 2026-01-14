<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ApiToken;

class EnsureApiBearerToken
{
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->header('Authorization') ?? $request->bearerToken();

        if (!$auth) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (str_starts_with($auth, 'Bearer ')) {
            $token = substr($auth, 7);
        } else {
            $token = $auth;
        }

        $found = ApiToken::where('token', $token)->first();

        if (!$found) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // optionally, attach token model to request
        $request->attributes->set('api_token', $found);

        return $next($request);
    }
}
