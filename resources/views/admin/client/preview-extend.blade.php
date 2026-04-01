
@php
    $timelines = collect();
    $timelines->push($data->calendars);
    $timelines->push($data->reclamations);
    $timelines->push($data->processes);
    $timelines->push($data->sales);
    $timelines->push($data->documents);
    $timelines = $timelines->collapse();
    $timelines = $timelines->sortBy('created_at');
    $future = $timelines->where('created_at', '>=', now());
    $yesterday = $timelines->where('created_at', '<=', now());
@endphp

<div class="row">
    <div class="col-12">
        <div class="timeline" dir="ltr">
            @if($yesterday->count())
                <article class="timeline-item">
                    <h2 class="m-0 d-none">&nbsp;</h2>
                    <div class="time-show mt-0">
                        <div class="btn btn-primary width-lg">Przeszłość</div>
                    </div>
                </article>

                @foreach($yesterday as $timeline)
                    <article class="timeline-item {{$loop->even ? 'timeline-item-left' : ''}}">
                        <div class="timeline-desk">
                            <div class="timeline-box">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                <a href="{{custom_route(Str::of($timeline->getModelName())->lower()->snake().'-edit', ['id' => $timeline->getKey()])}}" class="mt-0 font-16">{{$timeline->created_at->format("d-m-Y H:i:s")}}</a>
                                <p class="mb-0">
                                    {{$timeline->getAdminName()}}
                                </p>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endif

            <article class="timeline-item">
                <h2 class="m-0 d-none">&nbsp;</h2>
                <div class="time-show mt-0">
                    <div class="btn btn-primary width-lg">Przyszłość</div>
                </div>
            </article>

            @foreach($future as $timeline)
                <article class="timeline-item {{$loop->even ? 'timeline-item-left' : ''}}">
                    <div class="timeline-desk">
                        <div class="timeline-box">
                            <span class="arrow-alt"></span>
                            <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                            <a href="{{custom_route(Str::of($timeline->getModelName())->lower()->snake().'-edit', ['id' => $timeline->getKey()])}}" class="mt-0 font-16">{{$timeline->created_at->format("d-m-Y H:i:s")}}</a>
                            <p class="mb-0">
                                {{$timeline->getAdminName()}}
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <!-- end timeline -->
    </div> <!-- end col -->
</div>
<!-- end row -->

@if($data->clients->count())
    <div class="card">
        <div class="card-body">
            <h4>Poleceni klienci</h4>
            <ul class="mb-0">
                @foreach($data->clients as $client)
                    <li>{{($client->company_name ? '('.$client->company_name.') ' : '').$client->getName()}}</li>
                @endforeach
            </ul>
        </div> <!-- end card body-->
    </div> <!-- end card -->
@endif
