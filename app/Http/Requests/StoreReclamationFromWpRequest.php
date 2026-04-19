<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;

class StoreReclamationFromWpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'purchase_date' => ['nullable', 'date'],
            'fault_description' => ['required', 'string'],
            'urgency' => ['nullable', 'in:pilne,niepilne'],
            'attachments' => ['nullable', 'array', 'max:20'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,mp4,mov,pdf', 'max:20480'],
        ];
    }

    public function priorityValue(): string
    {
        return $this->input('urgency') === 'pilne'
            ? Priority::PRIORITY_HIGH->name
            : Priority::PRIORITY_NORMAL->name;
    }
}
