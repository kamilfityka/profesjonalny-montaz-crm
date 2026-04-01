<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\User;
use App\Models\Calendar;
use App\Models\Document;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Praust\App\Models\Concerns\PraustCategory;
use Praust\App\Models\Concerns\PraustManyCategories;
use Praust\App\Models\PraustActionModel;
use Praust\App\Models\PraustInfoBox;
use Throwable;

class CalendarController extends \Praust\App\Http\Controllers\Admin\PraustActionController
{
    use User;

	public string $module_name = 'Lista zadań';

    protected function beforeList(mixed $id, Builder &$data): void
    {
        parent::beforeList($id, $data);

        $this->beforeListUser($id, $data);
    }

    protected function beforeSearchList(mixed $model, Builder &$query, Request $request): void
    {
        parent::beforeSearchList($model, $query, $request);

        if ($request->has('search.user_id')) {
            $query->where('user_id', $request->input('search.user_id'));
        } else {
            $query->where('user_id', $request->user()->getKey());
            $query->orWhere('created_by', $request->user()->getKey());
        }
    }

    protected function afterUpdate(Request $request, mixed &$data)
    {
        parent::afterUpdate($request, $data);

        $this->afterUpdateUser($request, $data);

        if($request->has('save_and_active'))
        {
            /** @var Calendar $data */
            $data->active = false;
            $data->save();
        }
    }

    protected function afterStore(Request $request, mixed &$data)
    {
        parent::afterStore($request, $data);
    }

    public function postStore(Request $request): mixed
    {
        $this->checkPermission($request, 'create');

        /** @var PraustActionModel $data */
        $model = $data = $this->model_name_class;

        $rules = getClass('Tools')::getValidationRules($data);
        $this->beforeStore($request, $rules);

        $validator = Validator::make($request->input($this->getModelName(), []), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $data->setDefaultValues();

            if ($data->hasOrder()) {
                if ($data->getDefaultOrderDirection() == 'asc') {
                    $data->order = 0;

                    /** @var PraustActionModel[] $elements */
                    $elements = $model->newQuery()->get();
                    foreach ($elements as $element) {
                        $element->order++;
                        $element->timestamps = false;
                        $element->save();
                    }
                } else {
                    $data->order = $model->newQuery()->max('order') + 1;
                }
            }

            if ($data->usesTimestamps()) {
                $data->created_by_user()->associate($request->user());
                $data->updated_by_user()->associate($request->user());
            }

            $data->saveFromInput($request);

            $data->save();

            $this->saveDefaultData($request, $data);

            if ($data->hasTrait(PraustManyCategories::class)) {
                $this->saveCategories($request, $data);
            }

            $this->afterStore($request, $data);

            $data->touch();

            DB::commit();

            PraustInfoBox::flashSuccess(custom_admin_trans('Poprawnie dodano!'));
            if ($request->has('save_and_add')) {
                return redirect()->to(custom_route(Str::of($this->model_name)->kebab() . '-create'));
            } elseif ($request->has('save_and_stay')) {
                return redirect()->to(custom_route(Str::of($this->model_name)->kebab() . '-edit', ['id' => $data->getKey()]));
            } else {
                return redirect()->to(custom_route('dashboard-index'));
            }
        } catch (Exception $m) {
            if ($data->getKey()) {
                $data->forceDelete();
            }
            DB::rollback();
            PraustInfoBox::flashError($m->getMessage());
        } catch (Throwable $m) {
            if ($data->getKey()) {
                $data->forceDelete();
            }
            DB::rollback();
            PraustInfoBox::flashException($m->getMessage() . ' (' . $m->getFile() . ': ' . $m->getLine() . ')');
        }
        return redirect()->back()->withInput();
    }

    public function postMove(Request $request, $id)
    {
        $this->checkPermission($request, 'read');

        /** @var Document $model */
        $model = $this->model_name_class;

        /** @var Document $data */
        $data = $model->newQuery()->findOrFail($id);

        $data->created_at = Carbon::parse($request->input('to'));
        $data->save();
    }
}
