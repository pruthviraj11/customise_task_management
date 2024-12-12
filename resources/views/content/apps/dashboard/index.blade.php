@extends('layouts/contentLayoutMaster')

@section('title', 'dashboard')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection
@section('content')

    <section class="app-user-list">
        <div class="row">


            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif
            @if (auth()->user()->id != 1)
                <div class="row">
                    <div class="col">
                        <a href="{{ route('app-task-get-total_task') }}" data-bs-toggle="tooltip" title="View Total Tasks">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="total_task">0</h3>
                                        <span>Total Tasks (My Scop of Work)</span>
                                    </div>
                                    <div class="avatar bg-light-primary p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>
                    {{-- @if ($teamTasks != 0) --}}
                    <div class="col">
                        <a href="{{ route('app-task-get-team_task') }}" data-bs-toggle="tooltip" title="View Total Tasks">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="total-team_task">Loading...</h3>
                                        <span>Team Tasks</span>
                                    </div>
                                    <div class="avatar bg-light-primary p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>
                    {{-- @endif --}}
                    @if (auth()->user()->id != 1)
                        <div class="col">
                            <a href="{{ route('app-task-get-my_and_team') }}" data-bs-toggle="tooltip"
                                title="View Total Tasks">
                                <div class="card">
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div>
                                            <h3 class="fw-bolder mb-75" id="total-tasks-count">Loading...</h3>
                                            <span>Total & Team Tasks</span>
                                        </div>
                                        <div class="avatar bg-light-primary p-50">
                                            <span class="avatar-content">
                                                <i data-feather="user" class="font-medium-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
            @endif
            {{-- @if (auth()->user()->id != 1)
                <div class="row">
                    <div class="col-lg-4 col-sm-12">
                        <a href="{{ route('app-task-get-total_deleted') }}">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div>
                                        <h3 class="fw-bolder mb-75 ">{{ $deleted_task }}</h3>
                                        <span>Total Tasks</span>
                                    </div>
                                    <div class="avatar bg-light-primary p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif --}}
            @if (auth()->user()->id == 1)
                <div class="col-lg-12 col-sm-12">
                    <a href="{{ route('app-task-get-total_task') }}" data-bs-toggle="tooltip" title="View Total Tasks">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div>
                                    <h3 class="fw-bolder mb-75 ">{{ $total['total_task'] }}</h3>
                                    <span>Total Tasks</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- <div class="col-lg-4 col-sm-12">
                    <a href="{{ route('app-task-get-admin_acc') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75 ">{{ $total['acc_task'] }}</h3>
                                    <span>Total Accepted Task</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <a href="{{ route('app-task-get-admin_req') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75 ">{{ $total['req_task'] }}</h3>
                                    <span>Total Requsted Task</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <a href="{{ route('app-task-get-admin_rej') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75 ">{{ $total['rej_task'] }}</h3>
                                    <span>Total Rejected Task</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div> --}}
            @endif
            @if (auth()->user()->id != 1)
                {{-- <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-mytask') }}" data-bs-toggle="tooltip"
                        title="This task is created by you for yourself">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $my_task }}</h3>
                                    <span>My Task</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="clipboard" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-accepted_by_me') }} " data-bs-toggle="tooltip"
                        title="This task is accepted by you">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $taccepted_by_me }}</h3>
                                    <span>Accepted By Me</span>
                                </div>
                                <div class="avatar bg-light-danger p-50">
                                    <span class="avatar-content">
                                        <i data-feather="check-square" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6" data-bs-toggle="tooltip" title="This task is assigned by you">
                    <a href="{{ route('app-task-get-assign_by_me') }}">

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $assign_by_me }}</h3>
                                    <span>Task Assign By Me</span>
                                </div>
                                <div class="avatar bg-light-success p-50">
                                    <span class="avatar-content">
                                        <i data-feather="phone-outgoing" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6" data-bs-toggle="tooltip"
                    title="This task is requested to you . You need to accept this task.">

                    <a href="{{ route('app-task-requested') }}">

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $requested_me }}</h3>
                                    <span>Task Requested Me</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="phone-incoming" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div> --}}


                <div class="row">
                    <!-- My Task -->
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ route('app-task-get-mytask') }}" data-bs-toggle="tooltip"
                            title="This task is created by you for yourself">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="my_task">0</h3>
                                        <span>My Task (My Own Task)</span>
                                    </div>
                                    <div class="avatar bg-light-primary p-50">
                                        <span class="avatar-content">
                                            <i data-feather="clipboard" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Accepted By Me -->
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ route('app-task-get-accepted_by_me') }}" data-bs-toggle="tooltip"
                            title="This task is accepted by you">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="taccepted_by_me">0</h3>
                                        <span>Accepted By Me</span>
                                    </div>
                                    <div class="avatar bg-light-danger p-50">
                                        <span class="avatar-content">
                                            <i data-feather="check-square" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Task Assigned By Me -->
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ route('app-task-get-assign_by_me') }}" data-bs-toggle="tooltip"
                            title="This task is assigned by you">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="assign_by_me">0</h3>
                                        <span>Task Assigned By Me</span>
                                    </div>
                                    <div class="avatar bg-light-success p-50">
                                        <span class="avatar-content">
                                            <i data-feather="phone-outgoing" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Task Requested To Me -->
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ route('app-task-requested') }}" data-bs-toggle="tooltip"
                            title="This task is requested to you. You need to accept this task.">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75" id="requested_me">0</h3>
                                        <span>Task Requested To Me</span>
                                    </div>
                                    <div class="avatar bg-light-warning p-50">
                                        <span class="avatar-content">
                                            <i data-feather="phone-incoming" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md">
                    <a href="{{ route('app-task-get-conceptualization') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="conceptualization_count">0</h3>
                                    <span>Conceptualization Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- <div class="col-md">
        <a href="{{ route('app-task-get-due_date_past') }}">
            <div class="card" style="min-height: 120px">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bolder mb-75" id="due_date_past_count">0</h3>
                        <span class="text-danger">Due Date Past Task</span>
                    </div>
                    <div class="avatar bg-light-warning p-50">
                        <span class="avatar-content">
                            <i data-feather="user-x" class="font-medium-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div> --}}

                <div class="col-md">
                    <a href="{{ route('app-task-get-scope_defined') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="scope_defined_count">0</h3>
                                    <span>Scope Defined Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md">
                    <a href="{{ route('app-task-get-in_execution') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="in_execution_count">0</h3>
                                    <span>In Execution Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md">
                    <a href="{{ route('app-task-get-completed') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="completed_count">0</h3>
                                    <span>Completed Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md">
                    <a href="{{ route('app-task-get-close') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="close_count">0</h3>
                                    <span>Close Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md">
                    <a href="{{ route('app-task-get-hold') }}">
                        <div class="card" style="min-height: 120px">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="hold_count">0</h3>
                                    <span>Hold Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <hr size="10" color="red">

            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-due_date_past') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="due_date_past_count_2">0</h3>
                                    <span class="text-danger">Due Date Past Task</span>
                                </div>
                                <div class="avatar bg-light-warning p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-x" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-deleted') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75" id="deleted_count">0</h3>
                                    <span class="text-danger">Total Deleted Task</span>
                                </div>
                                <div class="avatar bg-light-primary p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>


            {{-- @if (auth()->user()->id == 1) --}}

            {{-- @endif --}}
        </div>
        @if (auth()->user()->id != 1)
            <div class="container">
                <div class="card">
                    <div class="card-header d-grid">
                        <h1>Employee wise Status of Task
                        </h1>
                        <table id="users-hierarchy" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    {{-- <th>Hod</th>
                                    <th>Department</th> --}}
                                    @foreach ($statuses as $status)
                                        <th>{{ $status->status_name }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>


        @endif
        @if (auth()->user()->id == 1)
            <div class="row">
                <div class="container">
                    <div class="card">
                        <div class="row ">
                            <div class="card-header p-5">
                                {{-- <h1>Tasks by Department and Status</h1> --}} <h1>Department Wise Status of Task
                                </h1>
                                <table id="tasks-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Department / Status</th>

                                            <th>HOD</th>
                                            @foreach ($statuses as $status)
                                                <th>{{ $status->status_name }}</th>
                                            @endforeach
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="card">
                        <div class="card-header d-grid">


                            <div class="row">
                                <div class="col-md-6">
                                    <h1>Employee wise Status of Task
                                    </h1>
                                </div>
                                <div class="col-md-6">
                                    {{-- <form action="#" method="GET">
                                        <label for="user_id">Select User</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">-- Select a User --</option>
                                            @foreach ($usersWithG7 as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->first_name . ' ' . $user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form> --}}
                                </div>
                            </div>

                            <table id="users-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Hod</th>
                                        <th>Report To</th>
                                        <th>User Name</th>
                                        @foreach ($statuses as $status)
                                            <th>{{ $status->status_name }}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Total task in Department</h4>
                            {{-- <h1>Polar Area Chart</h1> --}}
                            <canvas style="max-width: 450px; max-height:450px;" id="polarAreaChart" width="120"
                                height="120"></canvas>
                        </div>
                        <div class="card-body">

                        </div>
                    </div>
                </div>




            </div>
    </section>
    @endif
    {{-- {{ dd($statuses) }} --}}
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

    <script>
        $(document).ready(function() {
            fetchTaskCounts();

            function fetchTaskCounts() {
                $.ajax({
                    url: "{{ route('task.counts') }}",
                    method: "GET",
                    success: function(response) {
                        $('#conceptualization_count').text(response.task_count.conceptualization);
                        $('#due_date_past_count_2').text(response.task_count.due_date_past);
                        $('#scope_defined_count').text(response.task_count.scope_defined);
                        $('#completed_count').text(response.task_count.completed);
                        $('#in_execution_count').text(response.task_count.in_execution);
                        $('#hold_count').text(response.task_count.hold);
                        $('#close_count').text(response.task_count.close);

                        if (response.task_count.deleted !== undefined) {
                            $('#deleted_count').text(response.task_count.deleted);
                        }
                    }
                });
            }
        });
    </script>
    <!-- Page js files -->
    {{-- <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
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
    </script> --}}
    {{-- 11-6 --}}
    {{-- <script>
        $(document).ready(function() {
            var usersTable = $('#users-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route('users.status.data') }}', // Ensure this route returns user status data
                paging: false, // Disable pagination
                info: false, // Disable the information summary
                dom: 'Bfrtip', // Add this line to include Buttons
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    title: '',
                    filename: 'User Status',
                    className: 'btn btn-success btn-sm'
                }],
                columns: [{
                        data: 'user_name',
                        name: 'user_name'
                    },
                    @foreach ($statuses as $status)
                        {
                            data: 'status_id{{ $status->id }}', // Ensure the key matches the JSON response
                            name: 'status_id{{ $status->id }}'
                        },
                    @endforeach {
                        data: 'total',
                        name: 'total'
                    } // Add Total column
                ]
            });
        });
    </script> --}}
    {{-- 11-6 --}}

    <script>
        $(document).ready(function() {
            var usersTable = $('#users-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route('users.task.status.data') }}', // Ensure this route returns user task status data
                paging: false, // Disable pagination
                info: false, // Disable the information summary
                dom: 'Bfrtip', // Add this line to include Buttons
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    title: '',
                    filename: 'User Task Status',
                    className: 'btn btn-success btn-sm'
                }],
                columns: [{
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'hod',
                        name: 'hod'
                    }, {
                        data: 'report_to',
                        name: 'report_to'
                    }, {
                        data: 'user_name',
                        name: 'user_name'
                    },

                    @foreach ($statuses as $status)
                        {
                            data: '{{ \Str::slug($status->status_name, '_') }}',
                            name: '{{ \Str::slug($status->status_name, '_') }}'
                        },
                    @endforeach {
                        data: 'total',
                        name: 'total'
                    } // Add Total column
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var usersTable = $('#users-hierarchy').DataTable({
                processing: true,
                serverSide: false,
                ajax: '{{ route('users.task.status.hierarchy') }}', // Ensure this route returns user task status data
                paging: false, // Disable pagination
                info: false, // Disable the information summary
                dom: 'Bfrtip', // Add this line to include Buttons
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    title: '',
                    filename: 'User Task Status',
                    className: 'btn btn-success btn-sm'
                }],
                columns: [{
                        data: 'user_name',
                        name: 'user_name'
                    },

                    @foreach ($statuses as $status)
                        {
                            data: '{{ \Str::slug($status->status_name, '_') }}',
                            name: '{{ \Str::slug($status->status_name, '_') }}'
                        },
                    @endforeach {
                        data: 'total',
                        name: 'total'
                    } // Add Total column
                ]
            });
        });
    </script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('app-task-get-counts') }}', // Adjust route name as needed
                method: 'GET',
                success: function(data) {
                    $('#my_task').text(data.my_task);
                    $('#taccepted_by_me').text(data.taccepted_by_me);
                    $('#assign_by_me').text(data.assign_by_me);
                    $('#requested_me').text(data.requested_me);
                    $('#total_task').text(data.total_task);
                }
            });
        });
        $(document).ready(function() {
            $(document).ready(function() {
                $.ajax({
                    url: "{{ route('tasks.team_task') }}",
                    type: "GET",
                    success: function(response) {
                        // Update the task count in the HTML
                        $('#total-team_task').text(response.teamTasks_count);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching task count:", error);
                        $('#total-team_task').text('Error');
                    }
                });
            });
            $.ajax({
                url: "{{ route('tasks.totalCount') }}",
                type: "GET",
                success: function(response) {
                    // Update the task count in the HTML
                    $('#total-tasks-count').text(response.total_task_count);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching task count:", error);
                    $('#total-tasks-count').text('Error');
                }
            });
        });
    </script>

    @if (auth()->user()->id == 1)
        {{-- <script>
            var ctx = document.getElementById('polarAreaChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: {!! $data->pluck('department_name') !!},
                    datasets: [{
                        label: 'Task Count',
                        data: {!! $data->pluck('task_count') !!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)',
                            'rgba(255, 159, 64, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script> --}}
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                var table = $('#tasks-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('tasks.data') }}',
                    paging: false, // Disable pagination
                    info: false, // Disable the information summary
                    dom: 'lBfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                        title: '',
                        filename: 'Project Status',
                        className: 'btn btn-primary  btn-sm',

                    }, ],
                    columns: [{
                            data: 'department_name',
                            name: 'department_name'
                        }, {
                            data: 'hod',
                            name: 'hod'
                        },
                        @foreach ($statuses as $status)

                            {
                                data: 'status_{{ $status->id }}',
                                name: 'status_{{ $status->id }}'
                            },
                        @endforeach {
                            data: 'total',
                            name: 'total'
                        }
                    ]
                });
            });
        </script>
    @endif


@endsection
