<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyF1Token
{
    private const ALLOWED = ['CEO', 'COO', 'ADMIN', 'AM'];

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return $this->fail(401, 'INVALID_TOKEN', 'Token tidak ada.');
        }

        $parts = explode('.', $token);
        $payload = count($parts) === 3
            ? json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true)
            : null;
        $accountId = $payload['accountId'] ?? null;
        if (!$accountId) {
            return $this->fail(401, 'INVALID_TOKEN', 'Token tidak valid.');
        }

        $key = 'f1role:' . hash('sha256', $token);
        $role = Cache::get($key);

        if (!$role) {
            try {
                $res = Http::acceptJson()->withToken($token)->timeout(30)
                    ->get(rtrim(config('services.f1.url'), '/') . "/accounts/{$accountId}/role");
            } catch (ConnectionException $e) {
                return $this->fail(503, 'F1_UNAVAILABLE', 'Server F-1 tidak bisa dihubungi.');
            }
            if (in_array($res->status(), [401, 404])) {
                return $this->fail(401, 'INVALID_TOKEN', 'Token ditolak F-1.');
            }
            if ($res->status() === 403) {
                return $this->fail(403, 'FORBIDDEN', 'Akun tidak aktif atau tidak berwenang.');
            }
            if (!$res->ok()) {
                return $this->fail(503, 'F1_UNAVAILABLE', 'Server F-1 tidak merespons dengan benar.');
            }
            $role = $res->json('roleCode');
            if ($role) {
                Cache::put($key, $role, 60);
            }
        }

        if (!in_array($role, self::ALLOWED, true)) {
            return $this->fail(403, 'FORBIDDEN', 'Peran Anda tidak berwenang mengakses data klien.');
        }

        return $next($request);
    }

    private function fail(int $status, string $code, string $message): Response
    {
        return response()->json(['error' => ['code' => $code, 'message' => $message, 'details' => []]], $status);
    }
}