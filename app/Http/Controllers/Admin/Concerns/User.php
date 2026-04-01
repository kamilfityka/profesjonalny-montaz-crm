<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait User
{
    protected function beforeListUser(mixed $id, Builder &$data): void
    {
        if (!request()->has('search')) {
            $data->where('user_id', request()->user()->id);
        }
    }

    protected function beforeSearchListUser(mixed $model, Builder &$query, Request $request): void
    {
        if ($request->has('search.user_id')) {
            $query->where('user_id', $request->input('search.user_id'));
        } else {
            $query->where('user_id', $request->user()->id);
        }
    }

    protected function afterUpdateUser(Request $request, mixed &$data)
    {
    }

    protected function afterStoreUser(Request $request, mixed &$data)
    {
        $data->user()->associate($request->user());
        $data->save();
    }
}
