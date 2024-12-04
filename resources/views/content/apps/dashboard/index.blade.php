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
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection
@section('content')

    <section class="app-user-list">
        <div class="row">
            @if (auth()->user()->id == 1)
                <div class="col-lg-12 col-sm-12">
                    <a href="{{ route('app-task-get-total_task') }}">
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
                <div class="col-lg-4 col-sm-12">
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
                </div>
            @endif
            @if (auth()->user()->id != 1)
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-mytask') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $my_task }}</h3>
                                    <span>My Task</span>
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
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-accepted_by_me') }}">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $taccepted_by_me }}</h3>
                                    <span>Accepted By Me</span>
                                </div>
                                <div class="avatar bg-light-danger p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-plus" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-assign_by_me') }}">

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $assign_by_me }}</h3>
                                    <span>Task Assign By Me</span>
                                </div>
                                <div class="avatar bg-light-success p-50">
                                    <span class="avatar-content">
                                        <i data-feather="user-check" class="font-medium-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('app-task-get-requested_me') }}">

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder mb-75">{{ $requested_me }}</h3>
                                    <span>Task Requested Me</span>
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
            @endif
            <div class="col-lg-3 col-sm-6">
                <a href="{{ route('app-task-get-conceptualization') }}">

                    <div class="card">
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

            <div class="col-lg-3 col-sm-6">
                <a href="{{ route('app-task-get-scope_defined') }}">

                    <div class="card">
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
            <div class="col-lg-3 col-sm-6">
                <a href="{{ route('app-task-get-completed') }}">

                    <div class="card">
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
            <div class="col-lg-3 col-sm-6">
                <a href="{{ route('app-task-get-in_execution') }}">

                    <div class="card">
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

            <div class="col-lg-3 col-sm-6">
                <a href="{{ route('app-task-get-hold') }}">

                    <div class="card">
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
            <div class="col-12"></div>
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



        </div>

    </section>
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
