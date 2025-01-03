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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-dropdown" class="fw-bold">Filter Tasks</label>
                        <select id="filter-dropdown" class="form-select select2">
                            <option value="rejected_my_task">Rejected My Task</option>
                            <option value="rejected_by_me">Rejected by Me</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <table class="rejected-list-table table dt-responsive" id="rejected-items-table">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Task</th>
                                <th>Task Number</th>
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
            $('#filter-dropdown').select2();

            var filterValue = $('#filter-dropdown').val(); // Correctly get the default filter value

            var table = $('#rejected-items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rejected-tasks') }}", // Update route to handle filtering
                    data: function(d) {
                        d.filter = filterValue; // Dynamically pass the filter value
                    }
                },
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Excel',
                    title: '',
                    filename: 'Rejected_Items',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8]
                    }
                }],
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
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

                    },

                    {
                        data: 'title',
                        name: 'title',
                        searchable: true,
                    },
                    {
                        data: 'description',
                        name: 'description',
                        searchable: true,
                    },

                    {
                        data: 'created_by_username',
                        name: 'created_by_username',
                        searchable: true,
                    },
                    {
                        data: 'Task_assign_to',
                        name: 'Task_assign_to',
                        searchable: true,
                    },
                    {
                        data: 'remark',
                        name: 'remark',
                        searchable: true,

                    },
                    {
                        data: 'rejected_date',
                        name: 'rejected_date',
                        searchable: true,
                        render: function(data, type, row) {
                            // Check if data exists and is a valid date
                            if (data) {
                                // Convert to Date object if it's a valid date string
                                let date = new Date(data);

                                // Check if it's a valid date object
                                if (!isNaN(date)) {
                                    // Format as dd/mm/yyyy
                                    let day = String(date.getDate()).padStart(2, '0');
                                    let month = String(date.getMonth() + 1).padStart(2,
                                        '0'); // months are 0-indexed
                                    let year = date.getFullYear();

                                    return `${day}/${month}/${year}`;
                                }
                            }
                            // Return default value if no valid date
                            return '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: true,
                    },

                ],

                drawCallback: function() {
                    feather.replace();
                }
            });
            // Attach event listener to the filter dropdown
            $('#filter-dropdown').change(function() {
                filterValue = $(this).val(); // Get the selected value from the dropdown
                table.ajax.reload(); // Reload the DataTable with the new filter
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
