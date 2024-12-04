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
                    <table class="rejected-list-table table dt-responsive" id="rejected-items-table">
                        <thead>
                            <tr>
                                <th>Task Id</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>
                                    created by
                                </th>
                                <th>Submitted By</th>
                                <th>Rejection Reason</th>
                                <th>Rejection Date</th>
                                <th>Status</th>
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
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'assignee_name',
                        name: 'assignee_name'
                    },
                    {
                        data: 'rejection_reason',
                        name: 'rejection_reason'
                    },
                    {
                        data: 'assignee_updated_at',
                        name: 'assignee_updated_at'
                    },
                    {
                        data: 'assignee_status',
                        name: 'assignee_status',
                        render: function(data) {
                            if (data === 'Rejected') {
                                return '<span class="badge bg-danger">Rejected</span>';
                            } else {
                                return '<span class="badge bg-danger">Rejected</span>';
                            }
                        }
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
