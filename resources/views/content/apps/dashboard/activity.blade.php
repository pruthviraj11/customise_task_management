@extends('layouts/contentLayoutMaster')

@section('title', 'Activity')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection
@section('content')
    <div class="content-body">
        <!-- Timeline Starts -->
        <section class="basic-timeline">
            <div class="row">
                <div class="col-lg-12 col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic</h4>
                        </div>
                        <div class="card-body">
                            <ul class="timeline">
                                @foreach ($activityLogs as $activityLog)
                                    @if ($activityLog)
                                        <li class="timeline-item">
                                            <span
                                                class="timeline-point timeline-point-success timeline-point-indicator"></span>
                                            <div class="timeline-event">
                                                <div
                                                    class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                    <h6>
                                                        @if ($activityLog->subject_type == 'App\Models\Task')
                                                            Task id is: {{ $activityLog->subject_id }},
                                                        @endif {{ $activityLog->description }}
                                                    </h6>
                                                    <span
                                                        class="timeline-event-time">{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</span>
                                                </div>
                                                @php
                                                    $properties = json_decode($activityLog->properties, true);
                                                    $attributes = isset($properties['attributes'])
                                                        ? $properties['attributes']
                                                        : [];
                                                    $causerId = $activityLog->causer_id;
                                                    $user = \App\Models\User::find($causerId); // Assuming User model namespace is App\Models\User
                                                @endphp
                                                <div class="row">
                                                    <div class="d-flex flex-row align-items-center col-3">
                                                        <div class="avatar">
                                                            @if ($user->profile_img)
                                                                @if ($user->profile_img = '')
                                                                    <img src="{{ Storage::url($user->profile_img) }}"
                                                                        alt="avatar" height="38" width="38">
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="ms-50">
                                                            <h6 class="mb-0">{{ $user->first_name }}
                                                                {{ $user->last_name }}</h6>
                                                            <span>{{ $user->authorization }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-50">Click below to see details.</p>
                                                        <button class="btn btn-outline-primary btn-sm" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapseExample{{ $activityLog->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="collapseExample{{ $activityLog->id }}">
                                                            Show Report
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="collapse" id="collapseExample{{ $activityLog->id }}">
                                                    {{-- <ul class="list-group list-group-flush mt-1">
                                                        <div class="row">
                                                            @if ($activityLog->event === 'updated')
                                                                @php
                                                                    $properties = json_decode(
                                                                        $activityLog->properties,
                                                                        true,
                                                                    );

                                                                    $old = isset($properties['old'])
                                                                        ? $properties['old']
                                                                        : [];
                                                                    $attributes = isset($properties['attributes'])
                                                                        ? $properties['attributes']
                                                                        : [];

                                                                @endphp
                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>Old Data:</span>
                                                                    </li>
                                                                    @foreach ($old as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                                : <span
                                                                                    class="fw-bold">{{ $value }}</span></span>
                                                                        </li>
                                                                    @endforeach
                                                                </div>

                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>New Data:</span>
                                                                    </li>
                                                                    @foreach ($attributes as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                                : <span
                                                                                    class="fw-bold">{{ $value }}</span></span>
                                                                        </li>
                                                                    @endforeach
                                                                </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $properties = json_decode($activityLog->properties, true);
                                                            $attributes = isset($properties['attributes'])
                                                                ? $properties['attributes']
                                                                : [];
                                                        @endphp
                                                        @foreach ($attributes as $key => $value)
                                                            <li
                                                                class="list-group-item d-flex justify-content-between flex-wrap">
                                                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }} : <span
                                                                        class="fw-bold">{{ $value }}</span></span>
                                                            </li>
                                                        @endforeach
                                    @endif
                            </ul> --}}
                                                    <ul class="list-group list-group-flush mt-1">
                                                        <div class="row">
                                                            @if ($activityLog->event === 'updated')
                                                                @php
                                                                    $properties = json_decode(
                                                                        $activityLog->properties,
                                                                        true,
                                                                    );

                                                                    $old = isset($properties['old'])
                                                                        ? $properties['old']
                                                                        : [];
                                                                    $attributes = isset($properties['attributes'])
                                                                        ? $properties['attributes']
                                                                        : [];

                                                                    // Fetching old and new project names directly
                                                                    $oldProjectName = isset($old['project_id'])
                                                                        ? \App\Models\Project::find($old['project_id'])
                                                                                ->project_name ?? null
                                                                        : null;
                                                                    $newProjectName = isset($attributes['project_id'])
                                                                        ? \App\Models\Project::find(
                                                                                $attributes['project_id'],
                                                                            )->project_name ?? null
                                                                        : null;

                                                                    // Fetching old and new priority names directly
                                                                    $oldPriorityName = isset($old['priority_id'])
                                                                        ? \App\Models\Priority::find(
                                                                                $old['priority_id'],
                                                                            )->priority_name ?? null
                                                                        : null;
                                                                    $newPriorityName = isset($attributes['priority_id'])
                                                                        ? \App\Models\Priority::find(
                                                                                $attributes['priority_id'],
                                                                            )->priority_name ?? null
                                                                        : null;

                                                                    // Fetching old and new task status directly
                                                                    $oldTaskStatusName = isset($old['task_status'])
                                                                        ? \App\Models\Status::find($old['task_status'])
                                                                                ->status_name ?? null
                                                                        : null;
                                                                    $newTaskStatusName = isset(
                                                                        $attributes['task_status'],
                                                                    )
                                                                        ? \App\Models\Status::find(
                                                                                $attributes['task_status'],
                                                                            )->status_name ?? null
                                                                        : null;

                                                                    // Fetching old and new department names directly
                                                                    $oldDepartmentName = isset($old['department_id'])
                                                                        ? \App\Models\Department::find(
                                                                                $old['department_id'],
                                                                            )->department_name ?? null
                                                                        : null;
                                                                    $newDepartmentName = isset(
                                                                        $attributes['department_id'],
                                                                    )
                                                                        ? \App\Models\Department::find(
                                                                                $attributes['department_id'],
                                                                            )->department_name ?? null
                                                                        : null;

                                                                    // Fetching old and new sub-department names directly
                                                                    $oldSubDepartmentName = isset(
                                                                        $old['sub_department_id'],
                                                                    )
                                                                        ? \App\Models\SubDepartment::find(
                                                                                $old['sub_department_id'],
                                                                            )->sub_department_name ?? null
                                                                        : null;
                                                                    $newSubDepartmentName = isset(
                                                                        $attributes['sub_department_id'],
                                                                    )
                                                                        ? \App\Models\SubDepartment::find(
                                                                                $attributes['sub_department_id'],
                                                                            )->sub_department_name ?? null
                                                                        : null;

                                                                @endphp
                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>Old Data:</span>
                                                                    </li>
                                                                    @foreach ($old as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_', ' ', str_replace('id', ' ', $key))) }}:
                                                                                <span class="fw-bold">
                                                                                    @if ($key == 'project_id')
                                                                                        {{ $oldProjectName }}
                                                                                    @elseif($key == 'priority_id')
                                                                                        {{ $oldPriorityName }}
                                                                                    @elseif($key === 'task_status')
                                                                                        {{ $oldTaskStatusName }}
                                                                                    @elseif($key === 'department_id')
                                                                                        {{ $oldDepartmentName }}
                                                                                    @elseif($key === 'sub_department_id')
                                                                                        {{ $oldSubDepartmentName }}
                                                                                    @else
                                                                                        {{ $value }}
                                                                                    @endif
                                                                                </span>
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                </div>

                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>New Data:</span>
                                                                    </li>
                                                                    @foreach ($attributes as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_id', '_', str_replace('_', ' ', $key))) }}:

                                                                                <span class="fw-bold">
                                                                                    @if ($key === 'project_id')
                                                                                        {{ $newProjectName }}
                                                                                    @elseif($key === 'priority_id')
                                                                                        {{ $newPriorityName }}
                                                                                    @elseif($key === 'task_status')
                                                                                        {{ $newTaskStatusName }}
                                                                                    @elseif($key === 'department_id')
                                                                                        {{ $newDepartmentName }}
                                                                                    @elseif($key === 'sub_department_id')
                                                                                        {{ $newSubDepartmentName }}
                                                                                    @else
                                                                                        {{ $value }}
                                                                                    @endif
                                                                                </span>
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                @php
                                                                    $properties = json_decode(
                                                                        $activityLog->properties,
                                                                        true,
                                                                    );
                                                                    $attributes = isset($properties['attributes'])
                                                                        ? $properties['attributes']
                                                                        : [];

                                                                    // Fetching new project name directly
                                                                    $projectName = isset($attributes['project_id'])
                                                                        ? \App\Models\Project::find(
                                                                                $attributes['project_id'],
                                                                            )->project_name ?? null
                                                                        : null;
                                                                    // dump($attributes);
                                                                @endphp
                                                                @foreach ($attributes as $key => $value)
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>{{ ucfirst(str_replace('_id', ' ', $key)) }}:
                                                                            <span class="fw-bold">
                                                                                @if ($key === 'project_id')
                                                                                    {{ $projectName }}
                                                                                @else
                                                                                    {{ $value }}
                                                                                @endif
                                                                            </span>
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </ul>

                                                </div>
                                            </div>
                                        </li>
                                    @else
                                        <p>Activity log entry not found.</p>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- working code

            <section class="basic-timeline">
            <div class="row">
                <div class="col-lg-12 col">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic</h4>
                        </div>
                        <div class="card-body">
                            <ul class="timeline">




                                @foreach ($activityLogs as $activityLog)
                                    @if ($activityLog)
                                        <div class="timeline-item">
                                            <span
                                                class="timeline-point timeline-point-success timeline-point-indicator"></span>
                                            <div class="timeline-event">
                                                <div
                                                    class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                    <h6>{{ $activityLog->description }}</h6>
                                                    <span
                                                        class="timeline-event-time">{{ $activityLog->created_at->diffForHumans() }}</span>
                                                </div>
                                                @php
                                                    $properties = json_decode($activityLog->properties, true);
                                                    $attributes = isset($properties['attributes'])
                                                        ? $properties['attributes']
                                                        : [];
                                                    $causerId = $activityLog->causer_id;
                                                    $user = \App\Models\User::find($causerId); // Assuming User model namespace is App\Models\User
                                                @endphp
                                                <div class="row">
                                                    <div class="d-flex flex-row align-items-center col-3">
                                                        <div class="avatar">
                                                            <img src="{{ Storage::url($user->profile_img) }}" alt="avatar"
                                                                height="38" width="38">
                                                        </div>
                                                        <div class="ms-50">
                                                            <h6 class="mb-0">{{ $user->first_name }}
                                                                {{ $user->last_name }}</h6>
                                                            <span>{{ $user->authorization }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-50">Click below to see details.
                                                        </p>
                                                        <button class="btn btn-outline-primary btn-sm" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapseExample{{ $activityLog->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="collapseExample{{ $activityLog->id }}">
                                                            Show Report
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="collapse" id="collapseExample{{ $activityLog->id }}">
                                                    <ul class="list-group list-group-flush mt-1">
                                                        <div class="row">
                                                            @if ($activityLog->event === 'updated')
                                                                @php
                                                                    $properties = json_decode(
                                                                        $activityLog->properties,
                                                                        true,
                                                                    );
                                                                    $old = isset($properties['old'])
                                                                        ? $properties['old']
                                                                        : [];
                                                                    $attributes = isset($properties['attributes'])
                                                                        ? $properties['attributes']
                                                                        : [];
                                                                @endphp
                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>Old Data:</span>
                                                                    </li>
                                                                    @foreach ($old as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                                :
                                                                                <span
                                                                                    class="fw-bold">{{ $value }}</span></span>

                                                                        </li>
                                                                    @endforeach
                                                                </div>

                                                                <div class="col-6">
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between flex-wrap">
                                                                        <span>New Data:</span>
                                                                    </li>
                                                                    @foreach ($attributes as $key => $value)
                                                                        <li
                                                                            class="list-group-item d-flex justify-content-between flex-wrap">
                                                                            <span>{{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                                :
                                                                                <span
                                                                                    class="fw-bold">{{ $value }}</span></span>

                                                                        </li>
                                                                    @endforeach
                                                                </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $properties = json_decode($activityLog->properties, true);
                                                            $attributes = isset($properties['attributes'])
                                                                ? $properties['attributes']
                                                                : [];
                                                        @endphp
                                                        @foreach ($attributes as $key => $value)
                                                            <li
                                                                class="list-group-item d-flex justify-content-between flex-wrap">
                                                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }} :
                                                                    <span class="fw-bold">{{ $value }}</span></span>

                                                            </li>
                                                        @endforeach
                                    @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <p>Activity log entry not found.</p>
                @endif
                @endforeach
                </ul>
            </div>
    </div>
    </div>

    </div>
    </section> --}}
        <!-- Timeline Ends -->

    </div>

@endsection
@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
    <script>
        // Function to generate a random password
        function generateRandomPassword(length) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            let password = "";
            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset.charAt(randomIndex);
            }
            return password;
        }
    </script>
@endsection
