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
    <style>
        .requested-to-us {
            background-color: hsla(38, 92%, 64%, 0.615) !important;
            text-align: end;
            font-weight: 900;
        }

        .pending-tasks {
            background-color: hsl(219, 67%, 72%) !important;
            text-align: end;
            font-weight: 900;

        }

        .over-dues {
            background-color: hsla(5, 89%, 50%, 0.643) !important;
            text-align: end;
            font-weight: 900;
        }

        .today-dues {
            background-color: hsla(35, 66%, 77%, 0.643) !important;
            text-align: end;
            font-weight: 900;
        }

        .finish-tasks {
            background-color: hsla(130, 62%, 63%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }

        .total {
            background-color: hsla(130, 66%, 25%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }

        .rejected_tasks {
            background-color: hsla(131, 39%, 69%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }

        .all_status_columns {
            text-align: end;

        }
    </style>
@endsection
@section('content')

    <section class="app-user-list">
        <div class="row">


            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif
            @if (auth()->user()->hasRole('Super Admin'))
                <div class="col-lg-6 col-sm-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="fw-bolder mb-75">{{ $total_task_count }}</h3>
                                <span>Total Task</span>
                            </div>
                            <div class="avatar bg-light-primary p-50">
                                <span class="avatar-content">
                                    <i data-feather="align-left" class="font-medium-4"></i>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('export.dashboard_total_tasks') }}" class="btn btn-success">Export to
                                Excel</a>

                            <a href="{{ route('app-close_date_present_old') }}" class="btn btn-warning"
                                onclick="return confirm('Are you sure you want to update completed_date from close_date?');">
                                Update Completed Dates
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>


        <div class="card-header">
            <div>
                <h1>Dynamic Report</h1>
            </div>
            <div class="row mb-2 ">
                <div class="col-md-4">
                    <div class="form-group mb-2 fw-bold">
                        <label for="row-field-selector">Row</label>
                        <select id="row-field-selector" class="form-select select2" aria-label="Select Row">
                            <option value="">Select a Field</option>
                            <option value="task_id">Task ID</option>
                            <option value="Task_number">Task Number</option>
                            <option value="Task_Ticket">Task/Ticket</option>
                            <option value="title">Title</option>
                            <option value="description">Description</option>
                            <option value="subject">Subject</option>
                            <option value="created_by_username">Assigned By</option>
                            <option value="Task_assign_to">Assigned To</option>
                            <option value="task_status">Task Status</option>
                            <option value="Created_Date">Created Date</option>
                            <option value="start_date">Start Date</option>
                            <option value="due_date">Due Date</option>
                            <option value="completed_date">Completed Date</option>
                            <option value="accepted_date">Accepted Date</option>
                            <option value="project">Project</option>
                            <option value="department">Department</option>
                            <option value="sub_department">Sub Department</option>
                            <option value="creator_department">Creator Department</option>
                            <option value="creator_sub_department">Creator Sub Department</option>
                            <option value="creator_phone">Creator Phone</option>
                            <option value="close_date">Close Date</option>
                            <option value="status">Status (0,1,2)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-2 fw-bold">
                        <label for="column-field-selector">Column</label>
                        <select id="column-field-selector" class="form-select select2" aria-label="Select Column">
                            <option value="">Select a Field</option>
                            <option value="task_id">Task ID</option>
                            <option value="Task_number">Task Number</option>
                            <option value="Task_Ticket">Task/Ticket</option>
                            <option value="title">Title</option>
                            <option value="description">Description</option>
                            <option value="subject">Subject</option>
                            <option value="created_by_username">Assigned By</option>
                            <option value="Task_assign_to">Assigned To</option>
                            <option value="task_status">Task Status</option>
                            <option value="Created_Date">Created Date</option>
                            <option value="start_date">Start Date</option>
                            <option value="due_date">Due Date</option>
                            <option value="completed_date">Completed Date</option>
                            <option value="accepted_date">Accepted Date</option>
                            <option value="project">Project</option>
                            <option value="department">Department</option>
                            <option value="sub_department">Sub Department</option>
                            <option value="creator_department">Creator Department</option>
                            <option value="creator_sub_department">Creator Sub Department</option>
                            <option value="creator_phone">Creator Phone</option>
                            <option value="close_date">Close Date</option>
                            <option value="status">Status (0,1,2)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-2 fw-bold">
                        <label for="date-field-selector">Date Field</label>
                        <select id="date-field-selector" class="form-select select2" aria-label="Select Date Field">
                            <option value="">Select a Date Field</option>
                            <option value="Created_Date">Created Date</option>
                            <option value="start_date">Start Date</option>
                            <option value="due_date">Due Date</option>
                            <option value="completed_date">Completed Date</option>
                            <option value="accepted_date">Accepted Date</option>
                            <option value="close_date">Close Date</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="form-group  fw-bold">
                        <label for="date-from">From Date</label>
                        <input type="date" id="date-from" class="form-control flatpickr-basic"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group  fw-bold">
                        <label for="date-to">To Date</label>
                        <input type="date" id="date-to" class="form-control flatpickr-basic"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="text-danger mt-2">
                    <p><span class="text-danger">*</span> If no Date Field is selected, the system will automatically use
                        the Task's From Date and To Date.</p>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="preview-report-btn" class="btn btn-info me-2">
                        <i class="ficon" data-feather="eye"></i> Preview Report
                    </button>
                    <button id="generate-report-btn" class="btn btn-primary">
                        <i class="ficon" data-feather="download"></i> Download Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Loading indicator -->
            <div id="report-loading" style="display: none;" class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading report data...</p>
            </div>

            <!-- Report container -->
            <div id="report-container" style="display: none;">
                <h2 class="mb-3">Report Preview</h2>
                <div id="report-table"></div>
            </div>
        </div>
        <div class="card-body">
            <!-- Loading indicator -->
            <div id="report-loading" style="display: none;" class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading report data...</p>
            </div>

            <!-- Report container -->
            <div id="report-container" style="display: none;">
                <h2 class="mb-3">Report Preview</h2>
                <div id="report-table"></div>
            </div>
        </div>


        {{-- @if (auth()->user()->id != 1) --}}



        <!---- Requested to us  ---->
        <div class="card-header ">
            <h1>Requested To Us Tasks Lists
            </h1>
            <div style="height: 60vh; overflow-y: scroll;">
                <table id="requested_to_me" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Requested To Us</th>

                            @foreach ($statusinfos as $index => $status)
                                @if ($index <= 4 && $index != 2)
                                    <th>{{ $status->status_name }} </th>
                                @endif
                            @endforeach

                            <th>Pending Tasks</th>
                            <th>Over Due</th>
                            <th>Today's Due</th>
                            @foreach ($statusinfos as $index => $status)
                                @if ($index == 2)
                                    <th>{{ $status->status_name }} </th>
                                @endif
                            @endforeach

                            @foreach ($statusinfos as $index => $status)
                                @if ($index >= 5)
                                    <th>{{ $status->status_name }}</th>
                                @endif
                            @endforeach


                            <th>Finished Task</th>
                            <th>Total</th>
                            <th>Rejected Task</th>
                            <th>Overall Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Requested To Us Total -->
                            @foreach ($statusinfos as $index => $status)
                                <th></th> <!-- Status Totals -->
                            @endforeach
                            <th></th> <!-- Pending Tasks Total -->
                            <th></th> <!-- Overdue Tasks Total -->
                            <th></th> <!-- Today's Due Tasks Total -->
                            <th></th> <!-- Finished Tasks Total -->
                            <th></th> <!-- Grand Total -->
                            <th></th> <!-- Rejected Tasks Total -->
                            <th></th> <!-- Overall Total -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>



        <!---- Requested By Us  ----->
        <div class="card-header">
            <h1>Requested By Us Tasks Lists
            </h1>
            <div style="height: 60vh; overflow-y: scroll; overflow-x: scroll;">
                <table id="requested_by_me" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Requested By Us</th>



                            @foreach ($statusinfos as $index => $status)
                                @if ($index <= 4 && $index != 2)
                                    <th>{{ $status->status_name }} </th>
                                @endif
                            @endforeach

                            <th>Pending Tasks</th>
                            <th>Over Due</th>
                            <th>Today's Due</th>

                            @foreach ($statusinfos as $index => $status)
                                @if ($index == 2)
                                    <th>{{ $status->status_name }} </th>
                                @endif
                            @endforeach
                            @foreach ($statusinfos as $index => $status)
                                @if ($index >= 5)
                                    <th>{{ $status->status_name }}</th>
                                @endif
                            @endforeach


                            <th>Finished Task</th>
                            <th>Total</th>
                            <th>Rejected Tasks</th>
                            <th>Overall Total</th>

                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Requested To Us Total -->
                            @foreach ($statusinfos as $index => $status)
                                <th></th> <!-- Status Totals -->
                            @endforeach
                            <th></th> <!-- Pending Tasks Total -->
                            <th></th> <!-- Overdue Tasks Total -->
                            <th></th> <!-- Today's Due Tasks Total -->
                            <th></th> <!-- Finished Tasks Total -->
                            <th></th> <!-- Grand Total -->
                            <th></th> <!-- Rejected Tasks Total -->
                            <th></th> <!-- Overall Total -->
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>



        <!---- Total Task to us  ---->
        {{--  Commented As Anand bhai Said --}}

        {{-- <div class="card-header">
                <h1>Total Tasks Lists
                </h1>
                <table id="total_task_status" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Total Requested Status</th>



                            @foreach ($statusinfos as $index => $status)
                                @if ($index <= 3)
                                    <th>{{ $status->status_name }}</th>
                                @endif
                            @endforeach

                            <th>Pending Tasks</th>
                            <th>Over Due</th>
                            <th>Today's Due</th>

                            @foreach ($statusinfos as $index => $status)
                                @if ($index >= 4)
                                    <th>{{ $status->status_name }}</th>
                                @endif
                            @endforeach


                            <th>Finished Task</th>
                            <th>Total</th>

                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th> <!-- Requested To Us Total -->
                            @foreach ($statusinfos as $index => $status)
                                <th></th> <!-- Status Totals -->
                            @endforeach
                            <th></th> <!-- Pending Tasks Total -->
                            <th></th> <!-- Overdue Tasks Total -->
                            <th></th> <!-- Today's Due Tasks Total -->
                            <th></th> <!-- Finished Tasks Total -->
                            <th></th> <!-- Grand Total -->
                        </tr>
                    </tfoot>
                </table>
            </div> --}}







        {{-- @endif --}}
        {{-- <div class="card"> --}}

        {{-- </div> --}}

    </section>

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
            // Initialize Select2 for dropdowns if not already initialized
            if ($.fn.select2) {
                $('.select2').select2();
            }

            // Initialize Flatpickr for date pickers if available
            // if (typeof flatpickr !== 'undefined') {
            //     $('.flatpickr-basic').flatpickr({
            //         dateFormat: 'Y-m-d',
            //         allowInput: true
            //     });
            // }

            // Preview button to show data on page
            $('#preview-report-btn').on('click', function() {
                // Get selected row and column fields
                var rowField = $('#row-field-selector').val();
                var columnField = $('#column-field-selector').val();

                // Validate selections
                if (!rowField || !columnField) {
                    // Show error message
                    toastr.error('Please select both Row and Column fields', 'Error');
                    return;
                }

                // Show SweetAlert with options
                Swal.fire({
                    title: 'Choose Report Type',
                    text: 'How would you like to view the report?',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Summary',
                    denyButtonText: 'List View',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User chose Summary
                        generateSummaryReport(rowField, columnField);
                    } else if (result.isDenied) {
                        // User chose List View
                        redirectToListView(rowField, columnField);
                    }
                    // If cancelled, do nothing
                });
            });

            // Function to generate summary report (existing functionality)
            function generateSummaryReport(rowField, columnField) {
                // Show loading indicator
                $('#report-loading').show();
                $('#report-container').hide();

                // Collect filter values
                var filters = {
                    row_field: rowField,
                    column_field: columnField,
                    report_type: 'summary',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Add date range filters if selected
                var dateField = $('#date-field-selector').val();
                var fromDate = $('#date-from').val();
                var toDate = $('#date-to').val();

                if (dateField && (fromDate || toDate)) {
                    filters.date_field = dateField;
                    if (fromDate) filters.from_date = fromDate;
                    if (toDate) filters.to_date = toDate;
                }

                // Add any additional filters from the page
                if ($('#filter-department').length) {
                    filters.department = $('#filter-department').val();
                }

                if ($('#filter-assignee').length) {
                    filters.assignees = $('#filter-assignee').val();
                }

                if ($('#filter-status').length) {
                    filters.status = $('#filter-status').val();
                }

                // Make AJAX request to get report data
                $.ajax({
                    url: '/preview-dynamic-report',
                    type: 'POST',
                    data: filters,
                    success: function(response) {
                        // Hide loading indicator
                        $('#report-loading').hide();

                        // Render the report table
                        renderReportTable(response.data, response.columnValues, rowField,
                            columnField, response.fieldDisplayNames);

                        // Show the report container
                        $('#report-container').show();
                    },
                    error: function(xhr, status, error) {
                        // Hide loading indicator
                        $('#report-loading').hide();

                        // Show error message
                        toastr.error('Failed to load report data', 'Error');
                        console.error(error);
                    }
                });
            }

            // Function to redirect to list view
            function redirectToListView(rowField, columnField) {
                // Collect filter values
                var filters = {
                    row_field: rowField,
                    column_field: columnField,
                    report_type: 'dynamic_report'
                };

                // Add date range filters if selected
                var dateField = $('#date-field-selector').val();
                var fromDate = $('#date-from').val();
                var toDate = $('#date-to').val();

                if (dateField && (fromDate || toDate)) {
                    filters.date_field = dateField;
                    if (fromDate) filters.from_date = fromDate;
                    if (toDate) filters.to_date = toDate;
                }

                // Add any additional filters from the page
                if ($('#filter-department').length) {
                    filters.department = $('#filter-department').val();
                }

                if ($('#filter-assignee').length) {
                    filters.assignees = $('#filter-assignee').val();
                }

                if ($('#filter-status').length) {
                    filters.status = $('#filter-status').val();
                }

                // Build query string
                var queryString = $.param(filters);

                // Redirect to list view route
                window.location.href = 'dynamic-report-list?' + queryString;
            }

            // Function to render the report table (existing functionality)
            function renderReportTable(data, columnValues, rowField, columnField, fieldDisplayNames) {
                var tableHTML = '<div class="table-responsive"><table class="table table-bordered table-striped">';

                // Create header row
                tableHTML += '<thead><tr>';
                tableHTML += '<th>' + (fieldDisplayNames[rowField] || rowField) + '</th>';

                // Add column headers
                columnValues.forEach(function(columnValue) {
                    tableHTML += '<th>' + columnValue + '</th>';
                });

                // Add horizontal "Row Total" header
                tableHTML += '<th>Total</th>';
                tableHTML += '</tr></thead><tbody>';

                // Object to store column totals
                var columnTotals = {};
                columnValues.forEach(function(columnValue) {
                    columnTotals[columnValue] = 0;
                });

                var grandTotal = 0;

                // Add data rows with row totals
                Object.keys(data).forEach(function(rowValue) {
                    tableHTML += '<tr>';
                    tableHTML += '<td>' + rowValue + '</td>';

                    var rowTotal = 0;

                    // Add cells for each column
                    columnValues.forEach(function(columnValue) {
                        var cellValue = data[rowValue][columnValue] || 0;
                        tableHTML += '<td>' + cellValue + '</td>';

                        rowTotal += cellValue;
                        columnTotals[columnValue] += cellValue;
                        grandTotal += cellValue;
                    });

                    // Add row total
                    tableHTML += '<td><strong>' + rowTotal + '</strong></td>';
                    tableHTML += '</tr>';
                });

                // Add column totals row
                tableHTML += '<tr>';
                tableHTML += '<th>Total</th>';

                columnValues.forEach(function(columnValue) {
                    tableHTML += '<th><strong>' + columnTotals[columnValue] + '</strong></th>';
                });

                // Add grand total
                tableHTML += '<th><strong>' + grandTotal + '</strong></th>';
                tableHTML += '</tr>';

                tableHTML += '</tbody></table></div>';

                // Set the HTML content
                $('#report-table').html(tableHTML);
            }

            // Existing Excel download functionality
            $('#generate-report-btn').on('click', function() {
                // Get selected row and column fields
                var rowField = $('#row-field-selector').val();
                var columnField = $('#column-field-selector').val();

                // Validate selections
                if (!rowField || !columnField) {
                    // Show error message
                    toastr.error('Please select both Row and Column fields', 'Error');
                    return;
                }

                // Create a form for POST submission
                var form = $('<form>', {
                    'method': 'POST',
                    'action': '/generate-custom-excel-report',
                    'target': '_blank'
                });

                // Add CSRF token
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                // Add selected fields
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'row_field',
                    'value': rowField
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'column_field',
                    'value': columnField
                }));

                // Add date range filters if selected
                var dateField = $('#date-field-selector').val();
                var fromDate = $('#date-from').val();
                var toDate = $('#date-to').val();

                if (dateField) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'date_field',
                        'value': dateField
                    }));
                }

                if (fromDate) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'from_date',
                        'value': fromDate
                    }));
                }

                if (toDate) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'to_date',
                        'value': toDate
                    }));
                }

                // Add any additional filters from the page
                if ($('#filter-department').length) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'department',
                        'value': $('#filter-department').val()
                    }));
                }

                if ($('#filter-assignee').length) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'assignees',
                        'value': $('#filter-assignee').val()
                    }));
                }

                if ($('#filter-status').length) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'status',
                        'value': $('#filter-status').val()
                    }));
                }

                // Add the form to the document and submit it
                form.appendTo('body').submit().remove();
            });
        });
    </script>

    <script>
        $(document).ready(function() {


            /*----------  Requested to me  ------------*/
            $(document).ready(function() {
                var usersTable = $('#requested_to_me').DataTable({
                    processing: true,
                    serverSide: false,
                    // ajax: '{{ route('users.task.status.hierarchy') }}', // Ensure this route returns user task status data
                    ajax: '{{ route('users.task.requested_to_me') }}',
                    paging: false, // Disable pagination
                    info: false, // Disable the information summary
                    dom: 'Bfrtip', // Add this line to include Buttons
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                        title: '',
                        filename: 'User Task Status',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Extract numeric value from HTML links for export
                                    if (typeof data === 'string' && data.includes(
                                        '<a')) {
                                        var match = data.match(/>([^<]+)</);
                                        return match ? match[1] : data;
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var api = usersTable;

                            // Convert XML to string if it's not already
                            var sheetXml = (typeof sheet === 'string') ? sheet :
                                new XMLSerializer().serializeToString(sheet);

                            // Calculate column totals
                            var totalColumns = api.columns().count();
                            var totals = [];
                            totals[0] = 'TOTAL'; // First column label

                            for (var i = 1; i < totalColumns; i++) {
                                var columnTotal = api.column(i).data().reduce(function(
                                    a, b) {
                                    var value = parseFloat(b) || 0;
                                    return a + value;
                                }, 0);
                                totals[i] = columnTotal;
                            }

                            // Get the row count to add totals at the bottom
                            var rowCount = api.data().count() +
                            2; // +1 for header, +1 for 0-based index

                            // Create the totals row XML
                            var totalRowXml = '<row r="' + rowCount + '">';
                            for (var j = 0; j < totals.length; j++) {
                                var cellRef = String.fromCharCode(65 + j) +
                                rowCount; // A, B, C, etc.
                                var cellValue = totals[j];
                                var cellType = (j === 0) ? 'inlineStr' :
                                'n'; // String for first column, number for others

                                if (j === 0) {
                                    totalRowXml += '<c r="' + cellRef + '" t="' +
                                        cellType + '"><is><t>' + cellValue +
                                        '</t></is></c>';
                                } else {
                                    totalRowXml += '<c r="' + cellRef + '" t="' +
                                        cellType + '"><v>' + cellValue + '</v></c>';
                                }
                            }
                            totalRowXml += '</row>';

                            // Insert the totals row before closing sheetData
                            var sheetDataCloseTag = '</sheetData>';
                            sheetXml = sheetXml.replace(sheetDataCloseTag, totalRowXml +
                                sheetDataCloseTag);

                            // Convert back to XML object if needed
                            if (typeof sheet !== 'string') {
                                var parser = new DOMParser();
                                xlsx.xl.worksheets['sheet1.xml'] = parser
                                    .parseFromString(sheetXml, 'application/xml');
                            } else {
                                xlsx.xl.worksheets['sheet1.xml'] = sheetXml;
                            }
                        }
                    }],
                    columns: [{
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'requested_to_us',
                            name: 'requested_to_us',
                            class: 'requested-to-us',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId = row.status_id
                                let url =
                                    '{{ route('tasks.requested_to_us', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(':status_id',
                                    statusId);

                                return `<a href="${url}" class="text-primary">${data}</a>`;

                            }
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index <= 4 && $index != 2)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_to_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id',
                                            statusId);

                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }

                                },
                            @endif
                        @endforeach

                        {
                            data: 'pending_tasks',
                            name: 'pending_tasks',
                            class: 'pending-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_pending_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            },
                        },
                        {
                            data: 'over_dues',
                            name: 'over_dues',
                            class: 'over-dues',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_over_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },

                        {
                            data: 'today_dues',
                            name: 'today_dues',
                            class: 'today-dues',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_today_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        @foreach ($statusinfos as $index => $status)
                            @if ($index == 2)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_to_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id',
                                            statusId);

                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }

                                },
                            @endif
                        @endforeach
                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 5)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                        let url =
                                            '{{ route('tasks.requested_to_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id', statusId);
                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }
                                },
                            @endif
                        @endforeach

                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks',
                            class: 'finish-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_finished_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'total',
                            name: 'total',
                            class: 'total',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_total_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        }, {
                            data: 'rejected_tasks',
                            name: 'rejected_tasks',
                            class: 'rejected_tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_rejected_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'overall_total',
                            name: 'overall_total',
                            class: 'finish-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_overall_total', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },

                    ],
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        var totalColumns = api.columns().count();
                        var grandTotal = 0;

                        for (var i = 1; i < totalColumns; i++) {
                            var columnTotal = api.column(i).data().reduce(function(a, b) {
                                return a + (parseFloat(b) || 0);
                            }, 0);

                            // Construct the URL dynamically based on the column index or data
                            var userIds = data.map(function(row) {
                                return row.user_id;
                            }).join(',');

                            var status_id =
                                i; // Assuming each column corresponds to a `status_id`
                            var typeOrStatusId =
                                'requestedToUsTasks'; // Replace with your type logic
                            var routeUrl = createUrl(userIds, status_id, typeOrStatusId);

                            // Render the clickable link in the footer
                            $(api.column(i).footer()).html(renderClickableLink(routeUrl,
                                columnTotal));

                            grandTotal += columnTotal;
                        }


                        var totalColumnIndex = totalColumns -
                            1; // Assuming "Total" column is the last column
                        var verticalSum = api.column(totalColumnIndex, {
                                page: 'current'
                            }).data()
                            .reduce(function(a, b) {
                                return a + (parseFloat(b) || 0); // Sum vertically
                            }, 0);


                        // Render the grand total in the last column
                        // $(api.column(totalColumns - 1).footer()).html(grandTotal);

                        var grandTotalRouteUrl = createUrl(userIds, 'all',
                            typeOrStatusId); // Pass 'all' or any identifier for the grand total

                        $(api.column(totalColumns - 1).footer()).html(renderClickableLink(
                            grandTotalRouteUrl, verticalSum));

                    }

                });

                function createUrl(userId, status_id, typeOrStatusId) {
                    let routeUrl =
                        '{{ route('tasks.requested_to_us_footer_total', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => ':type_or_status_id']) }}';
                    return routeUrl
                        .replace(':user_id', userId)
                        .replace(':status_id', status_id)
                        .replace(':type_or_status_id', typeOrStatusId);
                }

                function renderClickableLink(routeUrl, data) {
                    return `<a href="${routeUrl}" class="text-primary">${data || 0}</a>`;
                }
            });



            /*-------- Requested By me Task -----------*/
            $(document).ready(function() {
                var usersTable = $('#requested_by_me').DataTable({
                    processing: true,
                    serverSide: false,
                    // ajax: '{{ route('users.task.status.hierarchy') }}', // Ensure this route returns user task status data
                    ajax: '{{ route('users.task.requested_me') }}',
                    paging: false, // Disable pagination
                    info: false, // Disable the information summary
                    dom: 'Bfrtip', // Add this line to include Buttons
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                        title: '',
                        filename: 'User Task Status',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    // Extract numeric value from HTML links for export
                                    if (typeof data === 'string' && data.includes(
                                        '<a')) {
                                        var match = data.match(/>([^<]+)</);
                                        return match ? match[1] : data;
                                    }
                                    return data;
                                }
                            }
                        },
                        customize: function(xlsx) {
                            // Simple approach - add totals using SheetJS utilities
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            var api = usersTable;

                            // Calculate totals
                            var totalColumns = api.columns().count();
                            var totals = [];
                            totals.push('TOTAL'); // First column

                            for (var i = 1; i < totalColumns; i++) {
                                var columnTotal = api.column(i).data().reduce(function(
                                    a, b) {
                                    return a + (parseFloat(b) || 0);
                                }, 0);
                                totals.push(columnTotal);
                            }

                            // Add totals row to the workbook
                            var wb = xlsx;
                            var ws = wb.xl.worksheets['sheet1.xml'];

                            // Get current row count and add totals
                            var rowCount = api.data().count() + 1; // +1 for header

                            // Create totals row in the sheet
                            var $sheet = $(ws);
                            var lastRow = $sheet.find('row').last();
                            var newRowNumber = rowCount + 1;

                            var totalRowXml = '<row r="' + newRowNumber + '">';

                            for (var j = 0; j < totals.length; j++) {
                                var colLetter = String.fromCharCode(65 +
                                j); // A, B, C, etc.
                                var cellRef = colLetter + newRowNumber;
                                var cellValue = totals[j];

                                if (j === 0) {
                                    // Text cell for "TOTAL"
                                    totalRowXml += '<c r="' + cellRef +
                                        '" t="inlineStr"><is><t>' + cellValue +
                                        '</t></is></c>';
                                } else {
                                    // Number cell for totals
                                    totalRowXml += '<c r="' + cellRef + '"><v>' +
                                        cellValue + '</v></c>';
                                }
                            }

                            totalRowXml += '</row>';

                            // Insert the row before </sheetData>
                            var sheetStr = (new XMLSerializer()).serializeToString(ws);
                            sheetStr = sheetStr.replace('</sheetData>', totalRowXml +
                                '</sheetData>');

                            // Parse back to XML
                            var parser = new DOMParser();
                            xlsx.xl.worksheets['sheet1.xml'] = parser.parseFromString(
                                sheetStr, 'text/xml');
                        }
                    }],
                    columns: [{
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'requested_by_us',
                            name: 'requested_by_us',
                            class: 'requested-to-us',

                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId = row.status_id
                                let url =
                                    '{{ route('tasks.requested_by_us', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(':status_id',
                                    statusId);

                                return `<a href="${url}" class="text-primary">${data}</a>`;

                            }
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index <= 4 && $index != 2)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id',
                                            statusId);

                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }

                                },
                            @endif
                        @endforeach

                        {
                            data: 'pending_tasks',
                            name: 'pending_tasks',
                            class: 'pending-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_pending_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'over_dues',
                            name: 'over_dues',
                            class: 'over-dues',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_over_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'today_dues',
                            name: 'today_dues',
                            class: 'today-dues',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_today_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },

                        // @foreach ($statusinfos as $index => $status)
                        //     @if ($index >= 4)
                        //         {
                        //             data: '{{ \Str::slug($status->status_name, '_') }}',
                        //             name: '{{ \Str::slug($status->status_name, '_') }}',
                        //             render: function(data, type, row) {
                        //                 let userId = row.user_id;
                        //                 let statusId =
                        //                     '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                        //                 let url =
                        //                     '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                        //                 url = url.replace(':user_id', userId).replace(
                        //                     ':status_id',
                        //                     statusId);

                        //                 return `<a href="${url}" class="text-primary">${data}</a>`;
                        //             }
                        //         },
                        //     @endif
                        // @endforeach

                        @foreach ($statusinfos as $index => $status)
                            @if ($index == 2)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id',
                                            statusId);

                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }

                                },
                            @endif
                        @endforeach
                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 5)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class: 'all_status_columns',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                        let url =
                                            '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_to_us']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id', statusId);
                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }
                                },
                            @endif
                        @endforeach


                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks',
                            class: 'finish-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_finished_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'total',
                            name: 'total',
                            class: 'total',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_total_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'rejected_tasks',
                            name: 'rejected_tasks',
                            class: 'rejected_tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_rejected_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },
                        {
                            data: 'overall_task',
                            name: 'overall_task',
                            class: 'finish-tasks',
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_by_us_overall_total', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        }

                    ],
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        var totalColumns = api.columns().count();
                        var grandTotal = 0;

                        for (var i = 1; i < totalColumns; i++) {
                            var columnTotal = api.column(i).data().reduce(function(a, b) {
                                return a + (parseFloat(b) || 0);
                            }, 0);

                            console.log(row);
                            // Construct the URL dynamically based on the column index or data
                            var userIds = data.map(function(row) {
                                return row.user_id;
                            }).join(',');

                            var status_id =
                                i; // Assuming each column corresponds to a `status_id`
                            var typeOrStatusId =
                                'requestedToUsTasks'; // Replace with your type logic
                            var routeUrl = createUrl(userIds, status_id, typeOrStatusId);

                            // Render the clickable link in the footer
                            $(api.column(i).footer()).html(renderClickableLink(routeUrl,
                                columnTotal));

                            grandTotal += columnTotal;
                        }

                        var totalColumnIndex = totalColumns -
                            1; // Assuming "Total" column is the last column
                        var verticalSum = api.column(totalColumnIndex, {
                                page: 'current'
                            }).data()
                            .reduce(function(a, b) {
                                return a + (parseFloat(b) || 0); // Sum vertically
                            }, 0);


                        // Render the grand total in the last column
                        // $(api.column(totalColumns - 1).footer()).html(grandTotal);
                        var grandTotalRouteUrl = createUrl(userIds, 'all',
                            typeOrStatusId); // Pass 'all' or any identifier for the grand total

                        $(api.column(totalColumns - 1).footer()).html(renderClickableLink(
                            grandTotalRouteUrl, verticalSum));
                    }

                });

                function createUrl(userId, status_id, typeOrStatusId) {
                    let routeUrl =
                        '{{ route('tasks.requested_by_us_footer_total', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => ':type_or_status_id']) }}';
                    return routeUrl
                        .replace(':user_id', userId)
                        .replace(':status_id', status_id)
                        .replace(':type_or_status_id', typeOrStatusId);
                }

                function renderClickableLink(routeUrl, data) {
                    return `<a href="${routeUrl}" class="text-primary">${data || 0}</a>`;
                }


            });





            /*--------   Total Task Status ---------*/

            // Commented As Anand bhai Said
            // $(document).ready(function() {
            //     var usersTable = $('#total_task_status').DataTable({
            //         processing: true,
            //         serverSide: false,
            //         // ajax: '{{ route('users.task.status.hierarchy') }}', // Ensure this route returns user task status data
            //         ajax: '{{ route('users.total_task_details') }}',
            //         paging: false, // Disable pagination
            //         info: false, // Disable the information summary
            //         dom: 'Bfrtip', // Add this line to include Buttons
            //         buttons: [{
            //             extend: 'excel',
            //             text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
            //             title: '',
            //             filename: 'User Task Status',
            //             className: 'btn btn-success btn-sm'
            //         }],
            //         columns: [{
            //                 data: 'user_name',
            //                 name: 'user_name'
            //             },
            //             {
            //                 data: 'total_tasks',
            //                 name: 'total_tasks',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId = row.status_id
            //                     let url =
            //                         '{{ route('tasks.total_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(':status_id',
            //                         statusId);

            //                     return `<a href="${url}" class="text-primary">${data}</a>`;

            //                 }
            //             },

            //             @foreach ($statusinfos as $index => $status)
            //                 @if ($index <= 3)
            //                     {
            //                         data: '{{ \Str::slug($status->status_name, '_') }}',
            //                         name: '{{ \Str::slug($status->status_name, '_') }}',
            //                         render: function(data, type, row) {
            //                             let userId = row.user_id;
            //                             let statusId =
            //                                 '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

            //                             let url =
            //                                 '{{ route('tasks.total_task_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                             url = url.replace(':user_id', userId).replace(
            //                                 ':status_id',
            //                                 statusId);

            //                             return `<a href="${url}" class="text-primary">${data}</a>`;
            //                         }
            //                     },
            //                 @endif
            //             @endforeach

            //             {
            //                 data: 'pending_tasks',
            //                 name: 'pending_tasks',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId =
            //                         '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
            //                     let url =
            //                         '{{ route('tasks.total_task_pending_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(
            //                         ':status_id', statusId);
            //                     return `<a href="${url}" class="text-primary">${data}</a>`;
            //                 }
            //             },
            //             {
            //                 data: 'over_dues',
            //                 name: 'over_dues',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId =
            //                         '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
            //                     let url =
            //                         '{{ route('tasks.total_task_over_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(
            //                         ':status_id', statusId);
            //                     return `<a href="${url}" class="text-primary">${data}</a>`;
            //                 }
            //             },
            //             {
            //                 data: 'today_dues',
            //                 name: 'today_dues',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId =
            //                         '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
            //                     let url =
            //                         '{{ route('tasks.total_task_today_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(
            //                         ':status_id', statusId);
            //                     return `<a href="${url}" class="text-primary">${data}</a>`;
            //                 }
            //             },

            //             @foreach ($statusinfos as $index => $status)
            //                 @if ($index >= 4)
            //                     {
            //                         data: '{{ \Str::slug($status->status_name, '_') }}',
            //                         name: '{{ \Str::slug($status->status_name, '_') }}',
            //                         render: function(data, type, row) {
            //                             let userId = row.user_id;
            //                             let statusId =
            //                                 '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

            //                             let url =
            //                                 '{{ route('tasks.total_task_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                             url = url.replace(':user_id', userId).replace(
            //                                 ':status_id',
            //                                 statusId);

            //                             return `<a href="${url}" class="text-primary">${data}</a>`;
            //                         }
            //                     },
            //                 @endif
            //             @endforeach

            //             {
            //                 data: 'finish_tasks',
            //                 name: 'finish_tasks',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId =
            //                         '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
            //                     let url =
            //                         '{{ route('tasks.total_task_finished_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(
            //                         ':status_id', statusId);
            //                     return `<a href="${url}" class="text-primary">${data}</a>`;
            //                 }
            //             },
            //             {
            //                 data: 'total',
            //                 name: 'total',
            //                 render: function(data, type, row) {
            //                     let userId = row.user_id;
            //                     let statusId =
            //                         '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
            //                     let url =
            //                         '{{ route('tasks.total_task_total_task', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'total_task']) }}';
            //                     url = url.replace(':user_id', userId).replace(
            //                         ':status_id', statusId);
            //                     return `<a href="${url}" class="text-primary">${data}</a>`;
            //                 }
            //             },

            //         ],
            //         footerCallback: function(row, data, start, end, display) {
            //             var api = this.api();
            //             var totalColumns = api.columns().count();
            //             var grandTotal = 0;

            //             for (var i = 1; i < totalColumns; i++) {
            //                 var columnTotal = api.column(i).data().reduce(function(a, b) {
            //                     return a + (parseFloat(b) || 0);
            //                 }, 0);

            //                 console.log(row);
            //                 // Construct the URL dynamically based on the column index or data
            //                 var userIds = data.map(function(row) {
            //                     return row.user_id;
            //                 }).join(',');

            //                 var status_id =
            //                     i; // Assuming each column corresponds to a `status_id`
            //                 var typeOrStatusId =
            //                     'requestedToUsTasks'; // Replace with your type logic
            //                 var routeUrl = createUrl(userIds, status_id, typeOrStatusId);

            //                 // Render the clickable link in the footer
            //                 $(api.column(i).footer()).html(renderClickableLink(routeUrl,
            //                     columnTotal));

            //                 grandTotal += columnTotal;
            //             }


            //             var totalColumnIndex = totalColumns -
            //                 1; // Assuming "Total" column is the last column
            //             var verticalSum = api.column(totalColumnIndex, {
            //                     page: 'current'
            //                 }).data()
            //                 .reduce(function(a, b) {
            //                     return a + (parseFloat(b) || 0); // Sum vertically
            //                 }, 0);


            //             // Render the grand total in the last column
            //             // $(api.column(totalColumns - 1).footer()).html(grandTotal);
            //             var grandTotalRouteUrl = createUrl(userIds, 'all',
            //                 typeOrStatusId); // Pass 'all' or any identifier for the grand total

            //             $(api.column(totalColumns - 1).footer()).html(renderClickableLink(
            //                 grandTotalRouteUrl, verticalSum));
            //         }

            //     });

            //     function createUrl(userId, status_id, typeOrStatusId) {
            //         let routeUrl =
            //             '{{ route('tasks.total_task_footer_total', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => ':type_or_status_id']) }}';
            //         return routeUrl
            //             .replace(':user_id', userId)
            //             .replace(':status_id', status_id)
            //             .replace(':type_or_status_id', typeOrStatusId);
            //     }

            //     function renderClickableLink(routeUrl, data) {
            //         return `<a href="${routeUrl}" class="text-primary">${data || 0}</a>`;
            //     }


            // });





        });
    </script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script>





@endsection
