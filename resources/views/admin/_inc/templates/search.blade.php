<div class="me-2 mt-2">
    <label for="" class="mb-0">@cat("Szukaj"):</label>
    <input type="text" name="search[search]" class="form-control" value="{{request()->input('search.search')}}">
</div>
@if(auth()->user()->hasPermission('user-read'))
    @php
        $users = (new \App\Models\User())->newQuery()->order()->get();
    @endphp
@else
    @php
        $users = (new \App\Models\User())->newQuery()->where('all_see', 1)->order()->get();
    @endphp
@endif
@if($users->count() > 1 && $model->hasTrait(\App\Models\Concerns\User::class))
    <div class="me-2 mt-2">
        <label for="" class="mb-0">@cat("Użytkownik"):</label>
        <select name="search[user_id]" data-toggle="select2">
            @foreach($users as $user)
                <option
                    value="{{$user->getKey()}}" @selected(request()->input('search.user_id', auth()->id()) == $user->getKey())>
                    {{$user->getAdminName()}}
                </option>
            @endforeach
        </select>
    </div>
@endif
@if(view()->exists('admin.'.Str::kebab($model_name).'.search-extend'))
    @include('admin.'.Str::kebab($model_name).'.search-extend')
@endif
<div class="d-flex align-items-end me-2 mt-2">
    <button type="submit" class="btn btn-primary"><span class="btn-label"><i
                    class="fas fa-search"></i></span>@cat("Szukaj")</button>
</div>
@if(request()->has('search'))
    <div class="me-2 d-flex align-items-end mt-2">
        <a href="{{request()->fullUrlWithQuery(['page' => null, 'search' => null, 'perPage' => null])}}"
           class="btn btn-primary clear-filter">Wyczyść</a>
    </div>
@endif
