@if($data->purchase_date && $data->warranty_expired)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle-outline me-1"></i>
        <strong>Uwaga!</strong> Termin bezpłatnej regulacji (18 miesięcy od daty zakupu) minął
        <strong>{{ $data->warranty_days_overdue }} {{ $data->warranty_days_overdue == 1 ? 'dzień' : ($data->warranty_days_overdue < 5 ? 'dni' : 'dni') }}</strong> temu.
        <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-bs-toggle="modal" data-bs-target="#reject-email-modal">
            <i class="mdi mdi-email"></i> Wyślij e-mail o odrzuceniu
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    {{-- Reject email modal --}}
    <div class="modal fade" id="reject-email-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{custom_route('reclamation-send-email', ['id' => $data->getKey()])}}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Wyślij e-mail o odrzuceniu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Adres e-mail</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ $data->client?->email ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Szablon</label>
                            <select name="template_id" class="form-select" id="reject-template-select">
                                @foreach(\App\Models\EmailTemplate::where('name', 'like', '%odrzuceni%')->orWhere('name', 'like', '%Odrzuceni%')->get() as $template)
                                    <option value="{{ $template->getKey() }}">{{ $template->name }}</option>
                                @endforeach
                                @foreach(\App\Models\EmailTemplate::where('name', 'not like', '%odrzuceni%')->where('name', 'not like', '%Odrzuceni%')->get() as $template)
                                    <option value="{{ $template->getKey() }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-danger">Wyślij e-mail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif($data->purchase_date && !$data->warranty_expired)
    @php
        $expiryDate = \Carbon\Carbon::parse($data->purchase_date)->addMonths(18);
        $daysLeft = (int) now()->diffInDays($expiryDate, false);
    @endphp
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i>
        Gwarancja aktywna. Do końca terminu bezpłatnej regulacji pozostało <strong>{{ $daysLeft }}</strong> dni
        (do {{ $expiryDate->format('d-m-Y') }}).
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
