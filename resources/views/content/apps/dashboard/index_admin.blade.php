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
{{--
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> --}}
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
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div>
                                <h3 class="fw-bolder mb-75">{{ $total['total_task'] }}</h3>
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
            @if ($teamTasks != 0)
            <div class="col">
                <a href="{{ route('app-task-get-team_task') }}" data-bs-toggle="tooltip" title="View Total Tasks">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div>
                                <h3 class="fw-bolder mb-75">{{ $teamTasks }}</h3>
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
            @endif
            @if (auth()->user()->id != 1)
            <div class="col">
                <a href="{{ route('app-task-get-my_and_team') }}" data-bs-toggle="tooltip" title="View Total Tasks">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div>
                                {{-- <h3 class="fw-bolder mb-75">{{ $total['total_task'] + $teamTasks }}</h3> --}}
                                <h3 class="fw-bolder mb-75">{{ $MeAndTeam }}</h3>


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
        </div>
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
        <div class="col-lg-3 col-sm-6">
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
                {{-- <a href="{{ route('app-task-accepted') }}"> --}}
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
            {{-- <a href="{{ route('app-task-get-requested_me') }}"> --}}
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
        </div>
        @endif
        <div class="col-md ">
            <a href="{{ route('app-task-get-conceptualization') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['conceptualization'] }}</h3>
                            <span> Conceptualization Task</span>
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

        <div class="col-md ">
            <a href="{{ route('app-task-get-scope_defined') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['scope_defined'] }}</h3>
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
        <div class="col-md ">
            <a href="{{ route('app-task-get-in_execution') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['in_execution'] }}</h3>
                            <span> In Execution Task</span>
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
        <div class="col-md ">
            <a href="{{ route('app-task-get-completed') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['completed'] }}</h3>
                            <span> completed Task</span>
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
        <div class="col-md ">
            <a href="{{ route('app-task-get-close') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['close'] }}</h3>
                            <span> Close Task</span>
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

        <div class="col-md ">
            <a href="{{ route('app-task-get-hold') }}">

                <div class="card" style="min-height: 120px">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['hold'] }}</h3>
                            <span> Hold Task</span>
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
        <div class="col-12">
            <hr size="10" color="red">
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="{{ route('app-task-get-due_date_past') }}">

                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="fw-bolder mb-75">{{ $task_count['due_date_past'] }}</h3>
                            <span class="text-danger"> Due Date Past Task</span>
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

        {{-- @if (auth()->user()->id == 1) --}}
        <div class="col-lg-3 col-sm-6">
            <a href="{{ route('app-task-get-deleted') }}">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div>
                            <h3 class="fw-bolder mb-75 ">{{ $total['deleted'] }}</h3>
                            <span class="text-danger"> Total Deleted Task</span>
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
                    <h1>Employee wise Status of Task
                    </h1>
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

        {{-- <div class="col-lg-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Total task in Department</h4>

                    <canvas style="max-width: 450px; max-height:450px;" id="polarAreaChart" width="120"
                        height="120"></canvas>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div> --}}




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
{{-- 11-6 --}}
{{--
<script>
    $(document).ready(function () {
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
            @foreach($statuses as $status)
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
{{-- working --}}
{{--
<script>
    $(document).ready(function () {
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

            @foreach($statuses as $status)
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
    $(document).ready(function () {
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

            @foreach($statuses as $status)
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
</script> --}}
{{-- working --}}
{{--
<script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script>
@if (auth()->user()->id == 1)
<script>
    var ctx = document.getElementById('polarAreaChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: {!! $data-> pluck('department_name')!!
    },
        datasets: [{
            label: 'Task Count',
            data: {!! $data-> pluck('task_count')!!},
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
</script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
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

            },],
            columns: [{
                data: 'department_name',
                name: 'department_name'
            }, {
                data: 'hod',
                name: 'hod'
            },
            @foreach($statuses as $status)

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
@endif --}}
@endsection
