<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SessionController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'emailOrUsername' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $res = Http::acceptJson()
                ->timeout(90)
                ->post(rtrim(config('services.f1.url'), '/') . '/auth/login', $data);
        } catch (ConnectionException $e) {
            return response()->json(['error' => [
                'code' => 'F1_UNAVAILABLE',
                'message' => 'Server F-1 tidak bisa dihubungi.',
            ]], 502);
        }

        if ($res->status() === 503 || $res->json() === null) {
            return response()->json(['error' => [
                'code' => 'F1_WAKING',
                'message' => 'Server F-1 sedang bangun, coba lagi sekitar 1-2 menit.',
            ]], 503);
        }

        return response()->json($res->json(), $res->status());
    }
}