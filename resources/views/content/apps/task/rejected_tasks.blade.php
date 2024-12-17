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
                                <th>Task Number</th>
                                <th>Task/Ticket</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Subject</th>
                                <th>Assingn By</th>
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
                        searchable: false
                    }, // Non-searchable column
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
                        visible:false,
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
                        searchable: true
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
                        searchable: true
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
