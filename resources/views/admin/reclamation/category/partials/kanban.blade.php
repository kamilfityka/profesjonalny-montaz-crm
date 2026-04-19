<li id="task-{{$kanban->getKey()}}" data-id="{{$kanban->getKey()}}"
    data-order="{{$kanban->order}}" @class(['reclamation-card', 'task-low' => $data->priority == \App\Models\Enums\Priority::PRIORITY_LOW->name, 'task-medium' => $data->priority == \App\Models\Enums\Priority::PRIORITY_NORMAL->name, 'task-high reclamation-urgent' => $data->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name])>
    <h5 class="m-0">
        @if($data->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name)
            <span class="badge bg-danger reclamation-urgent-badge">PILNE</span>
        @endif
        <a href="{{$data->canEdit() ? custom_route(Str::kebab($model_name).'-edit', ['id' => $data->getKey()]) : '#0'}}"
           class="text-dark">{!! $data->getAdminName() !!}<br><div style="font-size:10px;margin-top:5px;">{{$data->created_at?->format("d-m-Y H:i")}}</div></a>
    </h5>
</li>
