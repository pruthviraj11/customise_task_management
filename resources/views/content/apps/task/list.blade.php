@extends('layouts/contentLayoutMaster')

@section('title', 'Task List')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">

@endsection

<style>
    .table-responsive {
        position: relative;
        height: 600px;
        /* Set a specific height for the scrollable area */
        overflow-y: auto;
        /* Allow vertical scrolling */
        margin-top: 20px;
    }

    #tasks-table thead th {
        position: sticky;
        top: 0;
        /* Ensure the same background as the table header */
        z-index: 10;
    }

    #tasks-table th,
    #tasks-table td {
        padding: 10px;
        text-align: center;
    }
</style>
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection


@section('content')
    <!-- departments list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-task-list">

        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Task list</h4>

            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    @if ($type === 'list')
                        <!-- Filter Inputs -->
                        {{-- <input type="text" id="filter-title" placeholder="Filter by Title"> --}}
                        <div class="row mb-2">
                            <div class="mb-1 col-3">
                                <label class="form-label" for="filter-status">Task Or Ticket</label>
                                <select id="task" class="form-select select2">
                                    <option value="">ALL</option>
                                    <option value="0">Task</option>
                                    <option value="1">Ticket</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-status">Filter by Status
                                    By</label>
                                <select class="  form-select select2" id="filter-status">
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-project">Filter by Project</label>
                                <select class="form-select select2" id="filter-project">
                                    <option value="">ALL</option>
                                </select>
                            </div>


                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-created-by">Filter by Created
                                    By</label>
                                <select id="filter-created-by" class="form-select select2">
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3">
                                <label class="form-label" for="filter-assignee">Filter by Assignee</label>
                                <select id="filter-assignee" class="form-select select2" multiple>
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-department">Filter by department</label>
                                <select id="filter-department"class=" form-select select2">
                                    <option value="">ALL </option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="dt_date">Start Date
                                    By</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input"
                                    data-mb-1column="5" id="dt_date" placeholder="StartDate to EndDate"
                                    data-mb-1column-index="4" name="dt_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_end_date" data-column="5" data-column-index="4" />
                            </div>
                            <div class="mb-1  col-3">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input" data-column="5"
                                    id="end_date" placeholder="StartDate to EndDate" data-column-index="4"
                                    name="end_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_end_date" data-column="5" data-column-index="4" />
                            </div>

                            <div class="mb-1 col-3">
                                <label class="form-label" for="accepted_task_date">Accepted Task Date</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input"
                                    data-column="5" id="accepted_task_date" placeholder="Start Date to End Date"
                                    data-column-index="4" name="accepted_task_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_accepted_task_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_accepted_task_end_date" data-column="5" data-column-index="4" />
                            </div>


                            {{-- <input type="date" id="filter-start-date" class="col  form-control"
                                placeholder="Filter by Start Date"> --}}
                            <div class="col-md-12 text-end">
                                <button id="apply-filters" class="mt-2 col btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-row mb-1 col-auto gap-1">
                        <a href="{{ route('app-task-add') }}" class=" justify-content-center btn btn-warning"> <i
                                data-feather="plus-circle" class="font-medium-4"></i> Add Task
                        </a>

                        @if ($type != 'requested' && $type != 'my_and_team' && $type != 'deleted')
                            <a href="{{ route('app-task-cardView', $type) }}" class="btn btn-info">Card View</a>
                        @endif
                        {{-- <a href="{{ route('export-tasks') }}" class="btn btn-success">Export</a> --}}
                        @if (auth()->user()->hasRole('Super Admin') && $type == 'list')
                            <a href="{{ route('export-total-tasks') }}" class="btn btn-success">Export</a>
                        @endif
                    </div>
                    <table class="user-list-table table " id="tasks-table">
                        <thead>
                            <tr class="">

                                <th>Actions</th>
                                <th>Task</th>
                                <th>Task Number</th>
                                <th>Task/Ticket</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Subject</th>
                                <th>Assign By</th>
                                <th>Task assign to</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th>Completed Date</th>
                                <th>Accepted task Date</th>
                                <th>Project</th>
                                <th>Department</th>
                                <th>Sub Department</th>
                                <th>Owner Department</th>
                                <th>Owner Sub Department</th>
                                <th>Owner Contatinfo</th>
                                <th>Close Date</th>


                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        <!-- list and filter end -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="rejectForm" action="" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Remark</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">


                            <div class="form-group">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea name="remark" class="form-control" id="message-text"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    {{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Remark</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea name="remark" class="form-control" id="message-text"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send message</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

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
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>

    <script>
        // Datepicker for advanced filter
        var separator = ' - ',
            rangePickr = $('.flatpickr-range'),
            dateFormat = 'DD/MM/YYYY';
        var options = {
            autoUpdateInput: false,
            autoApply: true,
            locale: {
                format: dateFormat,
                separator: separator
            },
            opens: $('html').attr('data-textdirection') === 'rtl' ? 'left' : 'right'
        };

        //
        if (rangePickr.length) {
            rangePickr.flatpickr({
                mode: 'range',
                dateFormat: 'd/m/Y',
                onClose: function(selectedDates, dateStr, instance) {
                    var startDate = '',
                        endDate = new Date();
                    if (selectedDates[0] != undefined) {
                        startDate =
                            selectedDates[0].getMonth() + 1 + '/' + selectedDates[0].getDate() + '/' +
                            selectedDates[0].getFullYear();
                        $('.start_date').val(startDate);
                    }
                    if (selectedDates[1] != undefined) {
                        endDate =
                            selectedDates[1].getMonth() + 1 + '/' + selectedDates[1].getDate() + '/' +
                            selectedDates[1].getFullYear();
                        $('.end_date').val(endDate);
                    }
                    $(rangePickr).trigger('change').trigger('keyup');
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // function newexportaction(e, dt, button, config) {
            //     var self = this;
            //     var oldStart = dt.settings()[0]._iDisplayStart;
            //     dt.one('preXhr', function(e, s, data) {
            //         // Load all data from the server
            //         data.start = 0;
            //         data.length = 2147483647;
            //         dt.one('preDraw', function(e, settings) {
            //             // Call the original action function
            //             if (button[0].className.indexOf('buttons-copy') >= 0) {
            //                 $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button,
            //                     config);
            //             } else if (button[0].className.indexOf('buttons-excel') >= 0) {
            //                 $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
            //                     $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt,
            //                         button, config) :
            //                     $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt,
            //                         button, config);
            //             } else if (button[0].className.indexOf('buttons-csv') >= 0) {
            //                 $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
            //                     $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button,
            //                         config) :
            //                     $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button,
            //                         config);
            //             } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
            //                 $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
            //                     $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button,
            //                         config) :
            //                     $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button,
            //                         config);
            //             } else if (button[0].className.indexOf('buttons-print') >= 0) {
            //                 $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            //             }
            //             dt.one('preXhr', function(e, s, data) {
            //                 settings._iDisplayStart = oldStart;
            //                 data.start = oldStart;
            //             });
            //             // Reload the grid with the original page
            //             setTimeout(dt.ajax.reload, 0);
            //             // Prevent rendering of the full data to the DOM
            //             return false;
            //         });
            //     });
            //     // Requery the server with the new one-time export settings
            //     dt.ajax.reload();
            // }


            var type = @json($type);
            @if ($type === 'accepted')
                {
                    ajaxUrl = "{{ route('app-task-get-accepted') }}";
                }
            @elseif ($type === 'team_task') {
                    ajaxUrl = "{{ route('app-task-get-team_task-list') }}";
                }
            @elseif ($type === 'requested') {
                    ajaxUrl = "{{ route('app-task-get-requested') }}";
                }
            @elseif ($type === 'list') {
                    ajaxUrl = "{{ route('app-task-getAll_total_task-get') }}";
                }
            @elseif ($type == 'mytask') {
                    ajaxUrl = "{{ route('app-task-mytask-get') }}";
                }
            @elseif ($type == 'accepted_by_me') {
                    ajaxUrl = "{{ route('app-task-getAll_accepted_by_me-get') }}";
                }
            @elseif ($type == 'assign_by_me') {
                    ajaxUrl = "{{ route('app-task-getAll_assign_by_me-get') }}";
                }
            @elseif ($type == 'requested_me') {
                    ajaxUrl = "{{ route('app-task-getAll_requested_me-get') }}";
                }
            @elseif ($type == 'conceptualization') {
                    ajaxUrl = "{{ route('app-task-getAll_conceptualization-get') }}";
                }
            @elseif ($type == 'close') {
                    ajaxUrl = "{{ route('app-task-getAll_close-get') }}";
                }
            @elseif ($type == 'due_date_past') {
                    ajaxUrl = "{{ route('app-task-getAll_due_date_past-get') }}";
                }
            @elseif ($type == 'scope_defined') {
                    ajaxUrl = "{{ route('app-task-getAll_scope_defined-get') }}";
                }
            @elseif ($type == 'completed') {
                    ajaxUrl = "{{ route('app-task-getAll_completed-get') }}";
                }
            @elseif ($type == 'hold') {
                    ajaxUrl = "{{ route('app-task-getAll_hold-get') }}";
                }
            @elseif ($type == 'in_execution') {
                    ajaxUrl = "{{ route('app-task-getAll_in_execution-get') }}";
                }
            @elseif ($type == 'admin_acc') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_acc-get') }}";
                }
            @elseif ($type == 'admin_req') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_req-get') }}";
                }
            @elseif ($type == 'admin_rej') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_rej-get') }}";
                }
            @elseif ($type == 'total_task') {
                    ajaxUrl = "{{ route('app-task-getAll_total_task-get') }}";
                }
            @elseif ($type == 'deleted') {
                    ajaxUrl = "{{ route('app-task-getAll_deleted-get') }}";
                }
            @elseif ($type == 'my_and_team') {
                    ajaxUrl = "{{ route('app-task-my_and_team-get') }}";
                }
            @elseif ($type == 'tasks.requested_to_us' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @endif
            var table = $('#tasks-table').DataTable({
                dom: '<"export-buttons"B>lfrtip',
                processing: true,
                serverSide: true,
                // pageLength: 10,
                filter: true,
                deferRender: true,
                // scrollY: 200,
                scrollCollapse: true,
                scroller: true,
                "searching": true,
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    action: newexportaction,
                    title: '',
                    filename: 'Task',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        modifier: {
                            length: -1
                        },
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 13, 14, 15, 16, 17]
                    }
                }],
                ajax: {
                    url: ajaxUrl,
                    data: function(d) {
                        d.department = $('#filter-department').val();
                        d.assignees = $('#filter-assignee').val();
                        d.dt_date = $('#dt_date').val();
                        d.task = $('#task').val();
                        d.accepted_task_date = $('#accepted_task_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#filter-status').val();
                        d.project = $('#filter-project').val();
                        d.created_by = $('#filter-created-by').val();
                        d.start_date = $('#filter-start-date').val();
                    }
                },
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        searchable: false
                    }, // Non-searchable column
                    {
                        data: 'task_id',
                        name: 'task_id',
                        searchable: true,
                        visible: false
                    },
                    {
                        data: 'Task_number',
                        name: 'Task_number',
                        searchable: true
                    },
                    {
                        data: 'Task_Ticket',
                        name: 'Task_Ticket',
                        searchable: true
                    },
                    {
                        data: 'title',
                        name: 'title',
                        searchable: true
                    },
                    {
                        data: 'description',
                        name: 'description',
                        searchable: true,
                        visible: false,
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                        searchable: true
                    },
                    {
                        data: 'created_by_username',
                        name: 'created_by_username',
                        searchable: true
                    },
                    {
                        data: 'Task_assign_to',
                        name: 'Task_assign_to',
                        searchable: true
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true
                    },
                    {
                        data: 'Created_Date',
                        name: 'Created_Date',
                        searchable: true

                    }, {
                        data: 'start_date',
                        name: 'start_date',
                        searchable: true

                    },

                    {
                        data: 'due_date',
                        name: 'due_date',
                        searchable: true

                    },
                    {
                        data: 'completed_date',
                        name: 'completed_date',
                        searchable: true,
                    },

                    {
                        data: 'accepted_date',
                        name: 'accepted_date',
                        searchable: true
                    },
                    {
                        data: 'project',
                        name: 'project',
                        searchable: true
                    },
                    {
                        data: 'department',
                        name: 'department',
                        searchable: true
                    },
                    {
                        data: 'sub_department',
                        name: 'sub_department',
                        searchable: true
                    },
                    {
                        data: 'creator_department',
                        name: 'creator_department',
                        searchable: true
                    },
                    {
                        data: 'creator_sub_department',
                        name: 'creator_sub_department',
                        searchable: true
                    },
                    {
                        data: 'creator_phone',
                        name: 'creator_phone',
                        searchable: true
                    },
                    {
                        data: 'close_date',
                        name: 'close_date',
                        searchable: true,
                    },

                ],

                drawCallback: function() {
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

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

            // Apply Filters
            $('#apply-filters').on('click', function() {
                table.draw();
            });
        });


        $(document).ready(function() {
            // Fetch status options and populate the dropdown
            $.ajax({
                url: '{{ route('get-status') }}', // Assuming this is the endpoint to fetch status options
                method: 'GET',
                success: function(response) {
                    // Assuming response is an array of status objects
                    response.forEach(function(status) {
                        $('#filter-status').append('<option value="' + status.id + '">' + status
                            .displayname + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching status options:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-projects') }}', // Define this route for fetching project options
                method: 'GET',
                success: function(response) {
                    // Assuming response is an array of project objects
                    response.forEach(function(project) {
                        $('#filter-project').append('<option value="' + project.id + '">' +
                            project.project_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching project options:', error);
                }
            });

            $.ajax({
                url: '{{ route('get-users') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(user) {
                        $('#filter-created-by').append('<option value="' + user.id + '">' + user
                            .full_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching created by options:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-users') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(user) {
                        $('#filter-assignee').append('<option value="' + user.id + '">' + user
                            .full_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching assignees:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-departments') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(department) {
                        $('#filter-department').append('<option value="' + department.id +
                            '">' + department.department_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching department options:', error);
                }
            });

        });


        $(document).on("click", ".confirm-delete", function(e) {
            e.preventDefault();
            var id = $(this).data("idos");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '/app/task/destroy/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Your file has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary file is safe :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });

        $(document).on("click", ".accept-task", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, accept it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = "{{ route('app-task-accept', ':id') }}".replace(':id', id);
                    Swal.fire({
                        icon: 'success',
                        title: 'Accepted!',
                        text: 'Your task has been accepted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary file is safe :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });

        $(document).on("click", ".confirm-retrieve", function(e) {
            e.preventDefault();
            var id = $(this).data("idos");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, retrieve it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '/app/task/retrive/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'retrieved!',
                        text: 'Your file has been retrieved.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary file is deleted :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });
    </script>
    {{-- Page js files --}}
@endsection

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>

@yield('js_load')

<script>
    function deleteConfirm() {
        if (confirm("Are you sure to delete data?")) {
            return true;
        }
        return false;
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).on('click', '.reject-btn', function() {
        var taskId = $(this).data('id');
        var formAction = '{{ route('app-task-reject', ':taskId') }}';
        formAction = formAction.replace(':taskId', taskId);
        $('#rejectForm').attr('action', formAction);
    });
</script>
