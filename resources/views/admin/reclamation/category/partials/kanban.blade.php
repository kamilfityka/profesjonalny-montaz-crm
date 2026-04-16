<li id="task-{{$kanban->getKey()}}" data-id="{{$kanban->getKey()}}"
    data-order="{{$kanban->order}}" @class([
        'task-low' => $data->priority == \App\Models\Enums\Priority::PRIORITY_LOW->name,
        'task-medium' => $data->priority == \App\Models\Enums\Priority::PRIORITY_NORMAL->name,
        'task-high' => $data->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name,
        'task-urgent' => $data->urgency === \App\Models\Enums\Urgency::URGENT->value,
    ])>
    <h5 class="m-0"><a
                href="{{$data->canEdit() ? custom_route(Str::kebab($model_name).'-edit', ['id' => $data->getKey()]) : '#0'}}"
                class="text-dark">{!! $data->getAdminName() !!}
                @if($data->urgency === \App\Models\Enums\Urgency::URGENT->value)
                    <span class="badge bg-danger" style="font-size:9px;">PILNE</span>
                @endif
                <br><div style="font-size:10px;margin-top:5px;">{{$data->created_at?->format("d-m-Y H:i")}}</div></a>
    </h5>
</li>

<style>
    .task-urgent {
        border: 2px solid #dc3545 !important;
        background-color: #fff5f5 !important;
    }
</style>
