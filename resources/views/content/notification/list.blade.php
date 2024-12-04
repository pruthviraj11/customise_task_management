@extends('layouts/contentLayoutMaster')

@section('title', $type)

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css')) }}">
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
                <h4 class="card-title text-capitalize">{{ $type }} List</h4>
                <button data-bs-toggle="tooltip" title="Mark all as read" data-bs-delay="400" class="btn btn-primary"
                    id="mark-all-as-read">
                    Mark all as Read</button>
            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">
                    <div class="container">
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="select_list">Select Type</label>
                                <select class="form-select select2" name="select_type" id="notification_type">
                                    <option value="unread">Unread / Unseen</option>
                                    <option value="read">Read / Seen</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table class="user-list-table table dt-responsive notification-table" id="notification-table">
                        <thead>
                            <tr>
                                <th>Action By</th>
                                <th>Action Type</th>
                                <th>Message</th>
                                <th>Notification Date</th>
                                <th>Actions</th>
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

    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            function loadData() {
                var dataTable = $('#notification-table').DataTable();
                // Check if a DataTable instance already exists
                if ($.fn.DataTable.isDataTable('#notification-table')) {
                    dataTable.destroy();
                }
                $('#notification-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('notifications-get-all') }}',
                        method: 'GET',
                        data: {
                            notification_type: $('#notification_type').val(),
                        }
                    },
                    columns: [
                        // {data: 'inquiry_detail', name: 'inquiry_detail'},
                        {
                            data: 'notification_action_from',
                            name: 'notification_action_from'
                        },
                        {
                            data: 'notification_type',
                            name: 'notification_type'
                        },
                        {
                            data: 'message',
                            name: 'message'
                        },
                        {
                            data: 'notification_date',
                            name: 'notification_date'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    drawCallback: function() {
                        feather.replace();
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });
            }

            loadData();


            $(document).on('change', '#notification_type', function(event) {
                event.preventDefault();
                loadData();
            });

            $(document).on('click', '#mark-all-as-read', function(event) {
                event.preventDefault();
                $.ajax({
                    url: '{{ route('app-notifications-read') }}',
                    method: 'GET',
                    success: function(response) {
                        toastr['success'](`${response.message}`, `Success`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    },
                    error: function(response) {
                        toastr['error'](`${response.message}`, `Error`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    }
                });
                loadData();
            });

            $(document).on('click', '#mark-as-read', function() {

                var notifiactionId = $(this).data('internal-notification-id');
                console.log(notifiactionId);
                $.ajax({
                    url: '{{ route('app-notifications-read', '') }}/' + notifiactionId,
                    method: 'GET',
                    success: function(response) {
                        toastr['success'](`${response.message}`, `Success`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    },
                    error: function(response) {
                        toastr['error'](`${response.message}`, `Error`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    }
                });
                loadData();
            });
        });
    </script>

    {{-- Page js files --}}
@endsection
