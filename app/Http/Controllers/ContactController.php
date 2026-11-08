<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Client;

class ContactController extends Controller
{
    public function index(Request $request, $clientId)
    {
        $query = Contact::where('client_id', $clientId);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ], 200);
    }

    public function store(Request $request, $clientId)
    {
        $request->validate([
            'type' => 'required|in:PIC,Direktur',
            'name' => 'required|string',
            'position' => 'required|string',
            'phone' => 'required|string',
        ]);

        $contact = Contact::create(array_merge(
            $request->all(),
            ['client_id' => $clientId, 'status' => 'active']
        ));

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil ditambahkan',
            'data' => $contact
        ], 201);
    }

    public function update(Request $request, $clientId, $contactId)
    {
        $contact = Contact::where('client_id', $clientId)->where('id', $contactId)->first();
        if (!$contact) {
            return response()->json(['error' => ['code' => 'NOT_FOUND', 'message' => 'Kontak tidak ditemukan']], 404);
        }

        $contact->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diperbarui',
            'data' => $contact
        ], 200);
    }

    public function archiveStatus($clientId, $contactId)
    {
        $contact = Contact::where('client_id', $clientId)->where('id', $contactId)->first();
        if (!$contact) {
            return response()->json(['error' => ['code' => 'NOT_FOUND', 'message' => 'Kontak tidak ditemukan']], 404);
        }

        $contact->update(['status' => 'archived']); // Soft-delete arsip sesuai aturan Q-09[cite: 47]
        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diarsipkan'
        ], 200);
    }
}
