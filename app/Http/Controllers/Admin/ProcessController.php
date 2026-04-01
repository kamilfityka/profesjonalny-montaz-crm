<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProcessController extends \Praust\App\Http\Controllers\Admin\PraustActionCategoryController
{
    use User;

    public string $module_name = 'Procesy';

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
}
