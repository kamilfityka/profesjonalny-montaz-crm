@php $users = (new \App\Models\User())->newQuery()->active()->order()->get(); @endphp
@php $client_categories = (new \App\Models\Client())->newQuery()->active()->range($date, $range, $date_end)->order()->groupBy('source')->pluck('source'); @endphp
@php $processes = (new \App\Models\Process())->newQuery()->active()->order()->range($date, $range, $date_end)->get(); @endphp
@php $process_categories = (new \App\Models\ProcessCategory())->newQuery()->withWhereHas('category_childrens', fn($query) => $query->range($date, $range, $date_end))->active()->order()->get(); @endphp
@php $reclamation_categories = (new \App\Models\ReclamationCategory())->newQuery()->withWhereHas('category_childrens', fn($query) => $query->range($date, $range, $date_end))->active()->order()->get(); @endphp
@php $sale_categories = (new \App\Models\SaleCategory())->newQuery()->active()->withWhereHas('category_childrens', fn($query) => $query->range($date, $range, $date_end))->order()->get(); @endphp
@php $sale_loses = (new \App\Models\Sale())->newQuery()->withTrashed()->range($date, $range, $date_end)->whereNotNull('lose_reason')->get(); @endphp
@php $sale_wines = (new \App\Models\Sale())->newQuery()->withTrashed()->range($date, $range, $date_end)->whereNotNull('win_reason')->get(); @endphp

<div class="card">
    <div class="card-body">
        <h4>Szanse sprzedaży</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach($sale_categories as $sale_category)
                        <th class="text-center" style="background-color: {{\Illuminate\Support\Str::start($sale_category->bgColor,'#')}};">{{$sale_category->getAdminName()}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach($sale_categories as $sale_category)
                            <td class="text-center" style="background-color: {{\Illuminate\Support\Str::start($sale_category->bgColor,'#')}};">
                                {{$sale_category->category_childrens->where('user_id', $user->getKey())->count()}}<br>
                                {!! number_format($sale_category->category_childrens->where('user_id', $user->getKey())->sum('value'), 2, ',', '&nbsp;') !!}&nbsp;zł
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Procesy</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach($process_categories as $process_category)
                        <th class="text-center" style="background-color: {{\Illuminate\Support\Str::start($process_category->bgColor,'#')}};">{{$process_category->getAdminName()}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach($process_categories as $process_category)
                            <td class="text-center" style="background-color: {{\Illuminate\Support\Str::start($process_category->bgColor,'#')}};">
                                {{$process_category->category_childrens->where('user_id', $user->getKey())->count()}}<br>
                                {!! number_format($process_category->category_childrens->where('user_id', $user->getKey())->sum('value'), 2, ',', '&nbsp;') !!}&nbsp;zł
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Podpisane umowy</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    <th>Liczba</th>
                </tr>
                @foreach($users as $user)
                    @continue(!$processes->where('user_id', $user->getKey())->count())
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        <td class="text-center">{{$processes->where('user_id', $user->getKey())->count()}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Reklamacje</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach($reclamation_categories as $reclamation_category)
                        <th class="text-center" style="background-color: {{\Illuminate\Support\Str::start($reclamation_category->bgColor,'#')}};">{{$reclamation_category->getAdminName()}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach($reclamation_categories as $reclamation_category)
                            <td class="text-center" style="background-color: {{\Illuminate\Support\Str::start($reclamation_category->bgColor,'#')}};">{{$reclamation_category->category_childrens->where('user_id', $user->getKey())->count()}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Przegrane</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach(\App\Models\Sale::$lose_reasons as $reason)
                        <th class="text-center">{{Str::ucfirst($reason)}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    @continue(!$sale_loses->where('user_id', $user->getKey())->count())
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach(\App\Models\Sale::$lose_reasons as $reason)
                            <td class="text-center">{{$sale_loses->where('user_id', $user->getKey())->where('lose_reason', $reason)->count()}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Wygrane</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach(\App\Models\Sale::$win_reasons as $reason)
                        <th class="text-center">{{Str::ucfirst($reason)}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    @continue(!$sale_wines->where('user_id', $user->getKey())->count())
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach(\App\Models\Sale::$win_reasons as $reason)
                            <td class="text-center">{{$sale_wines->where('user_id', $user->getKey())->where('win_reason', $reason)->count()}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Raport wg. użytkowników</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    <th class="text-center">Kontakty</th>
                    <th class="text-center">Zadania</th>
                    <th class="text-center"><i class="fe-check-square" title="Procent zrealizowanych zadań"></i></th>
                    {{--                        <th>Oferty</th>--}}
                    {{--                        <th>Zlecenia</th>--}}
                    {{--                        <th>x</th>--}}
                    {{--                        <th>Realizacje</th>--}}
                    {{--                        <th>Realizacje<br>Zakończone</th>--}}
                    <th class="text-center">Aktywne<br>szanse<br>sprzedaży</th>
                    <th class="text-center">Wygrane</th>
                    <th class="text-end">Wartość</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        <td class="text-center">{{$user->clients->count()}}</td>
                        <td class="text-center">{{$user->calendars->count()}}</td>
                        <td class="text-center">{{$user->calendars->count() ? round(($user->calendars->where('active', false)->count()/$user->calendars->count())*100, 2).'%' : '0%'}}</td>
                        <td class="text-center">{{$user->sales->whereNull('win_reason')->count()}}</td>
                        <td class="text-center">{{$user->sales->whereNotNull('win_reason')->count()}}</td>
                        <td class="text-end">{!! number_format($user->sales->sum('value'), 2, ',', '&nbsp;') !!}&nbsp;zł</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="new-page"></div>
<div class="card">
    <div class="card-body">
        <h4>Źródło pozyskania</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Użytkownik</th>
                    @foreach($client_categories as $client_category)
                        <th class="text-center">{{$client_category}}</th>
                    @endforeach
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{$user->getAdminName()}}</td>
                        @foreach($client_categories as $client_category)
                            <td class="text-center">{{$user->clients->where('source', $client_category)->count()}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
