@extends('layouts/contentLayoutMaster')

@section('title', 'Sub Departments List')

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
    <!-- sub_departments list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-sub_department-list">

        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Sub Departments list</h4>
                <a href="{{ route('app-sub_department-add') }}" class="col-md-2 btn btn-primary">Add Sub Department
                </a>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <table class="user-list-table table dt-responsive" id="sub_departments-table">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Sub Department Name</th>
                                <th> Department </th>
                                <th>Description</th>
                                <th>Status</th>
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
            $('#sub_departments-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'lBfrtip',
               buttons: [{
            extend: 'excel',
            text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
            action: newexportaction, // Custom export function
            title: '',
            filename: 'Department',
            className: 'btn btn-success btn-sm',
            exportOptions: {
                modifier: {
                    length: -1 // Export all data, not just the current page
                },
                columns: [1, 2, 3, 4] // Export these columns
            }
        }],
                ajax: "{{ route('app-sub_department-get-all') }}",
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sub_department_name',
                        name: 'sub_department_name',
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },

                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {

                            if (data === 'on') {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-danger">Inactive</span>';
                            }
                        }
                    },

                ],
                drawCallback: function() {
                    feather.replace();
                }

            });
             function newexportaction(e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;

        dt.one('preXhr', function(e, settings, data) {
            // Fetch all data from the server
            data.start = 0;
            data.length = 2147483647; // Max integer value to fetch all records

            dt.one('preDraw', function(e, settings) {
                // Call the original action based on the button clicked
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)
                        ? $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config)
                        : $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config)
                        ? $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config)
                        : $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)
                        ? $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config)
                        : $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }

                // After exporting, reset the pagination
                dt.one('preXhr', function(e, settings, data) {
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                // Re-render the DataTable after the export
                setTimeout(dt.ajax.reload, 0);

                return false; // Prevent immediate rendering of full data
            });
        });

        // Trigger the export
        dt.ajax.reload();
    }
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
                    window.location.href = '/app/sub_department/destroy/' + id;
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
