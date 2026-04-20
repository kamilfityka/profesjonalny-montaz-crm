<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reclamation;
use App\Models\ReclamationCategory;
use App\Services\ReclamationLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReclamationApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'text' => 'required|string',
            'purchase_date' => 'nullable|date',
        ]);

        $client = Client::where('phone', $validated['phone'])
            ->orWhere(function ($query) use ($validated) {
                if (!empty($validated['email'])) {
                    $query->where('email', $validated['email']);
                }
            })
            ->first();

        if (!$client) {
            $client = new Client();
            $client->active = true;
            $client->name = $validated['name'];
            $client->phone = $validated['phone'];
            $client->email = $validated['email'] ?? null;
            $client->save();
        }

        $category = ReclamationCategory::where('name', 'Oczekuje na weryfikację')->first();

        $reclamation = new Reclamation();
        $reclamation->active = true;
        $reclamation->name = $validated['name'];
        $reclamation->phone = $validated['phone'];
        $reclamation->address = $validated['address'] ?? null;
        $reclamation->text = $validated['text'];
        $reclamation->purchase_date = $validated['purchase_date'] ?? null;
        $reclamation->priority = 'PRIORITY_NORMAL';
        $reclamation->urgency = 'Niepilne';
        $reclamation->client()->associate($client);

        if ($category) {
            $reclamation->category()->associate($category);
        }

        $reclamation->save();

        ReclamationLogger::log($reclamation->getKey(), 'auto_created', 'Zgłoszenie utworzone z formularza WordPress');

        return response()->json([
            'success' => true,
            'id' => $reclamation->getKey(),
            'message' => 'Zgłoszenie zostało utworzone.',
        ], 201);
    }
}
