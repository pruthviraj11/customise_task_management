@extends('layouts/contentLayoutMaster')

@section('title', 'Operations')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-tree.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice-list.css')) }}">
@endsection

@section('content')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <div class="row match-height">
        </div>
        <!-- List DataTable -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dt_adv_search">
                            <div class="row g-1 mb-md-1">
                                <div class="col-md-4">
                                    <label class="form-label" for="branches_list">Branches</label>
                                    <select id="branches_list" class="select2 form-select"
                                            name="branch_id"
                                            required>
                                        <option val="">Select</option>
                                        @if(!empty($branches) && $branches->count() > 0)
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                    ({{ $branch->branch_code }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Request Date:</label>
                                    <div class="mb-0">
                                        <input
                                            type="text"
                                            class="form-control dt-date flatpickr-range dt-input"
                                            data-column="5"
                                            placeholder="StartDate to EndDate"
                                            data-column-index="4"
                                            name="dt_date"
                                        />
                                        <input
                                            type="hidden"
                                            class="form-control dt-date start_date dt-input"
                                            data-column="5"
                                            data-column-index="4"
                                            name="value_from_start_date"
                                        />
                                        <input
                                            type="hidden"
                                            class="form-control dt-date end_date dt-input"
                                            name="value_from_end_date"
                                            data-column="5"
                                            data-column-index="4"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        {{-- <a href="{{ route('app-inquiries-add') }}" class="btn btn-primary">Add Inquiry</a> --}}
                    </div>
                    <div class="card-body border-bottom">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="user-list-table table dt-responsive inquiry-table" id="cancellation-table-count">
                                <thead>
                                    <tr>
                                        <th>Agent</th>
                                        <th>Branch</th>
                                        <th>Inquires</th>
                                        @if(!empty(getInquiryStatus()))
                                            @foreach(getInquiryStatus() as $key => $value)
                                                <th>{{ $value->name ?? '' }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ List DataTable -->
    </section>
    <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/pages/app-invoice-list.js')) }}"></script>
    <script>
        $(document).ready(function() {

            var separator = ' - ',
                rangePickr = $('.flatpickr-range'),
                dateFormat = 'DD/MM/YYYY';
            var options = {
                autoUpdateInput: false,
                autoApply: true,
                locale: {
                    format: dateFormat,
                    separator: separator
                },
                opens: $('html').attr('data-textdirection') === 'rtl' ? 'left' : 'right'
            };

            //
            if (rangePickr.length) {
                rangePickr.flatpickr({
                    mode: 'range',
                    dateFormat: 'd/m/Y',
                    onClose: function (selectedDates, dateStr, instance) {
                        var startDate = '',
                            endDate = new Date();
                        if (selectedDates[0] != undefined) {
                            startDate =
                                selectedDates[0].getMonth() + 1 + '/' + selectedDates[0].getDate() + '/' + selectedDates[0].getFullYear();
                            $('.start_date').val(startDate);
                        }
                        if (selectedDates[1] != undefined) {
                            endDate =
                                selectedDates[1].getMonth() + 1 + '/' + selectedDates[1].getDate() + '/' + selectedDates[1].getFullYear();
                            $('.end_date').val(endDate);
                        }
                        $(rangePickr).trigger('change').trigger('keyup');
                    }
                });
            }
            /*------------------------------------------
            --------------------------------------------
            Cancellation 
            --------------------------------------------
            --------------------------------------------*/
            var table = $('#cancellation-table-count').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                  url: "{{ route('get-all-operation-list') }}",
                  data: function (d) {
                        d.branch_id = $('#branches_list').find(":selected").val(),
                        d.start_date = $('[name="value_from_start_date"]').val(),
                        d.end_date = $('[name="value_from_end_date"]').val()
                    }
                },
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'branch_id',
                        name: 'branch_id'
                    },
                    {
                        data: 'inquiry_id',
                        name: 'inquiry_id'
                    },
                    {
                        data: 'workshop_1',
                        name: 'workshop_1'
                    },
                    {
                        data: 'workshop_2',
                        name: 'workshop_2'
                    },
                    {
                        data: 'workshop_3',
                        name: 'workshop_3'
                    },
                    {
                        data: 'workshop_4',
                        name: 'workshop_4'
                    },
                    {
                        data: 'workshop_5',
                        name: 'workshop_5'
                    },
                    {
                        data: 'cv_sheet',
                        name: 'cv_sheet'
                    },
                    {
                        data: 'cv_confirmation',
                        name: 'cv_confirmation'
                    },
                    {
                        data: 'spread_sheet',
                        name: 'spread_sheet'
                    },
                    {
                        data: 'noc_pro',
                        name: 'noc_pro'
                    },
                    {
                        data: 'js_case_sheet',
                        name: 'js_case_sheet'
                    },
                    {
                        data: 'application',
                        name: 'application'
                    },
                    {
                        data: 'submission_progress',
                        name: 'submission_progress'
                    },
                    {
                        data: 'submitted',
                        name: 'submitted'
                    },
                    {
                        data: 'service_completed',
                        name: 'service_completed'
                    },
                    {
                        data: 'hold_payment',
                        name: 'hold_payment'
                    },
                    {
                        data: 'hold_payment_client',
                        name: 'hold_payment_client'
                    },
                    {
                        data: 'in_complaint',
                        name: 'in_complaint'
                    },
                    {
                        data: 'cancelled_with_refund',
                        name: 'cancelled_with_refund'
                    },
                    {
                        data: 'cancelled_without_refund',
                        name: 'cancelled_without_refund'
                    },
                    {
                        data: 'closure',
                        name: 'closure'
                    }
                ], drawCallback: function() {
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            $("#branches_list").change(function(){
                table.draw();
            });

            $(".flatpickr-range").change(function(){
                table.draw();
            });
        });
    </script>
@endsection
