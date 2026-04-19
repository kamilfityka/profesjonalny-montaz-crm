<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateReclamationFromWpForm;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReclamationFromWpRequest;
use Illuminate\Http\JsonResponse;

class WpReclamationWebhookController extends Controller
{
    public function __invoke(
        StoreReclamationFromWpRequest $request,
        CreateReclamationFromWpForm $action
    ): JsonResponse {
        $reclamation = $action->execute($request);

        return response()->json([
            'id' => $reclamation->getKey(),
            'status' => 'created',
        ], 201);
    }
}
