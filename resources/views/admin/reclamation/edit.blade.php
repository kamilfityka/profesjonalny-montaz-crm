@extends('praust::admin.layout')
<?php /** @var \App\Models\Reclamation $data */ ?>
@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    @if(\Illuminate\Support\Str::length($data->getAdminName()))
                        {{strip_tags($data->getAdminName())}}
                    @else
                        <i>@cat('nie uzupełniono')</i>
                    @endif
                    <a href="{{custom_route('reclamation-pdf', ['id' => $data->getKey()])}}"
                       class="ms-2 btn btn-info btn-xs" target="_blank">
                        <i class="mdi mdi-file-pdf-box"></i> Protokół PDF
                    </a>
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
    <div class="d-flex justify-content-md-end mb-2">
        @if($data->canCreate())
            @permission(\Illuminate\Support\Str::kebab($model_name).'-create')
            <a href="{{custom_route(\Illuminate\Support\Str::kebab($model_name).'-create')}}"
               class="ms-1 btn btn-primary mt-2">
                <span class="btn-label"><i class="mdi mdi-plus"></i></span>{{$model->trans['create']}}
            </a>
            @endpermission
        @endif
        @if($languages->count() > 1)
            @includeFirst(['admin._inc.templates.localLanguage', 'praust::admin._inc.templates.localLanguage'])
        @endif
    </div>

    {{-- Warranty alert --}}
    @include('admin.reclamation.partials.warranty-alert')

    <div class="card">
        <form action="{{custom_route(\Illuminate\Support\Str::kebab($model_name).'-update', ['id' => $data->getKey()])}}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @if(count($rules))
                <input type="hidden" name="rules" value="@json($rules)"/>
            @endif
            <div class="card-body">
                @if($availableTabsLang->count() > 1)
                    <div class="d-flex justify-content-between">
                        @if($model->editStyle == 'column')
                            <div></div>
                        @else
                            <ul class="nav nav-tabs nav-bordered">
                                @foreach($availableTabsLang as $i => $subTab)
                                    <li class="nav-item">
                                        <a class="nav-link {{!$i ? 'active' : ''}}" data-bs-toggle="tab"
                                           href="#module-{{$subTab['view']}}" role="tab">
                                            @cat($subTab['name'])
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                @includeFirst(['admin._inc.content', 'praust::admin._inc.content'])
                @if($data->isActive())
                    <button type="submit" name="save_and_active" class="btn btn-primary"><span class="btn-label"><i
                                class="fas fa-check"></i></span>@cat('Potwierdź zrealizowanie zadania')
                    </button>
                @endif
            </div>
            <div class="card-footer">
                <div class="button-list text-center text-md-start">
                    <button type="submit" class="btn btn-primary"><span class="btn-label"><i
                                    class="far fa-save"></i></span>@cat('Zapisz zmiany')
                    </button>
                    <button type="submit" name="save_and_stay" class="btn btn-primary"><span class="btn-label"><i
                                    class="far fa-save"></i></span>@cat('Zapisz i zostań')
                    </button>
                    @if($data->canCreate())
                        @permission(\Illuminate\Support\Str::kebab($model_name).'-create')
                        <button type="submit" name="save_and_add" class="btn btn-primary"><span class="btn-label"><i
                                        class="far fa-save"></i></span>@cat('Zapisz i dodaj nowy')
                        </button>
                        @endpermission
                    @endif
                    <a href="{{url()->previous()}}" class="btn btn-link">@cat('Anuluj zmiany')</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Attachments preview --}}
    @include('admin.reclamation.partials.attachments-preview')

    {{-- Email panel --}}
    @include('admin.reclamation.partials.email-panel')

    {{-- Notes timeline --}}
    @include('admin.reclamation.partials.notes')
@endsection
