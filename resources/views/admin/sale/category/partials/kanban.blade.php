<li id="task-{{$kanban->getKey()}}" data-id="{{$kanban->getKey()}}"
    data-order="{{$kanban->order}}" @class(['task-low' => $data->priority == \App\Models\Enums\Priority::PRIORITY_LOW->name, 'task-medium' => $data->priority == \App\Models\Enums\Priority::PRIORITY_NORMAL->name, 'task-high' => $data->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name])>
    <h5 class="m-0 mb-2"><a
                href="{{$data->canEdit() ? custom_route(Str::kebab($model_name).'-edit', ['id' => $data->getKey()]) : '#0'}}"
                class="text-dark">{!! $data->getAdminName() !!}</a>
    </h5>
    <div class="button-list">
        <a href="{{custom_route(Str::kebab($model_name).'-win', ['id' => $data->getKey()])}}" class="btn btn-primary" data-bs-toggle="modal"
           data-bs-target="#win_{{$data->getKey()}}">Wygrana</a>
        <a href="{{custom_route(Str::kebab($model_name).'-lose', ['id' => $data->getKey()])}}" class="btn btn-outline-primary" data-bs-toggle="modal"
           data-bs-target="#lose_{{$data->getKey()}}">Przegrana</a>
    </div>
</li>
