<?php declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreReclamationFromWpRequest;
use App\Models\Client;
use App\Models\Enums\ReclamationSource;
use App\Models\Reclamation;
use App\Models\ReclamationAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateReclamationFromWpForm
{
    public function execute(StoreReclamationFromWpRequest $request): Reclamation
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            $client = $this->resolveClient($data);

            $reclamation = new Reclamation();
            $reclamation->name = $data['name'];
            $reclamation->phone = $data['phone'];
            $reclamation->address = $data['address'];
            $reclamation->fault_description = $data['fault_description'];
            $reclamation->purchase_date = $data['purchase_date'] ?? null;
            $reclamation->priority = $request->priorityValue();
            $reclamation->source = ReclamationSource::WP_FORM->value;
            $reclamation->active = true;

            $pendingCategoryId = config('services.wp_webhook.pending_category_id');
            if ($pendingCategoryId) {
                $reclamation->category()->associate($pendingCategoryId);
            }

            if ($client) {
                $reclamation->client()->associate($client);
            }

            $reclamation->save();

            $files = $request->file('attachments') ?? [];
            foreach ($files as $file) {
                $this->storeAttachment($reclamation, $file);
            }

            // TODO: po wydaniu base z B2 podmienić na $reclamation->logEvent('reclamation.wp_registered', ...)
            \Log::info('reclamation.wp_registered', [
                'reclamation_id' => $reclamation->getKey(),
                'ip' => $request->ip(),
            ]);

            return $reclamation;
        });
    }

    private function resolveClient(array $data): ?Client
    {
        $email = $data['email'] ?? null;
        $phone = $data['phone'];

        $query = (new Client())->newQuery();
        if ($email) {
            $client = (clone $query)->where('email', $email)->first();
            if ($client) {
                return $client;
            }
        }

        return $query->where('phone', $phone)->first();
    }

    private function storeAttachment(Reclamation $reclamation, UploadedFile $file): void
    {
        $attachment = new ReclamationAttachment();
        $attachment->reclamation_id = $reclamation->getKey();
        $attachment->file = $file->getClientOriginalName();
        $reclamation->admin_attachments()->save($attachment);

        $targetDir = Storage::path(
            Str::snake($reclamation->getModelName()) . '/' . $reclamation->getKey() . '/attachments/' . $attachment->getKey()
        );

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $file->move($targetDir, $file->getClientOriginalName());
    }
}
