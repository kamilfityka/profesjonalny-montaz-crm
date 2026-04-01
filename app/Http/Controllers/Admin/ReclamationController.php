<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\User;
use App\Models\Client;
use App\Models\Exports\Clients;
use App\Models\Exports\Reclamations;
use App\Models\Reclamation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
    }

    protected function afterStore(Request $request, mixed &$data)
    {
        $this->afterStoreUser($request, $data);
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
}
