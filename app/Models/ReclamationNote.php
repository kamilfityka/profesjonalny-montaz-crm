<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamationNote extends Model
{
    protected $fillable = ['reclamation_id', 'user_id', 'type', 'content'];

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAuto(): bool
    {
        return str_starts_with($this->type, 'auto_');
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'manual' => 'Notatka',
            'auto_status_change' => 'Zmiana statusu',
            'auto_email_sent' => 'Wysłano e-mail',
            'auto_created' => 'Utworzono zgłoszenie',
            default => $this->type,
        };
    }
}
