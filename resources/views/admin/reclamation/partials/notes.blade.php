<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3"><i class="mdi mdi-message-text-outline me-1"></i> Notatki i historia zdarzeń</h4>

        {{-- Add note form --}}
        <form action="{{custom_route('reclamation-note', ['id' => $data->getKey()])}}" method="POST" class="mb-4">
            @csrf
            <div class="mb-2">
                <textarea name="content" class="form-control" rows="3" placeholder="Dodaj notatkę..." required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus"></i> Dodaj notatkę
            </button>
        </form>

        {{-- Notes timeline --}}
        @if($data->notes->count())
            <div class="timeline-alt pb-0">
                @foreach($data->notes as $note)
                    <div class="timeline-item">
                        <i class="mdi {{ match($note->type) {
                            'auto_status_change' => 'mdi-swap-horizontal bg-info-lighten text-info',
                            'auto_email_sent' => 'mdi-email bg-warning-lighten text-warning',
                            'auto_created' => 'mdi-plus bg-success-lighten text-success',
                            default => 'mdi-message-text bg-primary-lighten text-primary',
                        } }} timeline-icon"></i>
                        <div class="timeline-item-info">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">{{ $note->getTypeLabel() }}</span>
                                <small class="text-muted">{{ $note->created_at->format('d-m-Y H:i') }}</small>
                            </div>
                            <p class="mb-1">{{ $note->content }}</p>
                            @if($note->user)
                                <small class="text-muted">{{ $note->user->name ?? $note->user->email }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">Brak notatek.</p>
        @endif
    </div>
</div>
