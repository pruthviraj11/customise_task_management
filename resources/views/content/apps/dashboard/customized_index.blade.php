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
@endsection
@section('content')

    <section class="app-user-list">
        <div class="row">


            @if (session('success'))
                <p>{{ session('success') }}</p>
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
                </table>
            </div>



            <!---- Requested to us  ---->
            <div class="card-header">
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
                </table>
            </div>







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
                            name: 'requested_to_us'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index <= 3)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'pending_tasks',
                            name: 'pending_tasks'
                        },
                        {
                            data: 'over_dues',
                            name: 'over_dues'
                        },
                        {
                            data: 'today_dues',
                            name: 'today_dues'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 4)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks'
                        },
                        {
                            data: 'total',
                            name: 'total'
                        },

                    ]
                });
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
                            name: 'requested_by_us'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index <= 3)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'pending_tasks',
                            name: 'pending_tasks'
                        },
                        {
                            data: 'over_dues',
                            name: 'over_dues'
                        },
                        {
                            data: 'today_dues',
                            name: 'today_dues'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 4)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks'
                        },
                        {
                            data: 'total',
                            name: 'total'
                        },

                    ]
                });
            });





            /*--------   Total Task Status ---------*/


            $(document).ready(function() {
                var usersTable = $('#total_task_status').DataTable({
                    processing: true,
                    serverSide: false,
                    // ajax: '{{ route('users.task.status.hierarchy') }}', // Ensure this route returns user task status data
                    ajax: '{{ route('users.total_task_details') }}',
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
                            data: 'total_tasks',
                            name: 'total_tasks'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index <= 3)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'pending_tasks',
                            name: 'pending_tasks'
                        },
                        {
                            data: 'over_dues',
                            name: 'over_dues'
                        },
                        {
                            data: 'today_dues',
                            name: 'today_dues'
                        },

                        @foreach ($statusinfos as $index => $status)
                            @if ($index >= 4)
                                {
                                    data: '{{ \Str::slug($status->status_name, '_') }}',
                                    name: '{{ \Str::slug($status->status_name, '_') }}'
                                },
                            @endif
                        @endforeach

                        {
                            data: 'finish_tasks',
                            name: 'finish_tasks'
                        },
                        {
                            data: 'total',
                            name: 'total'
                        },

                    ]
                });
            });





        });
    </script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script>





@endsection