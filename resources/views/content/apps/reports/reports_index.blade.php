@extends('layouts/contentLayoutMaster')

@section('title', 'Reports')

@section('vendor-style')
    {{-- Page CSS files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f3f3f3;
            font-weight: bold;
        }

        .inactive-row {
            color: red;
            font-weight: bold;
            /* Make the text bold */
        }


        /* Style for Total row */
        .total-row {
            font-weight: bold;
            /* Make the text bold */
            background-color: #f2f2f2;
            /* Light grey background for the Total row */
            color: #000;
            /* Black text color */
        }

        /* Optional: Style for Total row in footer */
        tfoot th {
            font-weight: bold;
            background-color: #f2f2f2;
            /* Light grey background for footer */
            color: #000;
            /* Black text color */
        }

        .highlight-yellow {
            background-color: yellow;
        }

        .text-red-bold {
            color: red;
            font-weight: bold;
        }

        .SrNo {
            font-weight: bold;
        }

        .pending-task {
            background-color: #f9ef9b !important;
            font-weight: 900;

            /* Example color */
        }

        .close-task-total {
            background-color: #75de8c !important;
            color: #ffff;
            font-weight: 900;
        }

        .total {
            background-color: #56627A !important;
            color: #ffff;
            font-weight: 900;

        }

        .heading_color {
            background-color: #33405E !important;
            color: #ffff;
        }
    </style>
@endsection

@section('content')
    <div class="card table-responsive">
        <div class="card-header">
            <h4>Task Report</h4>
        </div>
        @if (auth()->user()->hasRole('Super Admin'))
            <div class="card-body ">
                <table id="g7-table" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>G7 Name</th>
                            <th>Users Status</th>
                            <th>Total Task</th>
                            <th>Total Completed Task</th>
                            <th>Completion %</th>
                            <th>Total Pending Tasks till Yesterday (Opening for today)</th>
                            <th>Tasks Added On the Reporting Date</th>
                            <th>Tasks Completed on the Reporting Date</th>
                            <th>Total Pending Tasks till Yesterday (Closing for today)</th>
                            <th>Overdue Task as on Date</th>
                            <th>% Overdue Task</th>
                            <th>Conceptualization</th>
                            <th>Scope Defined</th>
                            <th>In Execution</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
    </div>
    @endif

    <div class="card table-responsive">
        <div class="card-header">
            <div class="row text-center mt-2">
                <div class="col-md-4">
                    <p><strong>Report Date:</strong> {{ \Carbon\Carbon::today()->format('F j, Y') }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Report By:</strong>
                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Department Covered:</strong>
                        {{ auth()->user()->department->department_name ?? 'No Department' }}</p>
                </div>
            </div>
        </div>
        <div class="ms-2">
            <h3 class="text-danger"><span>*</span> <strong>This report includes only accepted tasks.</strong></h3>
        </div>
        <div class="card-body">
            <table id="users-hierarchy_new" class="table table-bordered">
                <thead>
                    <tr class="text-center ">
                        <th class="heading_color">Sr. No</th>
                        <th class="heading_color">Particulars</th>
                        @foreach ($table_data as $users)
                            <th class="heading_color">
                                {{-- @if ($loop->first) --}}
                                {{-- Self --}}
                                {{-- @else --}}
                                <?= $users['user_name']  ?>
                                {{-- @endif --}}
                            </th>
                        @endforeach
                        <th class="heading_color">Total</th> <!-- Add the Total column header -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Initialize totals for each row
                        $totals = [
                            'totalTasksTillYesterday' => 0,
                            'totalPendingTasksTillYesterday' => 0,
                            'totalTasksAddedToday' => 0,
                            'totalTasksCompletedToday' => 0,
                            'totalTasksReportDate' => 0,
                            'totalPendingTask' => 0,
                            'totalOverdueTasksTillReportDate' => 0,
                        ];
                    @endphp

                    <!-- Total Tasks Till Yesterday Row -->
                    <tr class="text-center">
                        <td class="SrNo">1</td>
                        <td>Total Tasks Till Yesterday (Opening For Today)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksTillYesterday'] += $users['total_tasks_till_yesterday'];
                            @endphp
                            <td>{{ $users['total_tasks_till_yesterday'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksTillYesterday'] }}</td> <!-- Display the total -->
                    </tr>

                    <!-- Total Pending Tasks Till Yesterday Row -->
                    <tr class="text-center">
                        <td class="SrNo">2</td>
                        <td>Total Pending Tasks Till Yesterday (Opening For Today)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalPendingTasksTillYesterday'] +=
                                    $users['total_pending_tasks_till_yesterday'];
                            @endphp
                            <td>{{ $users['total_pending_tasks_till_yesterday'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalPendingTasksTillYesterday'] }}</td>
                    </tr>

                    <!-- Tasks Added on Report Date Row -->
                    <tr class="text-center">
                        <td class="SrNo">3</td>
                        <td>Tasks Added on Report Date</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksAddedToday'] += $users['tasks_added_today'];
                            @endphp
                            <td>{{ $users['tasks_added_today'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksAddedToday'] }}</td>
                    </tr>

                    <!-- Task Completed on Report Date Row -->
                    <tr class="text-center">
                        <td class="SrNo">4</td>
                        <td>Task Completed on Report Date</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksCompletedToday'] += $users['tasks_completed_today'];
                            @endphp
                            <td>{{ $users['tasks_completed_today'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksCompletedToday'] }}</td>
                    </tr>

                    <!-- Total Tasks Till Report Date Row -->
                    <tr class="highlight-yellow text-center">
                        <td class="SrNo">5</td>
                        <td>Total Tasks Till Report Date (Closing For Today)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksReportDate'] += $users['task_report_date'];
                            @endphp
                            <td>{{ $users['task_report_date'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksReportDate'] }}</td>
                    </tr>

                    <!-- Total Pending Tasks Row -->
                    <tr class="highlight-yellow text-center">
                        <td class="SrNo">6</td>
                        <td>Total Pending Tasks (Closing For Today)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalPendingTask'] += $users['total_pending_task'];
                            @endphp
                            <td>{{ $users['total_pending_task'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalPendingTask'] }}</td>
                    </tr>

                    <!-- Total Overdue Tasks Till Report Date Row -->
                    <tr class="text-center">
                        <td class="SrNo">7</td>
                        <td class="text-red-bold">Total Overdue Tasks Till Report Date</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalOverdueTasksTillReportDate'] +=
                                    $users['total_overdue_tasks_till_report_date'];
                            @endphp
                            <td>{{ $users['total_overdue_tasks_till_report_date'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalOverdueTasksTillReportDate'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card table-responsive">
        {{-- <div class="card-header">
            <div class="row text-center mt-2">
                <div class="col-md-4">
                    <p><strong>Report Date:</strong> {{ \Carbon\Carbon::today()->format('F j, Y') }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Report By:</strong>
                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Department Covered:</strong>
                        {{ auth()->user()->department->department_name ?? 'No Department' }}</p>
                </div>
            </div>
        </div> --}}

        <div class="card-body">
            <table id="users-status-bifurcation" class="table table-bordered ">
                <thead>
                    <tr class="text-center">
                        <th class="heading_color">Sr. No</th>
                        <th class="heading_color">Particulars</th>
                        @foreach ($table_data as $users)
                            <th class="heading_color">

                                <?= $users['user_name']  ?>
                            </th>
                        @endforeach
                        <th class="heading_color">Total</th> <!-- Add the Total column header -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Initialize totals for each row
                        $totals = [
                            'totalTasksConceptualization' => 0,
                            'totalTasksScopeDefined' => 0,
                            'totalTasksInExecution' => 0,
                            'totalStatusCount' => 0,
                        ];
                    @endphp

                    <!-- Conceptualization -->
                    <tr class="text-center">
                        <td class="SrNo">1</td>
                        <td>Conceptualization</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksConceptualization'] += $users['totalTasksConceptualization'];
                            @endphp
                            <td>{{ $users['totalTasksConceptualization'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksConceptualization'] }}</td> <!-- Display the total -->
                    </tr>

                    <!-- Scope Defined -->
                    <tr class="text-center">
                        <td class="SrNo">2</td>
                        <td>Scope Defined</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksScopeDefined'] += $users['totalTasksScopeDefined'];
                            @endphp
                            <td>{{ $users['totalTasksScopeDefined'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksScopeDefined'] }}</td>
                    </tr>

                    <!-- In Execution -->
                    <tr class="text-center">
                        <td class="SrNo">3</td>
                        <td>In Execution</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksInExecution'] += $users['totalTasksInExecution'];
                            @endphp
                            <td>{{ $users['totalTasksInExecution'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksInExecution'] }}</td>
                    </tr>

                    <tr class="text-center text-red-bold">
                        <td class="SrNo">4</td>
                        <td>Total Pending Task</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalStatusCount'] += $users['totalStatusCount'];
                            @endphp
                            <td>{{ $users['totalStatusCount'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalStatusCount'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
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

    <script>
        $(document).ready(function() {
            var table = $('#g7-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('reports.g7-data') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        "data": 'users_status',
                        "name": 'users_status',
                        "render": function(data, type, row, meta) {
                            // Check if the users_status is 1 (active)
                            if (data == 1) {
                                return '<span class="badge bg-success">Active</span>';
                            }
                            // Check if the data is an empty value
                            else if (data === '') {
                                return ''; // Return an empty string if no value is present
                            }
                            // If the data is not 1 (active), show inactive
                            else {
                                return '<span class="badge bg-danger">Inactive</span>';
                            }
                        }
                    },
                    {
                        data: 'total_task',
                        name: 'total_task'
                    },
                    {
                        data: 'total_completed_task',
                        name: 'total_completed_task'
                    },
                    {
                        data: 'completion_percent',
                        name: 'completion_percent'
                    },
                    {
                        data: 'total_pending_yesterday',
                        name: 'total_pending_yesterday'
                    },
                    {
                        data: 'tasks_added_today',
                        name: 'tasks_added_today'
                    },
                    {
                        data: 'tasks_completed_today',
                        name: 'tasks_completed_today'
                    },
                    {
                        data: 'total_pending_closing',
                        name: 'total_pending_closing'
                    },
                    {
                        data: 'overdue_task',
                        name: 'overdue_task'
                    },
                    {
                        data: 'percent_overdue',
                        name: 'percent_overdue'
                    },
                    {
                        data: 'conceptualization',
                        name: 'conceptualization'
                    },
                    {
                        data: 'scope_defined',
                        name: 'scope_defined'
                    },
                    {
                        data: 'in_execution',
                        name: 'in_execution'
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data.name === 'Total') {
                        $(row).addClass('total-row');
                    }
                    if (data.name === 'Inactive') {
                        $(row).addClass('inactive-row');
                    }
                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();
                    var totalTask = api.column(1).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalCompleted = api.column(2).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalAddedToday = api.column(5).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalOverdue = api.column(8).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalConceptualization = api.column(10).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalScopeDefined = api.column(11).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);
                    var totalInExecution = api.column(12).data().reduce(function(a, b) {
                        return a + b;
                    }, 0);

                    // Update footer with totals
                    $(api.column(1).footer()).html(totalTask);
                    $(api.column(2).footer()).html(totalCompleted);
                    $(api.column(5).footer()).html(totalAddedToday);
                    $(api.column(8).footer()).html(totalOverdue);
                    $(api.column(10).footer()).html(totalConceptualization);
                    $(api.column(11).footer()).html(totalScopeDefined);
                    $(api.column(12).footer()).html(totalInExecution);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Feather icons
            feather.replace();

            // Initialize DataTable with buttons
            var usersTable = $('#users-hierarchy_new').DataTable({
                paging: false, // Disable pagination
                info: false, // Disable information summary
                dom: 'Bfrtip', // Add this line to include Buttons
                buttons: [{
                    extend: 'excel', // Add Excel export button
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel', // Button text with icon
                    filename: 'Task_Analysis_List', // Filename for the export
                    className: 'btn btn-success btn-sm', // Button classes
                    messageTop: function() {
                        // Dynamically generate the message to avoid encoding issues
                        var reportDate = '{{ \Carbon\Carbon::today()->format('F j, Y') }}';
                        var reportBy =
                            '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}';
                        var department =
                            '{{ auth()->user()->department->department_name ?? 'No Department' }}';

                        return 'Report Date: ' + reportDate + '\n' +
                            'Report By: ' + reportBy + '\n' +
                            'Department Covered: ' + department;
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // You can manipulate the sheet here if needed
                    }
                }],
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Feather icons
            feather.replace();

            // Initialize DataTable with buttons
            var usersTable = $('#users-status-bifurcation').DataTable({
                paging: false, // Disable pagination
                info: false, // Disable information summary
                dom: 'Bfrtip', // Add this line to include Buttons
                buttons: [{
                    extend: 'excel', // Add Excel export button
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel', // Button text with icon
                    filename: 'Task_Analysis_List', // Filename for the export
                    className: 'btn btn-success btn-sm', // Button classes
                    messageTop: function() {
                        // Dynamically generate the message to avoid encoding issues
                        var reportDate = '{{ \Carbon\Carbon::today()->format('F j, Y') }}';
                        var reportBy =
                            '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}';
                        var department =
                            '{{ auth()->user()->department->department_name ?? 'No Department' }}';

                        return 'Report Date: ' + reportDate + '\n' +
                            'Report By: ' + reportBy + '\n' +
                            'Department Covered: ' + department;
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // You can manipulate the sheet here if needed
                    }
                }],
            });
        });
    </script>
@endsection
