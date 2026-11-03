<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Interaction;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $totalContacts = Contact::where('status', 'active')->count();
        $picCount = Contact::where('type', 'PIC')->where('status', 'active')->count();
        $dirCount = Contact::where('type', 'Direktur')->where('status', 'active')->count();

        $followUps = Interaction::where(function ($q) {
            $q->where('interaction_at', '>=', now())
                ->orWhere('notes', 'like', '%tindak lanjut%');
        })->count();

        $recentInteractions = Interaction::with('client')
            ->orderByDesc('interaction_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'notes' => $item->notes,
                    'interaction_at' => $item->interaction_at?->format('Y-m-d H:i:s'),
                    'formatted_date' => $item->interaction_at?->translatedFormat('d M Y') ?? '-',
                    'client' => $item->client ? [
                        'id' => $item->client->id,
                        'code' => $item->client->code,
                        'name' => $item->client->name,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'archived_clients' => $totalClients - $activeClients,
                'total_contacts' => $totalContacts,
                'pic_count' => $picCount,
                'dir_count' => $dirCount,
                'follow_ups' => $followUps,
                'recent_interactions' => $recentInteractions,
            ],
        ]);
    }
}

