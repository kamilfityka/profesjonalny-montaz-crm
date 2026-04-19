<?php

use App\Http\Controllers\Api\WpReclamationWebhookController;

app('router')->post('/reclamations/wp-webhook', WpReclamationWebhookController::class)
    ->middleware('wp.webhook.token')
    ->name('api.reclamations.wp-webhook');
