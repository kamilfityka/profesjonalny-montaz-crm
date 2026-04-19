@php
    /** @var \App\Models\Reclamation $reclamation */
    $warrantyStatus = app(\App\Actions\CheckWarrantyDeadline::class)->execute($reclamation);
@endphp

@if($warrantyStatus->isExpired())
    <div class="alert alert-danger d-flex align-items-center justify-content-between">
        <div>
            <strong>Termin bezpłatnej regulacji minął {{ $warrantyStatus->overdueDays }} dni temu.</strong>
            <small class="d-block text-muted">Okres 18 miesięcy od daty zakupu został przekroczony.</small>
        </div>
        {{-- TODO: po wydaniu base z B3 (szablony e-maili) podmienić na partial wysyłki szablonu rejection_warranty_expired --}}
        <button type="button" class="btn btn-outline-danger btn-sm" disabled
                title="Wymaga wdrożenia szablonów e-maili (Etap 7)">
            Zatwierdź e-mail odrzucenia
        </button>
    </div>
@elseif($warrantyStatus->isActive())
    <div class="alert alert-info">
        Pozostało <strong>{{ $warrantyStatus->remainingDays }}</strong> dni bezpłatnej regulacji.
    </div>
@endif
