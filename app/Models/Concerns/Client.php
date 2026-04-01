<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int client_id
 * @property \App\Models\Client client
 */
trait Client
{
    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
}
