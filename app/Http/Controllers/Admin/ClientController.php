<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\User;
use App\Models\Client;
use App\Models\DiscountProgramAccessCode;
use App\Models\Exports\Clients;
use App\Models\Exports\DiscountProgramAccessCodeExport;
use App\Models\Sale;
use App\Models\SaleCategory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends \Praust\App\Http\Controllers\Admin\PraustActionCategoryController
{
    use User;

	public string $module_name = 'Baza kontaktów';

    protected function afterStore(Request $request, mixed &$data)
    {
        parent::afterStore($request, $data);

        $this->afterStoreUser($request, $data);

        /** @var Client $data */
        if($data->category && $data->category->sale) {
            $sale = new Sale();
            $sale->setDefaultValues();
            $sale->client()->associate($data);
            $sale->user()->associate($request->user());
            $sale->category()->associate((new SaleCategory())->newQuery()->order()->active()->first());
            $sale->name = $data->getName();
            $sale->save();
        }
    }

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

    public function getDownload(Request $request, mixed $id = null): mixed
    {
        $data = (new Client())->newQuery()->when($id, fn($query) => $query->where('client_category_id', $id))->order();
        if ($request->has('search.user_id')) {
            $data = $data->where('user_id', $request->input('search.user_id'));
        } else {
            $data = $data->where('user_id', $request->user()->id);
        }
        $data = $data->get();
        return Excel::download(new Clients($data), Str::slug('klienci') . '.xlsx');
    }
}
