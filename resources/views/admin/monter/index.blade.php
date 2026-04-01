@extends('praust::admin.layout')
@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">
                    Grafik monterów
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
    @php $events = collect(); @endphp
    @php $user_id = 11; @endphp
    @php $calendars = (new \App\Models\Calendar())->newQuery()->where('user_id', $user_id)->orWhere('created_by', $user_id)->get(); @endphp
    @php $users = (new \App\Models\User())->newQuery()->get(); @endphp
    @foreach($calendars as $calendar)
        @php $temp = [
         'id' => $calendar->getKey(),
         'url' => auth()->id() == 3 ? custom_route('calendar-edit', ['id' => $calendar->getKey()]) : '#0',
         'title' => $calendar->getName(),
         'start' => $calendar->created_at,
         'end' => $calendar->created_at->addHour(),
         'className' => ($calendar->created_by == $user_id && $calendar->user_id != $user_id ? 'bg-warning' : '').' '.($calendar->type == \App\Models\Enums\CalendarType::TYPE_NOTE->name ? 'bg-success' : 'bg-info').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_LOW->name ? 'task-low' : '').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_NORMAL->name ? 'task-medium' : '').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name ? 'task-high' : '').' '.($calendar->isActive() ? '' : 'disabled')
         ]; @endphp
        @php $events->push($temp); @endphp
    @endforeach

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div> <!-- end card body-->
    </div> <!-- end card -->

    <!-- Add New Event MODAL -->
    <div class="modal fade" id="event-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                    <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title" id="modal-title">Dodaj do kalendarza</h5>
                </div>
                <div class="modal-body px-4 pb-4 pt-0">
                    <form class="needs-validation" name="event-form" id="form-event" novalidate>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-2">
                                    <label class="control-label form-label">Nazwa</label>
                                    <input class="form-control" placeholder="Nazwa wydarzenia"
                                           type="text" name="title" id="event-title" required />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-2">
                                    <label class="control-label form-label">Typ</label>
                                    <select class="form-control form-select" name="category"
                                            id="event-category" required>
                                        <option value="bg-success">Notatka</option>
                                        <option value="bg-info">Zadanie</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-danger" id="btn-delete-event">Usuń</button>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Zamknij</button>
                                <button type="submit" class="btn btn-success" id="btn-save-event">Zapisz</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal-content-->
        </div> <!-- end modal dialog-->
    </div>
    <!-- end modal-->
@endsection
@push('styles')
    <link href="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/core/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/daygrid/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/bootstrap/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/timegrid/main.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/list/main.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/moment/min/moment.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/core/main.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/core/locales/pl.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/bootstrap/main.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/daygrid/main.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/timegrid/main.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/list/main.min.js')}}"></script>
    <script src="{{getClass('Configuration')::getAdminPath('default/assets/libs/@fullcalendar/interaction/main.min.js')}}"></script>

    <script>
        !function($) {
            "use strict";

            var CalendarApp = function() {
                this.$body = $("body")
                this.$modal = $('#event-modal'),
                this.$calendar = $('#calendar'),
                this.$formEvent = $("#form-event"),
                this.$btnNewEvent = $("#btn-new-event"),
                this.$btnDeleteEvent = $("#btn-delete-event"),
                this.$btnSaveEvent = $("#btn-save-event"),
                this.$modalTitle = $("#modal-title"),
                this.$calendarObj = null,
                this.$selectedEvent = null,
                this.$newEventData = null
            };

            /* on select */
            CalendarApp.prototype.onSelect = function (info) {
                @if(auth()->id() == 3)
                    window.location.href = '{{custom_route('calendar-create')}}?created_at=' + info.dateStr;
                @endif
            };

            /* Initializing */
            CalendarApp.prototype.init = function() {
                var $this = this;

                // cal - init
                $this.$calendarObj = new FullCalendar.Calendar($this.$calendar[0], {
                    locale: 'pl',
                    plugins: [ 'bootstrap', 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                    slotDuration: '00:15:00',
                    minTime: '08:00:00',
                    maxTime: '19:00:00',
                    themeSystem: 'bootstrap',
                    bootstrapFontAwesome: false,
                    defaultView: 'dayGridMonth',
                    handleWindowResize: true,
                    //eventStartEditable: false,
                    //height: $(window).height() + 300,
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    events: @json($events),
                    editable: {{auth()->id() == 3 ? "true" : "false" }},
                    droppable: {{auth()->id() == 3 ? "true" : "false" }}, // this allows things to be dropped onto the calendar !!!
                    eventLimit: {{auth()->id() == 3 ? "true" : "false" }}, // allow "more" link when too many events
                    selectable: {{auth()->id() == 3 ? "true" : "false" }},
                    dateClick: function (info) {
                        $this.onSelect(info);
                    },
                    eventClick: function(info) {
                        $this.onEventClick(info);
                    },
                    eventDrop: function(eventDropInfo) {
                        @if(auth()->id() == 3)
                            $.ajax({
                                url: base_url + "{{config('praust.admin_path')}}/calendar/" + eventDropInfo.event.id + "/move",
                                type: 'POST',
                                data: {
                                    '_token': "{{csrf_token()}}",
                                    'to': eventDropInfo.event.end.toISOString(),
                                },
                                success: function () {
                                    $.NotificationApp.send("Udało się!", "Kolejność zmieniona poprawnie!", 'top-right', '#5ba035', 'success');
                                }
                            });
                        @endif
                    }
                });

                $this.$calendarObj.render();
            };

            //init CalendarApp
            $.CalendarApp = new CalendarApp;
            $.CalendarApp.Constructor = CalendarApp;
        }(window.jQuery);

        !function($) {
            "use strict";
            $.CalendarApp.init()
        }(window.jQuery);
    </script>
@endpush
