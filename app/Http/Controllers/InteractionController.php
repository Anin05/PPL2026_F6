<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interaction;
use Carbon\Carbon;

class InteractionController extends Controller
{
    public function index($clientId)
    {
        $interactions = Interaction::where('client_id', $clientId)
            ->orderBy('interaction_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $interactions
        ], 200);
    }

    public function store(Request $request, $clientId)
    {
        $request->validate([
            'interaction_at' => 'required|date',
            'type' => 'required|string',
            'notes' => 'required|string',
        ]);

        $interaction = Interaction::create([
            'client_id' => $clientId,
            'interaction_at' => Carbon::parse($request->interaction_at)->timezone('UTC'),
            'type' => $request->type,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Interaksi berhasil dicatat',
            'data' => $interaction
        ], 201);
    }
}
