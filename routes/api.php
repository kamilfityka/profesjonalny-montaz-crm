<?php

use App\Http\Controllers\Api\ReclamationApiController;
use App\Http\Middleware\VerifyApiToken;

app('router')->group(['middleware' => ['api', VerifyApiToken::class]], function () {
    app('router')->post('/reclamation', [ReclamationApiController::class, 'store'])->middleware('throttle:10,1');
});
