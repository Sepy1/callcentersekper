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

        // If token has allowed_ips configured, enforce client IP check
        if (!empty($found->allowed_ips) && is_array($found->allowed_ips)) {
            $clientIp = $request->ip();
            $allowed = false;
            foreach ($found->allowed_ips as $rule) {
                $rule = trim($rule);
                if ($rule === '') {
                    continue;
                }
                if (strpos($rule, '/') !== false) {
                    if ($this->ipInCidr($clientIp, $rule)) {
                        $allowed = true;
                        break;
                    }
                } else {
                    if ($clientIp === $rule) {
                        $allowed = true;
                        break;
                    }
                }
            }

            if (!$allowed) {
                return response()->json(['error' => 'IP not allowed'], 403);
            }
        }

        // optionally, attach token model to request
        $request->attributes->set('api_token', $found);

        return $next($request);
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        [$network, $prefix] = explode('/', $cidr);
        $prefix = (int) $prefix;

        $ipDec = ip2long($ip);
        $networkDec = ip2long($network);

        if ($ipDec === false || $networkDec === false) {
            return false;
        }

        $mask = -1 << (32 - $prefix);

        return (($ipDec & $mask) === ($networkDec & $mask));
    }
}
