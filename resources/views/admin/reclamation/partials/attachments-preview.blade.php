@if($data->admin_attachments && $data->admin_attachments->count())
<div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3"><i class="mdi mdi-attachment me-1"></i> Załączniki</h4>
        <div class="row">
            @foreach($data->admin_attachments as $attachment)
                @php
                    $extension = strtolower(pathinfo($attachment->file, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
                    $fileUrl = asset('storage/' . $attachment->file);
                @endphp
                <div class="col-md-4 col-sm-6 mb-3">
                    @if($isImage)
                        <a href="{{ $fileUrl }}" data-bs-toggle="modal" data-bs-target="#attachment-modal-{{ $attachment->getKey() }}">
                            <img src="{{ $fileUrl }}" class="img-fluid rounded border" alt="Załącznik" style="max-height: 200px; object-fit: cover; width: 100%;">
                        </a>
                        {{-- Modal lightbox --}}
                        <div class="modal fade" id="attachment-modal-{{ $attachment->getKey() }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ basename($attachment->file) }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <img src="{{ $fileUrl }}" class="img-fluid" alt="Załącznik">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($isVideo)
                        <video controls class="w-100 rounded border" style="max-height: 200px;">
                            <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                            Twoja przeglądarka nie obsługuje odtwarzania wideo.
                        </video>
                    @else
                        <div class="border rounded p-3 text-center">
                            <i class="mdi mdi-file-document-outline font-24"></i>
                            <p class="mb-1 text-truncate">{{ basename($attachment->file) }}</p>
                            <a href="{{ $fileUrl }}" class="btn btn-sm btn-outline-primary" download>
                                <i class="mdi mdi-download"></i> Pobierz
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
