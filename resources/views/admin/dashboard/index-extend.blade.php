@include('praust::admin._inc.templates.widgets')

@php $events = collect(); @endphp
@php $calendars = (new \App\Models\Calendar())->newQuery()->where('user_id', request()->input('user_id', auth()->id()))->orWhere('created_by', auth()->id())->get(); @endphp
@php $users = (new \App\Models\User())->newQuery()->get(); @endphp
@foreach($calendars as $calendar)
    @php $temp = [
     'id' => $calendar->getKey(),
     'url' => custom_route('calendar-edit', ['id' => $calendar->getKey()]),
     'title' => $calendar->getName(),
     'start' => $calendar->created_at,
     'end' => $calendar->created_at->addHour(),
     'className' => ($calendar->created_by == auth()->id() && $calendar->user_id != auth()->id() ? 'bg-warning' : '').' '.($calendar->type == \App\Models\Enums\CalendarType::TYPE_NOTE->name ? 'bg-success' : 'bg-info').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_LOW->name ? 'task-low' : '').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_NORMAL->name ? 'task-medium' : '').' '.($calendar->priority == \App\Models\Enums\Priority::PRIORITY_HIGH->name ? 'task-high' : '').' '.($calendar->isActive() ? '' : 'disabled')
     ]; @endphp
    @php $events->push($temp); @endphp
@endforeach

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div> <!-- end card body-->
</div> <!-- end card -->

@if(auth()->user()->hasPermission('user-read'))
    @php $users = (new \App\Models\User())->newQuery()->where('id', '!=', 11)->get(); @endphp
@else
    @php $users = (new \App\Models\User())->newQuery()->where('id', '!=', 11)->where('all_see', 1)->order()->get(); @endphp
@endif
@if($users->count() > 1)
    <div class="d-flex align-items-end flex-wrap mt-3">
        <div>
            <label for="" class="mb-0">Użytkownik:</label>
            <select name="user_id" data-toggle="select2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach($users as $user)
                    <option value="{{request()->fullUrlWithQuery(['user_id' => $user->getKey()])}}" @selected($user->getKey() == request()->input('user_id', auth()->id()))>{{$user->getAdminName()}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif

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
                window.location.href = '{{custom_route('calendar-create')}}?created_at=' + info.dateStr;
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
                    editable: true,
                    droppable: true, // this allows things to be dropped onto the calendar !!!
                    eventLimit: true, // allow "more" link when too many events
                    selectable: true,
                    dateClick: function (info) {
                        $this.onSelect(info);
                    },
                    eventClick: function(info) {
                        $this.onEventClick(info);
                    },
                    eventDrop: function(eventDropInfo) {
                        console.log(eventDropInfo);
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
