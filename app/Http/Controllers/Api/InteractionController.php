<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Interaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InteractionController extends Controller
{
    // GET /api/v1/interactions (Global daftar interaksi)
    public function indexAll(Request $request): JsonResponse
    {
        $query = Interaction::with(['client']);

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($builder) use ($search) {
                $builder->where('notes', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('client_id')) {
            $clientId = $request->query('client_id');
            $query->where(function ($q) use ($clientId) {
                $q->where('client_id', $clientId)
                    ->orWhereHas('client', function ($cq) use ($clientId) {
                        $cq->where('code', $clientId);
                    });
            });
        }

        if ($request->filled('type')) {
            $type = $request->query('type');
            if ($type === 'Rapat') {
                $query->whereIn('type', ['Rapat', 'Meeting']);
            } else {
                $query->where('type', $type);
            }
        }

        if ($request->query('tab') === 'tl') {
            $query->where(function ($q) {
                $q->where('notes', 'like', '%tindak lanjut%')
                    ->orWhere('interaction_at', '>=', now()->startOfDay());
            });
        }

        $allCount = Interaction::count();
        $tlCount = Interaction::where(function ($q) {
            $q->where('notes', 'like', '%tindak lanjut%')
                ->orWhere('interaction_at', '>=', now()->startOfDay());
        })->count();

        $limit = min(
            max((int) $request->query('limit', 10), 1),
            100
        );

        $interactions = $query->orderByDesc('interaction_at')->paginate($limit);

        $formattedData = collect($interactions->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'client_id' => $item->client_id,
                'type' => $item->type,
                'notes' => $item->notes,
                'interaction_at' => $item->interaction_at?->format('Y-m-d H:i:s'),
                'date_formatted' => $item->interaction_at?->translatedFormat('d M Y') ?? '-',
                'time_formatted' => $item->interaction_at?->format('H:i') ?? '09:00',
                'is_follow_up' => str_contains(strtolower($item->notes), 'tindak lanjut') || ($item->interaction_at && $item->interaction_at->isFuture()),
                'client' => $item->client ? [
                    'id' => $item->client->id,
                    'code' => $item->client->code,
                    'name' => $item->client->name,
                    'city' => $item->client->city,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'meta' => [
                'current_page' => $interactions->currentPage(),
                'last_page' => $interactions->lastPage(),
                'per_page' => $interactions->perPage(),
                'total' => $interactions->total(),
                'stats' => [
                    'all_count' => $allCount,
                    'tl_count' => $tlCount,
                ],
            ],
        ]);
    }

    // POST /api/v1/interactions (Global simpan interaksi)
    public function storeGlobal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'string'],
            'type' => ['required', 'string', 'in:Telepon,WhatsApp,Email,Meeting,Rapat,Kunjungan'],
            'notes' => ['required', 'string'],
            'interaction_at' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data interaksi tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $client = Client::where('id', $request->input('client_id'))
            ->orWhere('code', $request->input('client_id'))
            ->first();

        if (!$client) {
            return response()->json([
                'error' => [
                    'code' => 'CLIENT_NOT_FOUND',
                    'message' => 'Klien tidak ditemukan.',
                    'details' => ['client_id' => ['Klien yang dipilih tidak valid.']],
                ],
            ], 404);
        }

        $type = $request->input('type');
        if ($type === 'Rapat') $type = 'Meeting';

        $interaction = $client->interactions()->create([
            'type' => $type,
            'notes' => $request->input('notes'),
            'interaction_at' => $request->input('interaction_at'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Interaksi berhasil dicatat.',
            'data' => $interaction->load('client'),
        ], 201);
    }

    // GET /api/v1/interactions/{id}
    public function show(string $id): JsonResponse
    {
        $interaction = Interaction::with('client')->find($id);

        if (!$interaction) {
            return response()->json([
                'error' => [
                    'code' => 'INTERACTION_NOT_FOUND',
                    'message' => 'Interaksi tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $interaction->id,
                'client_id' => $interaction->client_id,
                'type' => $interaction->type,
                'notes' => $interaction->notes,
                'interaction_at' => $interaction->interaction_at?->format('Y-m-d H:i:s'),
                'date_formatted' => $interaction->interaction_at?->translatedFormat('d F Y') ?? '-',
                'time_formatted' => $interaction->interaction_at?->format('H:i') ?? '09:00',
                'client' => $interaction->client,
            ],
        ]);
    }

    // DELETE /api/v1/interactions/{id}
    public function destroyGlobal(string $id): JsonResponse
    {
        $interaction = Interaction::find($id);

        if (!$interaction) {
            return response()->json([
                'error' => [
                    'code' => 'INTERACTION_NOT_FOUND',
                    'message' => 'Interaksi tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $interaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan interaksi berhasil dihapus.',
        ]);
    }

    // GET /api/v1/clients/{clientId}/interactions
    public function index(string $clientId): JsonResponse
    {
        $client = Client::where('id', $clientId)->orWhere('code', $clientId)->first();

        if (!$client) {
            return response()->json([
                'error' => [
                    'code' => 'CLIENT_NOT_FOUND',
                    'message' => 'Klien tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $client->interactions()
                ->orderByDesc('interaction_at')
                ->get(),
        ]);
    }

    // POST /api/v1/clients/{clientId}/interactions
    public function store(Request $request, string $clientId): JsonResponse
    {
        $client = Client::where('id', $clientId)->orWhere('code', $clientId)->first();

        if (!$client) {
            return response()->json([
                'error' => [
                    'code' => 'CLIENT_NOT_FOUND',
                    'message' => 'Klien tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                'in:Telepon,WhatsApp,Email,Meeting,Rapat,Kunjungan',
            ],
            'notes' => ['required', 'string'],
            'interaction_at' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data interaksi tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $type = $request->input('type');
        if ($type === 'Rapat') $type = 'Meeting';

        $interaction = $client->interactions()->create([
            'type' => $type,
            'notes' => $request->input('notes'),
            'interaction_at' => $request->input('interaction_at'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Interaksi berhasil dicatat.',
            'data' => $interaction,
        ], 201);
    }
}
