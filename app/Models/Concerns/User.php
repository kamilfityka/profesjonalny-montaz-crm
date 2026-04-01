<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int user_id
 * @property \App\Models\User user
 */
trait User
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
