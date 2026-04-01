@extends('praust::admin.layout')
@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Statystyki
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
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center" style="margin-bottom: -0.75rem;">
                <div class="btn-group flex-wrap me-1 mb-2">
                    <a href="{{request()->fullUrlWithQuery(['range' => 'day', 'date_end' => null])}}"
                       class="btn {{$range == 'day' ? 'btn-primary' : 'btn-light'}}">Dzień</a>
                    <a href="{{request()->fullUrlWithQuery(['range' => 'week', 'date_end' => null])}}"
                       class="btn {{$range == 'week' ? 'btn-primary' : 'btn-light'}}">Tydzień</a>
                    <a href="{{request()->fullUrlWithQuery(['range' => 'month', 'date_end' => null])}}"
                       class="btn {{$range == 'month' ? 'btn-primary' : 'btn-light'}}">Miesiąc</a>
                    <a href="{{request()->fullUrlWithQuery(['range' => 'quarter', 'date_end' => null])}}"
                       class="btn {{$range == 'quarter' ? 'btn-primary' : 'btn-light'}}">Kwartał</a>
                    <a href="{{request()->fullUrlWithQuery(['range' => 'year', 'date_end' => null])}}"
                       class="btn {{$range == 'year' ? 'btn-primary' : 'btn-light'}}">Rok</a>
                    <a href="{{request()->fullUrlWithQuery(['range' => 'range'])}}"
                       class="btn {{$range == 'range' ? 'btn-primary' : 'btn-light'}}">Zakres</a>
                </div>
                <div class="form-inline mx-1 mb-2">
                    @if($range == 'range')
                        <input type="text" id="date-picker" autocomplete="off" name="datefilter" class="form-control" value="{{$date->format('d-m-Y')}} - {{$date_end->format('d-m-Y')}}" />
                    @else
                        <input type="text" name="date" autocomplete="off" class="form-control data-picker" value="{{$date->format('d-m-Y')}}">
                    @endif
                </div>
                <div class="form-inline mx-1 mb-2">
                    @if($range == 'day')
                        Dzień: {{$date->format('d-m-Y')}}
                    @elseif($range == 'week')
                        Tydzień: {{$date->copy()->startOfWeek()->format('d-m-Y')}} - {{$date->copy()->endOfWeek()->format('d-m-Y')}}
                    @elseif($range == 'month')
                        Miesiąc: {{Str::ucfirst($date->monthName)}} {{$date->year}}
                    @elseif($range == 'quarter')
                        Kwartał: {{$date->copy()->startOfQuarter()->format('d-m-Y')}} - {{$date->copy()->endOfQuarter()->format('d-m-Y')}}
                    @elseif($range == 'year')
                        Rok: {{$date->year}}
                    @endif
                </div>
                <div class="form-inline mx-1 mb-2">
                    <a href="{{custom_route('statistic-pdf', ['range' => $range, 'date' => $date->format('d-m-Y'), 'date_end' => $date_end->format('d-m-Y')])}}" class="btn btn-primary">Pobierz PDF</a>
                </div>
            </div>
        </div>
    </div>
    @include('admin.statistic.partials.tables')
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .daterangepicker .calendar-table td,.daterangepicker .calendar-table th{height:32px}
        .daterangepicker td.active,.daterangepicker td.active:hover{background-color:#28baa5}
        .daterangepicker td.in-range{background-color:#f6f6f6}
        .daterangepicker td.in-range.active{background-color:#28baa5}
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/noframework.waypoints.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".data-picker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-mm-yy',
                onSelect: function (selectedDate) {
                    window.location = '{{request()->fullUrlWithoutQuery(['range', 'date', 'date_start', 'date_end'])}}?range={{$range}}&date=' + selectedDate;
                }
            });

            $('#date-picker').daterangepicker({
                opens: 'left',
                "locale": {
                    "applyLabel": "Zastosuj",
                    "cancelLabel": "Anuluj",
                    "fromLabel": "Od",
                    "toLabel": "Do",
                    daysOfWeek: ['Ndz', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob'],
                    monthNames: ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'],
                    firstDay: 1,
                    format: 'DD-MM-YYYY'
                }
            }, function(start, end, label) {
                window.location = '{{request()->fullUrlWithoutQuery(['range', 'date', 'date_start', 'date_end'])}}?range={{$range}}&date=' + start.format('DD-MM-YYYY') + '&date_end=' + end.format('DD-MM-YYYY');
            });
        });
    </script>
@endpush
