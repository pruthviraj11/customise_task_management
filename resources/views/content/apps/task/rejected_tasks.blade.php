@extends('layouts/contentLayoutMaster')

@section('title', 'Rejected Tasks List')

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
    <!-- rejected items list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-rejected-list">

        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Rejected Taks List</h4>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <table class="rejected-list-table table" id="rejected-items-table">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Pin Task</th>
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
    </section>
    <!-- rejected items list ends -->
    @php
    $selectedColumns = json_decode(auth()->user()->selected_fields, true);

    if (empty($selectedColumns)) {
$selectedColumns = ["0", "3", "4", "5", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22"];
}

@endphp
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
    @yield('links')
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

            var type = @json($type);
            var selectedColumns = @json($selectedColumns);
            console.log(selectedColumns);


            $('#rejected-items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('rejected-tasks') }}", // Adjust this route as necessary
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Excel',
                    title: '',
                    filename: 'Rejected_Items',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [1, 2, 3, 4]
                    }
                }],
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        visible: selectedColumns.includes("0")
                    },
                    {
                        data: 'pin_task', // Pin Task column
                        name: 'pin_task',
                        searchable: false,
                        visible: {{ $type == 'mytask' ? 'true' : 'false' }},
                        render: function(data, type, row) {
                            // Check if the task is pinned and pinned by the current user
                            if (row.is_pinned) {
                                return `
                <i class="ficon pin-task-icon" data-feather="paperclip"
                   style="cursor: pointer; color: red"
                   title="Pin Task"
                   data-task-id="${row.task_number}">
                </i>
            `;
                            } else {
                                return `
                <i class="ficon pin-task-icon" data-feather="paperclip"
                   style="cursor: pointer;"
                   title="Pin Task"
                   data-task-id="${row.task_number}">
                </i>
            `;
                            }
                        }
                    },
                    {
                        data: 'task_id',
                        name: 'task_id',
                        searchable: true,
                        visible: false
                    },
                    {
                        data: 'Task_number',
                        name: 'Task_number',
                        searchable: true,
                        visible: selectedColumns.includes("3")

                    },
                    {
                        data: 'Task_Ticket',
                        name: 'Task_Ticket',
                        searchable: true,
                        visible: selectedColumns.includes("4")

                    },
                    {
                        data: 'title',
                        name: 'title',
                        searchable: true,
                        visible: selectedColumns.includes("5")
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
                        searchable: true,
                        visible: selectedColumns.includes("7")
                    },
                    {
                        data: 'created_by_username',
                        name: 'created_by_username',
                        searchable: true,
                        visible: selectedColumns.includes("8")
                    },
                    {
                        data: 'Task_assign_to',
                        name: 'Task_assign_to',
                        searchable: true,
                        visible: selectedColumns.includes("9")
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true,
                        visible: selectedColumns.includes("10")
                    },
                    {
                        data: 'Created_Date',
                        name: 'Created_Date',
                        searchable: true,
                        visible: selectedColumns.includes("11")

                    }, {
                        data: 'start_date',
                        name: 'start_date',
                        searchable: true,
                        visible: selectedColumns.includes("12")

                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                        searchable: true,
                        visible: selectedColumns.includes("13")

                    },
                    {
                        data: 'completed_date',
                        name: 'completed_date',
                        searchable: true,
                        visible: selectedColumns.includes("14")
                    },

                    {
                        data: 'accepted_date',
                        name: 'accepted_date',
                        searchable: true,
                        visible: selectedColumns.includes("15")
                    },
                    {
                        data: 'project',
                        name: 'project',
                        searchable: true,
                        visible: selectedColumns.includes("16")
                    },
                    {
                        data: 'department',
                        name: 'department',
                        searchable: true,
                        visible: selectedColumns.includes("17")
                    },
                    {
                        data: 'sub_department',
                        name: 'sub_department',
                        searchable: true,
                        visible: selectedColumns.includes("18")
                    },
                    {
                        data: 'creator_department',
                        name: 'creator_department',
                        searchable: true,
                        visible: selectedColumns.includes("19")
                    },
                    {
                        data: 'creator_sub_department',
                        name: 'creator_sub_department',
                        searchable: true,
                        visible: selectedColumns.includes("20")
                    },
                    {
                        data: 'creator_phone',
                        name: 'creator_phone',
                        searchable: true,
                        visible: selectedColumns.includes("21")
                    },
                    {
                        data: 'close_date',
                        name: 'close_date',
                        searchable: true,
                        visible: selectedColumns.includes("22")
                    },
                    @if ($type == 'mytask')

                        {
                            data: 'is_pinned',
                            name: 'is_pinned',
                            visible: false,
                            searchable: false,
                        },
                    @endif

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
                    window.location.href = '/app/rejected/destroy/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Your item has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your item is safe :)',
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
