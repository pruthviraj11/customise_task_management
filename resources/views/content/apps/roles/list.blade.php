@extends('layouts/contentLayoutMaster')

@section('title', 'Role')

@section('vendor-style')
    {{-- Page Css files --}}
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection

@section('content')
    <!-- users list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-user-list">
        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Roles List</h4>
                <a href="{{ route('app-roles-add') }}" class="col-md-2 btn btn-primary">Add Role</a>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <table class="user-list-table table dt-responsive" id="role-table">
                        <thead>
                            <tr>

                                <th>Actions</th>
                                <th>Name</th>
                                <th>Display Name</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal to add new user starts-->
        <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
            <div class="modal-dialog">
                <form class="add-new-user modal-content pt-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                    <div class="modal-header mb-1">
                        <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    </div>
                    <div class="modal-body flex-grow-1">
                        <div class="mb-1">
                            <label class="form-label" for="basic-icon-default-fullname">Full Name</label>
                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname"
                                placeholder="John Doe" name="user-fullname" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-icon-default-uname">Username</label>
                            <input type="text" id="basic-icon-default-uname" class="form-control dt-uname"
                                placeholder="Web Developer" name="user-name" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-icon-default-email">Email</label>
                            <input type="text" id="basic-icon-default-email" class="form-control dt-email"
                                placeholder="john.doe@example.com" name="user-email" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-icon-default-contact">Contact</label>
                            <input type="text" id="basic-icon-default-contact" class="form-control dt-contact"
                                placeholder="+1 (609) 933-44-22" name="user-contact" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="country-floating">Country</label>
                            <input type="text" id="country-floating" class="form-control" name="country"
                                placeholder="Country" />
                        </div>
                        <button type="submit" class="btn btn-primary me-1 data-submit">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal to add new user Ends-->
        </div>
        <!-- list and filter end -->
    </section>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}


@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('#role-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('app-roles-get-all') }}",
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Excel',
                    title: '',
                    filename: 'Project Status',
                    className: 'btn btn-success  btn-sm',
                    exportOptions: {
                        columns: [1, 2]
                    }
                }, ],
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'display_name',
                        name: 'display_name',
                        searchable: true
                    },

                ],
                drawCallback: function() {
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
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
                    window.location.href = '/app/roles/destroy/' + id;
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
