<?php /** @var \Praust\App\Models\PraustActionModel $data */ ?>
@php
    $this_model_name = $model_name;
    if ($model_is_category) {
        $this_model_name = $model_category_name ;
    }
@endphp
@php $generated_class = new $data->attached_builder; @endphp
@php $builder_class = $generated_class->getBuilders(); @endphp
<div class="row">
    <div class="col-12 {{$data->getKey() ? 'col-lg-6' : ''}}">
        <div id="builder_wrapper">
            @foreach($data->admin_builders as $k => $item)
                @php $item->options = $item->options ? json_decode($item->options) : new \StdClass; @endphp

                @php
                    $path = 'praust::admin.builders.section_none';
                    if (view()->exists('admin.builders.section_none')) {
                        $path = 'admin.builders.section_none';
                    }
                    if ($builder_class[$item->type]['file']) {
                        $find_path = 'admin.builders.' . ($builder_class[$item->type]['group'] ? $builder_class[$item->type]['group'] . '.' : '') . $builder_class[$item->type]['file'];
                        if (view()->exists($find_path)) {
                            $path = $find_path;
                        } else if (view()->exists('admin.builders.' . $builder_class[$item->type]['file'])) {
                            $path = 'admin.builders.' . $builder_class[$item->type]['file'];
                        } else if (view()->exists('praust::' . $find_path)) {
                            $path = 'praust::' . $find_path;
                        }
                    }
                @endphp
                @include('praust::admin.builders.layout', compact('path'))
            @endforeach
        </div>
    </div>
    @if($data->getKey())
        <div class="col-12 col-lg-6">
            <iframe src="{{custom_route('document-pdf', ['id' => $data->getKey()])}}" width="100%" height="500px"></iframe>
        </div>
    @endif
</div>

@php $possible_parameters = ['']; @endphp
@php $possible_parameters[] = \Illuminate\Support\Str::of($model_name)->lower()->toString(); @endphp
@if($data->getKey())
    @php $possible_parameters[] = \Illuminate\Support\Str::of($model_name)->lower()->toString().'.id_'.$data->getKey(); @endphp
@endif
@if($data->hasTrait(\Praust\App\Models\Concerns\PraustView::class) && $data->view)
    @php $possible_parameters[] = \Illuminate\Support\Str::of($model_name)->lower()->toString().'.view_'.$data->view->getKey(); @endphp
@endif

