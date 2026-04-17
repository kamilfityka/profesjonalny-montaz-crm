<?php namespace App\Http\Controllers\Admin;

use App\Actions\ConvertSaleToProcess;
use App\Http\Controllers\Admin\Concerns\User;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Praust\App\Models\PraustInfoBox;

class SaleController extends \Praust\App\Http\Controllers\Admin\PraustActionCategoryController
{
    use User;

	public string $module_name = 'Szanse sprzedaży';

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

    public function getLose(Request $request, $id)
    {
        $model = $this->model_name_class;
        /** @var Sale $data */
        $data = $model->newQuery()->findOrFail($id);

        $data->lose_reason = Sale::$lose_reasons[$request->input('reason_id')];
        $data->save();

        PraustInfoBox::flashSuccess(custom_admin_trans('Zaznaczono proces jako przegrany!'));
        $data->delete();
        return redirect()->back();
    }

    public function getWin(Request $request, $id)
    {
        /** @var Sale $data */
        $data = $this->model_name_class->newQuery()->findOrFail($id);

        $process = (new ConvertSaleToProcess())->execute($data, Sale::$win_reasons[$request->input('reason_id')]);

        PraustInfoBox::flashSuccess(custom_admin_trans('Zaznaczono proces jako wygrany!'));
        return redirect()->to(custom_route('process-edit', ['id' => $process->getKey()]));
    }
}
