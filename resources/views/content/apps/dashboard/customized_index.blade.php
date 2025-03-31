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
        .requested-to-us{
            background-color: hsla(38, 92%, 64%, 0.615) !important;
            text-align: end;
            font-weight: 900;
        }
        .pending-tasks {
            background-color: hsl(219, 67%, 72%) !important;
            text-align: end;
            font-weight: 900;

        }
        .over-dues{
            background-color: hsla(5, 89%, 50%, 0.643) !important;
            text-align: end;
            font-weight: 900;
        }
        .today-dues{
            background-color: hsla(35, 66%, 77%, 0.643) !important;
            text-align: end;
            font-weight: 900;
        }
        .finish-tasks{
            background-color: hsla(130, 62%, 63%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }
        .total{
            background-color: hsla(130, 66%, 25%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }
        .rejected_tasks{
            background-color: hsla(130, 66%, 25%, 0.62) !important;
            text-align: end;
            font-weight: 900;
        }
        .all_status_columns{
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
                        </div>
                    </div>
                </div>
            @endif

        </div>
        @if (auth()->user()->id != 1)



            <!---- Requested to us  ---->
            <div class="card-header">
                <h1>Requested To Us Tasks Lists
                </h1>
                <table id="requested_to_me" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Requested To Us</th>

                            @foreach ($statusinfos as $index => $status)
                                @if ($index <= 4 && $index !=2)
                                    <th>{{ $status->status_name }} </th>
                                @endif
                            @endforeach

                            <th>Pending Tasks</th>
                            <th>Over Due</th>
                            <th>Today's Due</th>
                            @foreach ($statusinfos as $index => $status)
                            @if ($index ==2)
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
            </div>



            <!---- Requested By Us  ----->
            <div class="card-header">
                <h1>Requested By Us Tasks Lists
                </h1>
                <table id="requested_by_me" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Requested By Us</th>



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







        @endif

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
                        className: 'btn btn-success btn-sm'
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
                            @if ($index <= 4 && $index !=2)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    class:'all_status_columns',
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
                            class:'over-dues',
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
                            class:'today-dues',
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
                                    class:'all_status_columns',
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
                                    class:'all_status_columns',
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
                        },{
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
                        className: 'btn btn-success btn-sm'
                    }],
                    columns: [{
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'requested_by_us',
                            name: 'requested_by_us',
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
                            @if ($index <= 3)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
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
                            render: function(data, type, row) {
                                let userId = row.user_id;
                                let statusId =
                                    '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId
                                let url =
                                    '{{ route('tasks.requested_to_us_today_due', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                url = url.replace(':user_id', userId).replace(
                                    ':status_id', statusId);
                                return `<a href="${url}" class="text-primary">${data}</a>`;
                            }
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 4)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}',
                                    render: function(data, type, row) {
                                        let userId = row.user_id;
                                        let statusId =
                                            '{{ \Str::slug($status->id, '_') }}'; // Dynamically set the statusId

                                        let url =
                                            '{{ route('tasks.requested_by_us_status', ['user_id' => ':user_id', 'status_id' => ':status_id', 'type' => 'requested_by_me']) }}';
                                        url = url.replace(':user_id', userId).replace(
                                            ':status_id',
                                            statusId);

                                        return `<a href="${url}" class="text-primary">${data}</a>`;
                                    }
                                },
                            @endif
                        @endforeach

                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks',
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
