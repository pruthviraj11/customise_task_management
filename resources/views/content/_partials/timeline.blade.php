<!-- Activity Timeline -->
<div class="card activity">
    <h4 class="card-header">Activity</h4>
    <div class="card-body pt-1">
        <ul class="timeline ms-50">
            @foreach(getTimeLine($inquiry->id) as $data)
                <li class="timeline-item">
                    <span class="timeline-point {{$data->point}} timeline-point-indicator"></span>
                    <div class="timeline-event">
                        <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                            <h6>{{ $data->agent_first.' '.$data->agent_last }}</h6>
                            <span class="timeline-event-time me-1">{{ $data->time_ago }}</span>
                        </div>
                        <h5>{{ $data->first_name.' '.$data->last_name }}</h5>
                        <p>{{ $data->description }}</p>
                        @if($data->follow_up_description)
                            <hr>
                            <h6>{{ $data->follow_up_description }}</h6>
                            <p><strong>Note: </strong>{{ $data->follow_up_note }}</p>
                        @endif
                        @if($data->package_name)
                            <hr>
                            <h6>{{ $data->package_name }}</h6>
{{--                            <p><strong>Status: </strong>{{ $data->inquiry_status_name }}</p>--}}
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
<!-- /Activity Timeline -->
