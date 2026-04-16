<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\User;
use App\Models\Client;
use App\Models\EmailTemplate;
use App\Models\Exports\Clients;
use App\Models\Exports\Reclamations;
use App\Models\Reclamation;
use App\Models\ReclamationNote;
use App\Mail\ReclamationEmail;
use App\Services\ReclamationLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReclamationController extends \Praust\App\Http\Controllers\Admin\PraustActionCategoryController
{
    use User;

	public string $module_name = 'Zgłoszenia';

    protected function beforeList(mixed $id, Builder &$data): void
    {
        parent::beforeList($id, $data);

        $this->beforeListUser($id, $data);
    }

    protected function beforeSearchList(mixed $model, Builder &$query, Request $request): void
    {
        parent::beforeSearchList($model, $query, $request);

        $this->beforeSearchListUser($model, $query, $request);
    }

    protected function afterUpdate(Request $request, mixed &$data)
    {
        $this->afterUpdateUser($request, $data);

        ReclamationLogger::log($data->getKey(), 'auto_status_change', 'Zaktualizowano zgłoszenie');
    }

    protected function afterStore(Request $request, mixed &$data)
    {
        $this->afterStoreUser($request, $data);

        ReclamationLogger::log($data->getKey(), 'auto_created', 'Utworzono nowe zgłoszenie');
    }

    public function getDownload(Request $request, mixed $id = null): mixed
    {
        $data = (new Reclamation())->newQuery()->where('reclamation_category_id', '!=', 9)->when($id, fn($query) => $query->where('reclamation_category_id', $id))->order();
        if ($request->has('search.user_id')) {
            $data = $data->where('user_id', $request->input('search.user_id'));
        } else {
            $data = $data->where('user_id', $request->user()->id);
        }
        $data = $data->get();
        return Excel::download(new Reclamations($data), Str::slug('reklamacje') . '.xlsx');
    }

    public function postNote(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $reclamation = (new Reclamation())->newQuery()->findOrFail($id);

        ReclamationLogger::log($reclamation->getKey(), 'manual', $request->input('content'));

        return redirect()->back()->with('success', 'Notatka została dodana.');
    }

    public function getPdf(Request $request, $id)
    {
        /** @var Reclamation $data */
        $data = (new Reclamation())->newQuery()->with(['notes', 'client', 'type'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.reclamation.pdf', compact('data'));
        $pdf->setOptions(['defaultFont' => 'Maisonneue', 'font_height_ratio' => 1, 'enable_remote' => true]);
        return $pdf->stream('protokol-serwisowy-' . $data->getKey() . '.pdf');
    }

    public function getEmailTemplates(Request $request, $id): JsonResponse
    {
        $templates = (new EmailTemplate())->newQuery()->get(['id', 'name', 'subject', 'body']);
        return response()->json($templates);
    }

    public function postSendEmail(Request $request, $id)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'email' => 'required|email',
        ]);

        /** @var Reclamation $reclamation */
        $reclamation = (new Reclamation())->newQuery()->with('client')->findOrFail($id);
        $template = (new EmailTemplate())->newQuery()->findOrFail($request->input('template_id'));

        $replacements = [
            '{client_name}' => $reclamation->name ?? $reclamation->client?->name ?? '',
            '{case_number}' => (string) $reclamation->getKey(),
            '{address}' => $reclamation->address ?? '',
            '{phone}' => $reclamation->phone ?? '',
            '{purchase_date}' => $reclamation->purchase_date ? \Carbon\Carbon::parse($reclamation->purchase_date)->format('d-m-Y') : '',
        ];

        $subject = str_replace(array_keys($replacements), array_values($replacements), $template->subject);
        $body = str_replace(array_keys($replacements), array_values($replacements), $template->body);

        Mail::to($request->input('email'))->send(new ReclamationEmail($subject, $body));

        ReclamationLogger::log($reclamation->getKey(), 'auto_email_sent', 'Wysłano e-mail: ' . $subject . ' na adres: ' . $request->input('email'));

        return redirect()->back()->with('success', 'E-mail został wysłany.');
    }
}
