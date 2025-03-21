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
    </style>
@endsection

@section('content')




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

        <div class="card-body">
            <table id="users-hierarchy_new" class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No</th>
                        <th>Particulars</th>
                        @foreach ($table_data as $users)
                            <th>
                                @if ($loop->first)
                                {{ $users['user_name'] }}
                                @else
                                    {{ $users['user_name'] }}
                                @endif
                            </th>
                        @endforeach
                        <th>Total</th> <!-- Add the Total column header -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Initialize totals for each row
                        $totals = [
                            'totalTasksLastWeek' => 0,
                            'totalPendingTasksLastWeek' => 0,
                            'totalTasksAddedLastWeek' => 0,
                            'totalTasksCompletedLastWeek' => 0,
                            'totalTasksReportLastWeek' => 0,
                            'totalPendingTask' => 0,
                            'totalOverdueTasksLastWeek' => 0,
                        ];
                    @endphp

                    <!-- Total Tasks Till Last Week Row -->
                    <tr class="text-center">
                        <td class="SrNo">1</td>
                        <td>Total Tasks Till Last Week (Opening For This Week)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksLastWeek'] += $users['total_tasks_last_week'];
                            @endphp
                            <td>{{ $users['total_tasks_last_week'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksLastWeek'] }}</td> <!-- Display the total -->
                    </tr>

                    <!-- Total Pending Tasks Till Last Week Row -->
                    <tr class="text-center">
                        <td class="SrNo">2</td>
                        <td>Total Pending Tasks Till Last Week (Opening For This Week)</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalPendingTasksLastWeek'] += $users['total_pending_tasks_last_week'];
                            @endphp
                            <td>{{ $users['total_pending_tasks_last_week'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalPendingTasksLastWeek'] }}</td>
                    </tr>

                    <!-- Tasks Added in Last Week Row -->
                    <tr class="text-center">
                        <td class="SrNo">3</td>
                        <td>Tasks Added in Last Week</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksAddedLastWeek'] += $users['tasks_added_last_week'];
                            @endphp
                            <td>{{ $users['tasks_added_last_week'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksAddedLastWeek'] }}</td>
                    </tr>

                    <!-- Tasks Completed in Last Week Row -->
                    <tr class="text-center">
                        <td class="SrNo">4</td>
                        <td>Tasks Completed in Last Week</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksCompletedLastWeek'] += $users['tasks_completed_last_week'];
                            @endphp
                            <td>{{ $users['tasks_completed_last_week'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksCompletedLastWeek'] }}</td>
                    </tr>

                    <!-- Total Tasks Till Report Week Row -->
                    <tr class="highlight-yellow text-center">
                        <td class="SrNo">5</td>
                        <td>Total Tasks on Reported Date</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalTasksReportLastWeek'] += $users['task_report_date'];
                            @endphp
                            <td>{{ $users['task_report_date'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalTasksReportLastWeek'] }}</td>
                    </tr>

                    <!-- Total Pending Tasks Row -->
                    <tr class="highlight-yellow text-center">
                        <td class="SrNo">6</td>
                        <td>Total Pending Tasks</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalPendingTask'] += $users['total_pending_task'];
                            @endphp
                            <td>{{ $users['total_pending_task'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalPendingTask'] }}</td>
                    </tr>

                    <!-- Total Overdue Tasks Till Report Week Row -->
                    <tr class="text-center">
                        <td class="SrNo">7</td>
                        <td class="text-red-bold">Total Overdue Tasks Till Report Week</td>
                        @foreach ($table_data as $users)
                            @php
                                $totals['totalOverdueTasksLastWeek'] += $users['total_overdue_tasks_last_week'];
                            @endphp
                            <td>{{ $users['total_overdue_tasks_last_week'] }}</td>
                        @endforeach
                        <td>{{ $totals['totalOverdueTasksLastWeek'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    {{-- <div class="card table-responsive">
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

        <div class="card-body">
            <table id="users-status-bifurcation" class="table table-bordered">
                <thead>
                    <tr class="text-center">
                        <th>Sr. No</th>
                        <th>Particulars</th>
                        @foreach ($table_data as $users)
                            <th>
                                @if ($loop->first)
                                    Self
                                @else
                                    {{ $users['user_name'] }}
                                @endif
                            </th>
                        @endforeach
                        <th>Total</th> <!-- Add the Total column header -->
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
                        <td>Total Pendding Task</td>
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
    </div> --}}

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
