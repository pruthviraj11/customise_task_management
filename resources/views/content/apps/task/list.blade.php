@extends('layouts/contentLayoutMaster')

@section('title', 'Task List')

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
    <!-- departments list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-task-list">

        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Task list</h4>
                <div class="d-flex flex-row col-auto gap-1">
                    <a href="{{ route('app-task-add') }}" class=" btn btn-primary">Add Task
                    </a>
                    <a href="{{ route('app-task-cardView') }}" class="btn btn-primary">Card View
                    </a>
                </div>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <table class="user-list-table table " id="tasks-table">
                        <thead>
                            <tr>

                                <th>Actions</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Subject</th>
                                <th>Assingn By</th>
                                <th>Task assign to</th>
                                <th>Created Date</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th>Accepted task Date</th>
                                <th>Status</th>
                                <th>Project</th>
                                <th>Department</th>
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

            var type = @json($type);
            var ajaxUrl;
            if (type === 'accepted') {
                ajaxUrl = "{{ route('app-task-get-accepted') }}";
            } else if (type === 'requested') {
                ajaxUrl = "{{ route('app-task-get-requested') }}";
            } else if (type === 'list') {
                ajaxUrl = "{{ route('app-task-get-all') }}";
            } else if (type == 'mytask') {
                ajaxUrl = "{{ route('app-task-mytask-get') }}";
            } else if (type == 'accepted_by_me') {
                ajaxUrl = "{{ route('app-task-getAll_accepted_by_me-get') }}";
            } else if (type == 'assign_by_me') {
                ajaxUrl = "{{ route('app-task-getAll_assign_by_me-get') }}";
            } else if (type == 'requested_me') {
                ajaxUrl = "{{ route('app-task-getAll_requested_me-get') }}";
            } else if (type == 'conceptualization') {
                ajaxUrl = "{{ route('app-task-getAll_conceptualization-get') }}";
            } else if (type == 'due_date_past') {
                ajaxUrl = "{{ route('app-task-getAll_due_date_past-get') }}";
            } else if (type == 'scope_defined') {
                ajaxUrl = "{{ route('app-task-getAll_scope_defined-get') }}";
            } else if (type == 'completed') {
                ajaxUrl = "{{ route('app-task-getAll_completed-get') }}";
            } else if (type == 'hold') {
                ajaxUrl = "{{ route('app-task-getAll_hold-get') }}";
            } else if (type == 'in_execution') {
                ajaxUrl = "{{ route('app-task-getAll_in_execution-get') }}";
            } else if (type == 'admin_acc') {
                ajaxUrl = "{{ route('app-task-getAll_admin_acc-get') }}";
            } else if (type == 'admin_req') {
                ajaxUrl = "{{ route('app-task-getAll_admin_req-get') }}";
            }else if (type == 'admin_rej') {
                ajaxUrl = "{{ route('app-task-getAll_admin_rej-get') }}";
            } else if (type == 'total_task') {
                ajaxUrl = "{{ route('app-task-getAll_total_task-get') }}";
            }
            $('#tasks-table').DataTable({
                dom: '<"export-buttons"B>lfrtip',
                processing: true,
                serverSide: true,
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Excel',
                    action: newexportaction,
                    title: '',
                    filename: 'Task',
                    className: 'btn btn-primary btn-sm',
                    exportOptions: {
                        modifier: {
                            length: -1
                        },
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                    }
                }, ],
                ajax: ajaxUrl,
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: false,
                        export: true,
                    },
                    {
                        data: 'subject',
                        name: 'subject',
                    },

                    {
                        data: 'created_by_username',
                        name: 'created_by_username',
                    },

                    {
                        data: 'task_Assign',
                        name: 'task_Assign',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            // Check if the data is valid
                            if (data) {
                                // Parse the date string to a Date object
                                var date = new Date(data);
                                // Format the date to dd-mm-yyyy
                                var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' +
                                    (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();
                                return formattedDate;
                            }
                            return '';
                        }
                    },

                    {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data, type, row) {
                            // Check if the data is valid
                            if (data) {
                                // Parse the date string to a Date object
                                var date = new Date(data);
                                // Format the date to dd-mm-yyyy
                                var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' +
                                    (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();
                                return formattedDate;
                            }
                            return '';
                        }
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                        render: function(data, type, row) {
                            // Check if the data is valid
                            if (data) {
                                // Parse the date string to a Date object
                                var date = new Date(data);
                                // Format the date to dd-mm-yyyy
                                var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' +
                                    (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();
                                return formattedDate;
                            }
                            return '';
                        }
                    },
                    {
                        data: 'accepted_date',
                        name: 'accepted_date',
                        render: function(data, type, row) {
                            // Check if the data is valid
                            if (data) {
                                // Parse the date string to a Date object
                                var date = new Date(data);
                                // Format the date to dd-mm-yyyy
                                var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' +
                                    (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();
                                return formattedDate;
                            }
                            return '';
                        }
                    },
                    {
                        data: 'task_status_name',
                        name: 'task_status_name',
                    },
                    {
                        data: 'project_name',
                        name: 'project_name',
                    },
                    {
                        data: 'department_name',
                        name: 'department_name',
                    },

                ],
                drawCallback: function() {
                    feather.replace();
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
