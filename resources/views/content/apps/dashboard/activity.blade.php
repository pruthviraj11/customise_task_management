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
                            <h4 class="card-title">Activity Log</h4>
                            {{-- <input type="text" id="searchInput" class="form-control mb-3"
                                placeholder="Search activities..."> --}}
                            <div class="card-header d-flex flex-column align-items-start gap-2">

                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Search activities...">
                            </div>

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
                                                                    $oldSubDepartmentName = isset($old['subdepartment'])
                                                                        ? \App\Models\SubDepartment::find(
                                                                                $old['subdepartment'],
                                                                            )->sub_department_name ?? null
                                                                        : null;
                                                                    $newSubDepartmentName = isset(
                                                                        $attributes['subdepartment'],
                                                                    )
                                                                        ? \App\Models\SubDepartment::find(
                                                                                $attributes['subdepartment'],
                                                                            )->sub_department_name ?? null
                                                                        : null;

                                                                    $oldReportToUser = isset($old['report_to'])
                                                                        ? \App\Models\User::find($old['report_to'])
                                                                        : null;
                                                                    $newReportToUser = isset($attributes['report_to'])
                                                                        ? \App\Models\User::find(
                                                                            $attributes['report_to'],
                                                                        )
                                                                        : null;

                                                                    $oldReportToName = $oldReportToUser
                                                                        ? $oldReportToUser->first_name .
                                                                            ' ' .
                                                                            $oldReportToUser->last_name
                                                                        : null;
                                                                    $newReportToName = $newReportToUser
                                                                        ? $newReportToUser->first_name .
                                                                            ' ' .
                                                                            $newReportToUser->last_name
                                                                        : null;

                                                                    $oldStatusLabel = isset($old['status'])
                                                                        ? match ((int) $old['status']) {
                                                                            0 => 'Not Accepted',
                                                                            1 => 'Accepted',
                                                                            2 => 'Rejected',
                                                                            default => 'Unknown',
                                                                        }
                                                                        : null;

                                                                    $newStatusLabel = isset($attributes['status'])
                                                                        ? match ((int) $attributes['status']) {
                                                                            0 => 'Not Accepted',
                                                                            1 => 'Accepted',
                                                                            2 => 'Rejected',
                                                                            default => 'Unknown',
                                                                        }
                                                                        : null;

                                                                    $createdAtFormatted = isset(
                                                                        $activityLog->created_at,
                                                                    )
                                                                        ? \Carbon\Carbon::parse(
                                                                            $activityLog->created_at,
                                                                        )->format('d F Y, h:i A')
                                                                        : null;

                                                                    $oldCompletedByName =
                                                                        isset($old['completed_by']) &&
                                                                        \App\Models\User::find($old['completed_by'])
                                                                            ? \App\Models\User::find(
                                                                                    $old['completed_by'],
                                                                                )->first_name .
                                                                                ' ' .
                                                                                \App\Models\User::find(
                                                                                    $old['completed_by'],
                                                                                )->last_name
                                                                            : null;

                                                                    $newCompletedByName =
                                                                        isset($attributes['completed_by']) &&
                                                                        \App\Models\User::find(
                                                                            $attributes['completed_by'],
                                                                        )
                                                                            ? \App\Models\User::find(
                                                                                    $attributes['completed_by'],
                                                                                )->first_name .
                                                                                ' ' .
                                                                                \App\Models\User::find(
                                                                                    $attributes['completed_by'],
                                                                                )->last_name
                                                                            : null;

                                                                    $oldAcceptedByName =
                                                                        isset($old['accepted_by']) &&
                                                                        \App\Models\User::find($old['accepted_by'])
                                                                            ? \App\Models\User::find(
                                                                                    $old['accepted_by'],
                                                                                )->first_name .
                                                                                ' ' .
                                                                                \App\Models\User::find(
                                                                                    $old['accepted_by'],
                                                                                )->last_name
                                                                            : null;

                                                                    $newAcceptedByName =
                                                                        isset($attributes['accepted_by']) &&
                                                                        \App\Models\User::find(
                                                                            $attributes['accepted_by'],
                                                                        )
                                                                            ? \App\Models\User::find(
                                                                                    $attributes['accepted_by'],
                                                                                )->first_name .
                                                                                ' ' .
                                                                                \App\Models\User::find(
                                                                                    $attributes['accepted_by'],
                                                                                )->last_name
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
                                                                                    @elseif($key === 'subdepartment')
                                                                                        {{ $oldSubDepartmentName }}
                                                                                    @elseif($key === 'report_to')
                                                                                        {{ $oldReportToName }}
                                                                                    @elseif($key === 'status')
                                                                                        {{ $oldStatusLabel }}
                                                                                    @elseif($key === 'created_at')
                                                                                        {{ $createdAtFormatted }}
                                                                                    @elseif($key === 'completed_by')
                                                                                        {{ $oldCompletedByName }}
                                                                                    @elseif($key === 'accepted_by')
                                                                                        {{ $oldAcceptedByName }}
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
                                                                                    @elseif($key === 'subdepartment')
                                                                                        {{ $newSubDepartmentName }}
                                                                                    @elseif($key === 'report_to')
                                                                                        {{ $newReportToName }}
                                                                                    @elseif($key === 'status')
                                                                                        {{ $newStatusLabel }}
                                                                                    @elseif($key === 'created_at')
                                                                                        {{ $createdAtFormatted }}
                                                                                    @elseif($key === 'completed_by')
                                                                                        {{ $newCompletedByName }}
                                                                                    @elseif($key === 'accepted_by')
                                                                                        {{ $newAcceptedByName }}
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
                            <div class="pagination-wrapper mt-2 d-flex justify-content-center">
                                {{ $activityLogs->links('pagination::bootstrap-4') }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            let debounceTimer;

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const searchTerm = this.value.toLowerCase();

                debounceTimer = setTimeout(() => {
                    // Show loading indicator
                    document.querySelector('.timeline').innerHTML =
                        '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                    // Fetch filtered results via AJAX
                    fetch(`{{ route('activity-index') }}?term=${encodeURIComponent(searchTerm)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            document.querySelector('.timeline').innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error searching activities:', error);
                            document.querySelector('.timeline').innerHTML =
                                '<p class="text-danger">Error loading search results</p>';
                        });
                }, 400); // Wait 400ms after user stops typing
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const isRefresh = performance.navigation.type === performance.navigation.TYPE_RELOAD;
            const url = new URL(window.location.href);

            if (isRefresh && url.searchParams.has('page')) {
                url.searchParams.delete('page');
                window.location.replace(url.toString());
            }
        });
    </script>



@endsection