@php $documentTypes = (new \App\Models\DocumentType())->newQuery()->active()->order()->get(); @endphp
<div class="row">
    <div class="col-md-6">
        <div class="d-flex align-items-center">
            <p class="me-2 mb-0 font-14">@cat('Dodaj')</p>
            <select class="form-select w-auto builder-type me-2">
                @if($documentTypes->count())
                    <optgroup label="Zdefiniowane wzory">
                        @foreach($documentTypes as $documentType)
                            <option value="DocumentType:{{$documentType->getKey()}}">{{$documentType->getAdminName()}}</option>
                        @endforeach
                    </optgroup>
                @endif
                <optgroup label="Pozostałe">
                    @foreach(collect($builder_class)->whereIn('group', $possible_parameters) as $b => $builder)
                        <option value="{{$b}}">{{$builder['name']}}</option>
                    @endforeach
                </optgroup>
            </select>
            <select class="form-select w-auto order-id me-2">
                <option value="-1">@cat('Na początku')</option>
                @foreach($data->admin_builders as $b => $item)
                    <option value="{{$b}}" @selected($loop->last)>
                        @cat('po') {{$builder_class[$item->type]['name'] ?? ''}}</option>
                @endforeach
            </select>
            <a href="#" title="Dodaj" class="btn btn-primary me-2" id="add-builder">
                @cat('Wykonaj')
            </a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            let $builds = $("#builder_wrapper");
            let $orderID = $(".order-id");
            if ($builds.length) {
                $builds.find(".builder-item:not(.d-none) textarea.no-ckeditor").each(function () {
                    CKEDITOR.replace(this);
                    $(this).removeClass('no-ckeditor');
                });

                $builds.find(".builder-item:not(.d-none):first").find(".move_up").parent().hide();
                $builds.find(".builder-item:not(.d-none):last").find(".move_down").parent().hide();

                $builds.delegate(".move_down", "click", function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let $parent = $this.parents(".builder-item:not(.d-none)");
                    let $next = $parent.next();
                    if ($next.length) {
                        $parent.insertAfter($next);
                    }
                    check_buttons();

                    setTimeout(function () {
                        $([document.documentElement, document.body]).stop(true, true).animate({
                            scrollTop: $("#" + $parent.attr("id")).offset().top - $(".navbar-custom").height()
                        }, 400);
                    }, 100);

                    $.NotificationApp.send("Udało się!", "Moduł przeniesiony niżej!", 'top-right', '#5ba035', 'success');
                });

                $builds.delegate(".move_up", "click", function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let $parent = $this.parents(".builder-item:not(.d-none)");
                    let $previous = $parent.prev();
                    if ($previous.length) {
                        $parent.insertBefore($previous);
                    }
                    check_buttons();

                    setTimeout(function () {
                        $([document.documentElement, document.body]).stop(true, true).animate({
                            scrollTop: $("#" + $parent.attr("id")).offset().top - $(".navbar-custom").height()
                        }, 400);
                    }, 100);

                    $.NotificationApp.send("Udało się!", "Moduł przeniesiony wyżej!", 'top-right', '#5ba035', 'success');
                });

                function check_buttons(reload_cke = true) {
                    $builds.find(".builder-item:not(.d-none)").find(".move_up").parent().show();
                    $builds.find(".builder-item:not(.d-none)").find(".move_down").parent().show();
                    $builds.find(".builder-item:not(.d-none):first").find(".move_up").parent().hide();
                    $builds.find(".builder-item:not(.d-none):last").find(".move_down").parent().hide();

                    reorder_order();

                    if (reload_cke) {
                        $builds.find(".builder-item:not(.d-none) textarea:not(.form-control)").each(function () {
                            let id = $(this).next().attr("id").replace("cke_", "");

                            if (CKEDITOR.instances[id]) {
                                CKEDITOR.instances[id].destroy();
                                CKEDITOR.replace(this);
                            }
                        });
                    }
                }

                function reorder_order() {
                    $builds.find(".builder-item:not(.d-none)").each(function (i, v) {
                        $(v).find("input[name*='[order]']").val(i);
                    });
                    if ($orderID.length) {
                        $orderID.find("option:not(:first)").remove();
                        $builds.find(".builder-item").each(function (i, v) {
                            let $element = $("<option>");
                            $element.html("po " + $(this).find("h4").html());
                            $element.val(i);
                            $orderID.append($element);
                        });
                        let num = $('.order-id option').length;
                        $orderID.prop('selectedIndex', num - 1);
                    }
                }

                $builds.delegate("a.remove-builder", "click", function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let $parent = $this.parents(".builder-item");
                    $parent.fadeOut(function () {
                        $parent.remove();
                        check_buttons(false);
                    });

                    $.NotificationApp.send("Udało się!", "Moduł skasowany!", 'top-right', '#5ba035', 'success');
                });

                $("#add-builder").on("click", function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let type = $(".builder-type").val();
                    let k = $builds.find(".builder-item").length;

                    $.ajax({
                        url: base_url + "{{Str::start(config('praust.admin_path').'/'.Str::kebab($this_model_name).'/load_builder', '/')}}",
                        type: 'GET',
                        data: {data_id: '{{$data->getKey()}}', type: type, k: k},
                        success: function (result) {
                            let $result = $(result.data);
                            $result.find("textarea.no-ckeditor").each(function () {
                                CKEDITOR.replace(this);
                                $(this).removeClass('no-ckeditor');
                            });

                            if ($orderID.length) {
                                let builder_idx = parseInt($orderID.val());
                                if (builder_idx === -1) { // before
                                    $builds.prepend($result);
                                } else {
                                    let $next = $builds.find(".builder-item:not(.d-none)").eq(builder_idx);
                                    if ($next.length) {
                                        $result.insertAfter($next);
                                    }
                                }
                            } else {
                                $builds.append($result);
                            }
                            $this.blur();

                            $builds.find(".builder-item:not(.d-none)").find(".move_up").parent().show();
                            $builds.find(".builder-item:not(.d-none)").find(".move_down").parent().show();
                            $builds.find(".builder-item:not(.d-none):first").find(".move_up").parent().hide();
                            $builds.find(".builder-item:not(.d-none):last").find(".move_down").parent().hide();

                            reorder_order();
                            sortable();

                            APP.customFile();

                            setTimeout(function () {
                                $([document.documentElement, document.body]).stop(true, true).animate({
                                    scrollTop: $("#" + $result.attr("id")).offset().top - $(".navbar-custom").height()
                                }, 400);
                            }, 100);

                            $.NotificationApp.send("Udało się!", "Moduł dodany!", 'top-right', '#5ba035', 'success');
                        }
                    });
                });

                $builds.delegate("a[name=addValue]", 'click', function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let $container = $this.parents(".section-wrapper");
                    let type = $this.data('type');
                    let k = $this.parents(".builder-item").data("idx");
                    let x = $container.find(".builder-children-item").length + 1;

                    $.ajax({
                        url: base_url + "{{Str::start(config('praust.admin_path').'/'.Str::kebab($this_model_name).'/load_builder_children', '/')}}",
                        type: 'GET',
                        data: {data_id: '{{$data->getKey()}}', type: type, k: k, x: x},
                        success: function (result) {
                            let $result = $(result['data']);
                            $result.find("textarea.no-ckeditor").each(function () {
                                CKEDITOR.replace(this);
                                $(this).removeClass('no-ckeditor');
                            });

                            $container.find(".add-new-wrapper").before($result);

                            APP.customFile();

                            $.NotificationApp.send("Udało się!", "Moduł dodany!", 'top-right', '#5ba035', 'success');
                        }
                    });
                });
                $builds.delegate("a[name=deleteValue]", 'click', function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    let $container = $($this.parents(".builder-children-item")[0]);
                    $container.fadeOut(function () {
                        $container.remove();
                        check_buttons(false);
                    });

                    $.NotificationApp.send("Udało się!", "Moduł skasowany!", 'top-right', '#5ba035', 'success');
                });

                var sortable = function () {
                    $builds.find(".section-wrapper").each(function (i, v) {
                        $(v).find("tbody").sortable({
                            handle: '.sortable_handle',
                            axis: "y",
                            tolerance: "pointer",
                            cursor: "move",
                            opacity: 0.7,
                            revert: 300,
                            delay: 150,
                            dropOnEmpty: true,
                            placeholder: "movable-placeholder",
                            start: function (e, ui) {
                                ui.placeholder.height(ui.helper.outerHeight());
                            },
                            update: function (e, ui) {
                                let order = $(v).find("tbody").sortable('toArray');

                                $(ui.item[0]).find("textarea").each(function () {
                                    let id = $(this).next().attr("id").replace("cke_", "");

                                    if (CKEDITOR.instances[id]) {
                                        CKEDITOR.instances[id].destroy();
                                        CKEDITOR.replace(this);
                                    }
                                });

                                Object.keys(order).forEach(function (key) {
                                    if (order[key]) {
                                        $("#" + order[key]).find("input[name*='[order]']").val(key);
                                    }
                                });

                                $.NotificationApp.send("Udało się!", "Moduł przeniesiony!", 'top-right', '#5ba035', 'success');
                            },
                            helper: function (e, tr) {
                                let $originals = tr.children();
                                let $helper = tr.clone();
                                $helper.children().each(function (index) {
                                    // Set helper cell sizes to match the original sizes
                                    $(this).width($originals.eq(index).outerWidth());
                                });
                                return $helper;
                            }
                        });
                    })
                }
                sortable();
            }
        });
    </script>
@endpush

