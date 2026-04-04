<?php

namespace App\Services;

use App\Models\ReclamationNote;

class ReclamationLogger
{
    public static function log(int $reclamationId, string $type, string $content): ReclamationNote
    {
        return ReclamationNote::create([
            'reclamation_id' => $reclamationId,
            'user_id' => auth()->id(),
            'type' => $type,
            'content' => $content,
        ]);
    }
}
