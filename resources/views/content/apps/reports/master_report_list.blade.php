@extends('layouts/contentLayoutMaster')

@section('title', 'Master Reports')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
@endsection


@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection


@section('content')
    <!-- Projects list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-project-list">

        <!-- list and filter start -->
        <div class="card">

            <div class="card-header">


            </div>

            <div class="card-body border-bottom">
                <div class="row d-flex align-items-center">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="btn btn-primary btn-sm w-100"><strong>MEETING BUCKET</strong> -
                                    <strong>WEDNESDAY</strong> -
                                    BLOCK 2
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="btn btn-primary btn-sm pending-tasks-count"><strong>Total Pending Tasks:</strong>
                                    {{ $pendingTasksCount }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="btn btn-primary btn-sm overdue-tasks-count"><strong>Total Overdue Tasks:</strong>
                                    {{ $overdueTasksCount }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="btn btn-primary btn-sm pace-rate"><strong>Pace Rate:</strong>
                                    {{ number_format($paceRate * 100, 2) }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="projectSelect"><strong>Select Project</strong></label>
                                <select id="projectSelect" class="form-control select2 w-100" multiple="multiple">
                                    <option value="">All</option>
                                    @foreach ($projectOptions as $project)
                                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label for="statusSelect"><strong>Select Status</strong></label>
                                <select id="statusSelect" class="form-control select2 w-100" multiple="multiple">
                                    <option value="">All</option>
                                    @foreach ($statusOptions as $status)
                                        <option value="{{ $status->id }}">{{ $status->displayname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="user-list-table table dt-responsive" id="masters_report-table">
                        <thead>
                            <tr>
                                <th>Task Number</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Assigned By</th>
                                <th>Task Assigned To</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>New Due Date</th>
                                <th>Department</th>
                                <th>Project Name</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
    @yield('links')
@endsection

@section('page-script')

    <script>
        $(document).ready(function() {
            // Trigger when the selection changes
            $('#projectSelect').on('change', function() {
                var selectedProjectIds = $(this).val(); // Get selected project IDs

                // Make AJAX request to fetch the task counts
                $.ajax({
                    url: '{{ route('get.task.counts') }}', // The route we defined earlier
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // CSRF token
                        project_ids: selectedProjectIds // Pass the selected project IDs
                    },
                    success: function(response) {
                        // Append the returned data to the buttons
                        $('.pending-tasks-count').text('Total Pending Tasks: ' + response
                            .pendingTasksCount);
                        $('.overdue-tasks-count').text('Total Overdue Tasks: ' + response
                            .overdueTasksCount);
                        $('.pace-rate').text('Pace Rate: ' + response.paceRate + '%');
                    },
                    error: function(error) {
                        console.error('Error fetching task counts:', error);
                    }
                });
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $('#masters_report-table').DataTable({
                dom: '<"export-buttons"B>lfrtip',
                processing: true,
                serverSide: true,
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    action: newexportaction, // Custom export action function
                    title: '',
                    filename: 'Task',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        modifier: {
                            length: -1 // Exports all rows
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8,
                            9
                        ] // Export specific columns (adjust indexes as needed)
                    }
                }],
                ajax: {
                    url: "{{ route('app-masters_report-get-all') }}", // URL to fetch data via AJAX
                    data: function(d) {
                        // Add selected project IDs to the request data (as an array)
                        d.project_ids = $('#projectSelect')
                            .val(); // Send selected project IDs as an array
                        d.status_ids = $('#statusSelect')
                            .val(); // Send selected project IDs as an array
                    }
                },
                columns: [{
                        data: 'Task_number',
                        name: 'Task_number',
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    }, {
                        data: 'created_by_username',
                        name: 'created_by_username',
                    },
                    {
                        data: 'Task_assign_to',
                        name: 'Task_assign_to',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                    },
                    {
                        data: 'department',
                        name: 'department',
                    },
                    {
                        data: 'project',
                        name: 'project',
                    },
                ],
                drawCallback: function() {
                    feather.replace(); // Replace Feather icons after each draw
                }
            });
            $('#projectSelect').select2({
                placeholder: 'Select Projects',
                allowClear: true
            });

            // Listen for changes on the project dropdown
            $('#projectSelect').on('change', function() {
                // Redraw the DataTable with the selected project filter
                $('#masters_report-table').DataTable().ajax.reload();
            });

            $('#statusSelect').select2({
                placeholder: 'Select Status',
                allowClear: true
            });

            // Listen for changes on the project dropdown
            $('#statusSelect').on('change', function() {
                // Redraw the DataTable with the selected project filter
                $('#masters_report-table').DataTable().ajax.reload();
            });
            // Custom Excel export action function
            function newexportaction(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;
                dt.one('preXhr', function(e, s, data) {
                    // Just this once, load all data from the server...
                    data.start = 0;
                    data.length = 2147483647;
                    dt.one('preDraw', function(e, settings) {
                        // Call the original action function
                        if (button[0].className.indexOf('buttons-copy') >= 0) {
                            $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button,
                                config);
                        } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                            $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt,
                                    button, config) :
                                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt,
                                    button, config);
                        } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                            $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button,
                                    config) :
                                $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button,
                                    config);
                        } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                            $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button,
                                    config) :
                                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button,
                                    config);
                        } else if (button[0].className.indexOf('buttons-print') >= 0) {
                            $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                        }
                        dt.one('preXhr', function(e, s, data) {
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                        });
                        // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                        setTimeout(dt.ajax.reload, 0);
                        // Prevent rendering of the full data to the DOM
                        return false;
                    });
                });
                // Requery the server with the new one-time export settings
                dt.ajax.reload();
            }
        });
    </script>

    {{-- Page js files --}}
@endsection

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>

@yield('js_load')
