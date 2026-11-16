<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    // GET /api/v1/contacts (Daftar semua kontak)
    public function indexAll(Request $request): JsonResponse
    {
        $query = Contact::with('client');

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
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
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $totalCount = Contact::count();
        $picCount = Contact::where('type', 'PIC')->count();
        $dirCount = Contact::where('type', 'Direktur')->count();

        $limit = min(
            max((int) $request->query('limit', 8), 1),
            100
        );

        $contacts = $query->orderBy('name')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $contacts->items(),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
                'stats' => [
                    'total' => $totalCount,
                    'pic_count' => $picCount,
                    'dir_count' => $dirCount,
                ],
            ],
        ]);
    }

    // GET /api/v1/contacts/{contactId}
    public function show(string $contactId): JsonResponse
    {
        $contact = Contact::with('client')->find($contactId);

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $contact,
        ]);
    }

    // POST /api/v1/contacts (Global create kontak)
    public function storeGlobal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:PIC,Direktur'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data kontak tidak valid.',
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

        $contact = $client->contacts()->create([
            'name' => $request->input('name'),
            'position' => $request->input('position'),
            'type' => $request->input('type'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil ditambahkan.',
            'data' => $contact->load('client'),
        ], 201);
    }

    // PUT /api/v1/contacts/{contactId}
    public function updateGlobal(Request $request, string $contactId): JsonResponse
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'client_id' => ['sometimes', 'required', 'string'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:PIC,Direktur'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'status' => ['sometimes', 'in:active,archived'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data kontak tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $data = $validator->validated();

        if (isset($data['client_id'])) {
            $client = Client::where('id', $data['client_id'])
                ->orWhere('code', $data['client_id'])
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
            $data['client_id'] = $client->id;
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diperbarui.',
            'data' => $contact->fresh()->load('client'),
        ]);
    }

    // PATCH /api/v1/contacts/{contactId}/status
    public function updateStatusGlobal(Request $request, string $contactId): JsonResponse
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $newStatus = $request->input('status');
        if (!$newStatus) {
            $newStatus = ($contact->status === 'active') ? 'archived' : 'active';
        }

        $contact->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'active' ? 'Kontak berhasil diaktifkan.' : 'Kontak berhasil diarsipkan.',
            'data' => $contact->fresh()->load('client'),
        ]);
    }

    // DELETE /api/v1/contacts/{contactId}
    public function destroyGlobal(string $contactId): JsonResponse
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil dihapus.',
        ]);
    }

    // GET /api/v1/clients/{clientId}/contacts
    public function index(Request $request, string $clientId): JsonResponse
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

        $query = $client->contacts();

        if ($request->filled('jenis')) {
            $query->where('type', $request->query('jenis'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('name')->get(),
        ]);
    }

    // POST /api/v1/clients/{clientId}/contacts
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
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:PIC,Direktur'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data kontak tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $contact = $client->contacts()->create([
            ...$validator->validated(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil ditambahkan.',
            'data' => $contact,
        ], 201);
    }

    // PUT /api/v1/clients/{clientId}/contacts/{contactId}
    public function update(
        Request $request,
        string $clientId,
        string $contactId
    ): JsonResponse {
        $contact = Contact::where(function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
                ->orWhereHas('client', function ($cq) use ($clientId) {
                    $cq->where('code', $clientId);
                });
        })
            ->where('id', $contactId)
            ->first();

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:PIC,Direktur'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Data kontak tidak valid.',
                    'details' => $validator->errors(),
                ],
            ], 400);
        }

        $contact->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diperbarui.',
            'data' => $contact->fresh(),
        ]);
    }

    // PATCH /api/v1/clients/{clientId}/contacts/{contactId}/status
    public function updateStatus(
        Request $request,
        string $clientId,
        string $contactId
    ): JsonResponse {
        $contact = Contact::where(function ($q) use ($clientId) {
            $q->where('client_id', $clientId)
                ->orWhereHas('client', function ($cq) use ($clientId) {
                    $cq->where('code', $clientId);
                });
        })
            ->where('id', $contactId)
            ->first();

        if (!$contact) {
            return response()->json([
                'error' => [
                    'code' => 'CONTACT_NOT_FOUND',
                    'message' => 'Kontak tidak ditemukan.',
                    'details' => null,
                ],
            ], 404);
        }

        $newStatus = $request->input('status');
        if (!$newStatus) {
            $newStatus = ($contact->status === 'active') ? 'archived' : 'active';
        }

        $contact->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'active' ? 'Kontak berhasil diaktifkan.' : 'Kontak berhasil diarsipkan.',
            'data' => $contact->fresh(),
        ]);
    }
}
