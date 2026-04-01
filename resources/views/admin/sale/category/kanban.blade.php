@extends('praust::admin.layout')
<?php /** @var \Praust\App\Models\PraustActionModel|\Illuminate\Support\Collection $items_category */ ?>
<?php /** @var \Praust\App\Models\PraustActionModel|\Illuminate\Support\Collection $items */ ?>
@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    {{$current_module_name}}
                    @if($id)
                        <a href="{{custom_route(Str::kebab($model_category_name).'-edit', ['id' => $id])}}"
                           class="ms-2 btn btn-primary btn-xs">{{$model_category->trans['edit']}}</a>
                    @endif
                </h4>
                <div class="page-title-right">
                    @includeFirst(['admin._inc.templates.breadcrumb', 'praust::admin._inc.templates.breadcrumb'])
                </div>
            </div>
        </div>
    </div>

    @includeFirst(['admin._inc.templates.messages', 'praust::admin._inc.templates.messages'])
@endsection
@section('content')
    @php $users = (new \App\Models\User())->newQuery()->get(); @endphp
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="button-list">
            <a href="{{request()->fullUrlWithQuery(['view' => 'list'])}}" class="btn mb-0 btn-primary">
                <span class="btn-label"><i class="fas fa-list-ul"></i></span>Lista</a>
            @if($model->canCreate())
                @permission(Str::kebab($model_name).'-create')
                <a href="{{custom_route(Str::kebab($model_name).'-create')}}" class="btn mb-0 btn-primary">
                    <span class="btn-label"><i class="mdi mdi-plus"></i></span>{{$model->trans['create']}}
                </a>
                @endpermission
            @endif
            @if($model_category->canCreate())
                @permission(Str::kebab($model_category_name).'-create')
                <a href="{{custom_route(Str::kebab($model_category_name).'-create')}}" class="btn mb-0 btn-primary">
                    <span class="btn-label"><i class="mdi mdi-plus"></i></span>{{$model_category->trans['create']}}
                </a>
                @endpermission
            @endif
        </div>

        <div>
            <label for="" class="mb-0">Użytkownik:</label>
            <select name="user_id" data-toggle="select2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach($users as $user)
                    <option value="{{request()->fullUrlWithQuery(['search' => ['user_id' => $user->getKey()]])}}" @selected($user->getKey() == request()->input('search.user_id', auth()->id()))>{{$user->getAdminName()}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="scroll-fix-parent" style="transform: rotateX(180deg); overflow-x: auto;">
        <div class="scroll-fix-children" style="transform: rotateX(180deg);">
            <div class="d-flex" style="min-height: 65vh;">
                @foreach($items_category as $item)
                    <div class="card mb-0" style="min-width: 300px; box-shadow: none; border-radius: 0; border: none; background-color: {{\Illuminate\Support\Str::start($item->bgColor,'#')}};">
                        <div class="card-body">
                            <h4 class="header-title mb-3">{!! $item->getAdminName() !!}</h4>

                            @if($item->admin_category_childrens->count() >= 5)
                                <a href="#0" class="btn-toggle" data-bs-toggle="collapse"
                                   data-bs-target="#task_collapse_{{$item->getKey()}}">Rozwiń</a>
                            @endif

                            <div class="collapse {{$item->admin_category_childrens->count() < 5 ? 'show' : ''}}" id="task_collapse_{{$item->getKey()}}">
                                <ul class="sortable-list tasklist list-unstyled" data-category-id="{{$item->getKey()}}">
                                    @foreach($item->admin_category_childrens as $kanban)
                                        @includeFirst(['admin.'.Str::kebab($model_name).'.category.partials.kanban', 'admin.default.category.partials.kanban', 'praust::admin.'.Str::kebab($model_name).'.category.partials.kanban', 'praust::admin.default.category.partials.kanban'], ['data' => $kanban])
                                    @endforeach
                                </ul>
                            </div>
                            @permission(Str::kebab($model_name).'-create')
                            <div class="d-grid">
                                <a href="{{custom_route(Str::kebab($model_name).'-create', ['category_id' => $item->getKey()])}}"
                                   class="btn btn-primary mt-3 waves-effect waves-light"><i
                                        class="mdi mdi-plus"></i> {{$model->trans['create']}}</a>
                            </div>
                            @endpermission
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @foreach($items_category as $category)
        @foreach($category->admin_category_childrens as $item)
            <div class="modal fade" id="win_{{$item->getKey()}}"
                 aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Wygrana</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="button-list">
                                @foreach(\App\Models\Sale::$win_reasons as $k => $reason)
                                    <a href="{{custom_route('sale-win', ['id' => $item->getKey(), 'reason_id' => $k])}}" class="btn btn-primary">{{$reason}}</a>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="lose_{{$item->getKey()}}"
                 aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Przegrana</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="button-list">
                                @foreach(\App\Models\Sale::$lose_reasons as $k => $reason)
                                    <a href="{{custom_route('sale-lose', ['id' => $item->getKey(), 'reason_id' => $k])}}" class="btn btn-outline-primary">{{$reason}}</a>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endsection

@push('scripts')
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/sortablejs/Sortable.min.js')}}"></script>
    <script>
        !function ($) {
            "use strict";

            var KanbanBoard = function () {
                this.$body = $("body")
            };

            //initializing various charts and components
            KanbanBoard.prototype.init = function () {
                $('.tasklist').each(function () {
                    Sortable.create($(this)[0], {
                        group: 'shared',
                        animation: 150,
                        ghostClass: 'bg-ghost',
                        onEnd: function (evt) {
                            var orders = [];
                            var idx = 0;
                            $(evt.to).find("li").each(function (i, v) {
                                orders.push({'id': $(v).data("id"), 'order': idx});
                                idx++;
                            });

                            $.ajax({
                                url: base_url + "{{Str::start(config('praust.admin_path').'/'.Str::kebab($model_name).'/kanban', '/')}}",
                                type: 'POST',
                                data: {
                                    '_token': "{{csrf_token()}}",
                                    'model_id': evt.item.dataset.id,
                                    'model_category_id': evt.to.dataset.categoryId,
                                    'orders': orders
                                },
                                success: function () {
                                    $.NotificationApp.send("Udało się!", "Kolejność zmieniona poprawnie!", 'top-right', '#5ba035', 'success');
                                }
                            });
                        }
                    });

                });
            },

                //init KanbanBoard
                $.KanbanBoard = new KanbanBoard, $.KanbanBoard.Constructor =
                KanbanBoard

        }(window.jQuery),

            function ($) {
                "use strict";
                $.KanbanBoard.init()
            }(window.jQuery);
    </script>
@endpush
