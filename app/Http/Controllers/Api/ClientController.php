<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    // GET /api/v1/clients
    public function index(Request $request): JsonResponse
    {
        $query = Client::withCount('contacts');

        if ($request->filled('q')) {
            $search = $request->query('q');

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->query('city'));
        }

        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $archivedClients = $totalClients - $activeClients;

        $limit = min(
            max((int) $request->query('limit', 10), 1),
            100
        );

        $clients = $query->orderBy('name')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $clients->items(),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
                'stats' => [
                    'total' => $totalClients,
                    'active' => $activeClients,
                    'archived' => $archivedClients,
                ],
            ],
        ]);
    }

    // POST /api/v1/clients
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:clients,code',
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],           // <-- Ditambahkan
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:30'],   // <-- Ditambahkan
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data klien tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $client = Client::create([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'address' => $request->input('address'),       // <-- Ditambahkan
            'city' => $request->input('city'),
            'phone' => $request->input('phone'),
            'mobile' => $request->input('mobile'),         // <-- Ditambahkan
            'email' => $request->input('email'),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Klien berhasil ditambahkan.',
            'data' => $client,
        ], 201);
    }

    // GET /api/v1/clients/{clientId}
    public function show(string $clientId): JsonResponse
    {
        $client = Client::with([
            'contacts',
            'interactions',
        ])
        ->where('id', $clientId)
        ->orWhere('code', $clientId)
        ->first();

        if (!$client) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'data' => $client,
        ]);
    }

    // PUT /api/v1/clients/{clientId}
    public function update(
        Request $request,
        string $clientId
    ): JsonResponse {
// UBAH BARIS INI: Agar PUT bisa menerima UUID maupun KLI-001
    $client = Client::where('id', $clientId)
        ->orWhere('code', $clientId)
        ->first();
        if (!$client) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'unique:clients,code,' . $client->id . ',id',
            ],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'address' => ['sometimes', 'nullable', 'string'],          // <-- Ditambahkan
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'mobile' => ['sometimes', 'nullable', 'string', 'max:30'], // <-- Ditambahkan
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data klien tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $client->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Klien berhasil diperbarui.',
            'data' => $client->fresh(),
        ]);
    }

    // PATCH /api/v1/clients/{clientId}/status
    public function updateStatus(Request $request, string $clientId): JsonResponse
    {
        // Cari berdasarkan 'id' ATAU 'code'
        $client = Client::where('id', $clientId)
            ->orWhere('code', $clientId)
            ->first();

        if (!$client) {
            return $this->notFound();
        }

        $newStatus = $request->input('status');
        if (!$newStatus) {
            $newStatus = ($client->status === 'active') ? 'archived' : 'active';
        }

        $client->update(['status' => $newStatus]);
        $client->contacts()->update(['status' => $newStatus]);

        $message = ($newStatus === 'active')
            ? 'Klien dan kontaknya berhasil dipulihkan.'
            : 'Klien dan kontaknya berhasil diarsipkan.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $client->fresh(),
        ]);
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'CLIENT_NOT_FOUND',
                'message' => 'Klien tidak ditemukan.',
                'details' => null,
            ],
        ], 404);
    }
}