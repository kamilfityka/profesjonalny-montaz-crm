<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3"><i class="mdi mdi-email-outline me-1"></i> Komunikacja e-mail</h4>

        {{-- Send email form --}}
        <div class="mb-4">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#send-email-modal">
                <i class="mdi mdi-email-plus"></i> Wyślij e-mail
            </button>
        </div>

        {{-- Email history --}}
        @php
            $emailNotes = $data->notes->where('type', 'auto_email_sent');
        @endphp
        @if($emailNotes->count())
            <h5 class="mb-2">Historia komunikacji</h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Treść</th>
                            <th>Użytkownik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emailNotes as $note)
                            <tr>
                                <td>{{ $note->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $note->content }}</td>
                                <td>{{ $note->user?->name ?? $note->user?->email ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Brak wysłanych wiadomości.</p>
        @endif
    </div>
</div>

{{-- Send email modal --}}
<div class="modal fade" id="send-email-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{custom_route('reclamation-send-email', ['id' => $data->getKey()])}}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Wyślij e-mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Adres e-mail</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ $data->client?->email ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wybierz szablon</label>
                        <select name="template_id" class="form-select" id="email-template-select" required>
                            <option value="">-- Wybierz szablon --</option>
                            @foreach(\App\Models\EmailTemplate::all() as $template)
                                <option value="{{ $template->getKey() }}"
                                        data-subject="{{ $template->subject }}"
                                        data-body="{{ e($template->body) }}">
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="email-preview-container" style="display: none;">
                        <label class="form-label">Podgląd</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-1"><strong>Temat:</strong> <span id="email-preview-subject"></span></p>
                            <hr>
                            <div id="email-preview-body"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-send"></i> Wyślij
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('email-template-select');
        if (select) {
            select.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const container = document.getElementById('email-preview-container');
                if (option.value) {
                    document.getElementById('email-preview-subject').textContent = option.dataset.subject || '';
                    document.getElementById('email-preview-body').innerHTML = (option.dataset.body || '').replace(/\n/g, '<br>');
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        }
    });
</script>
