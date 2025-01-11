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
        }
    </style>
@endsection

@section('content')
    <div class="card table-responsive">
        <div class="card-header">
            <h4>Task Report</h4>
        </div>
        <div class="card-body ">
            <table id="g7-table" class="table table-bordered w-100">
                <thead>
                    <tr>
                        <th>G7 Name</th>
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
                    @forelse ($usersWithG7 as $user)
                        <tr>
                            <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                            <td></td>
                            <td></td>
                            <td>%</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>%</td>
                            <td>{{ $conceptualizationCounts[$user->id] ?? 0 }}</td>
                            <td>{{ $scopeDefineCounts[$user->id] ?? 0 }}</td>

                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">No users found with G7.</td>
                        </tr>
                    @endforelse

                    {{-- Static rows for Total and Inactive --}}
                    <tr>
                        <td>Total</td>
                        <td colspan="12">---</td>
                    </tr>
                    <tr class="inactive-row">
                        <td>Inactive</td>
                        <td colspan="12">---</td>
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
            $('#g7-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('reports.g7-data') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
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
                    },
                ],
            });
        });
    </script>

@endsection
