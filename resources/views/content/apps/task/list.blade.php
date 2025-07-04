@extends('layouts/contentLayoutMaster')

@section('title', 'Task List')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <!-- DataTables and Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>


@endsection

<style>
    .table-responsive {
        position: relative;
        height: 600px;
        /* Set a specific height for the scrollable area */
        overflow-y: auto;
        /* Allow vertical scrolling */
        margin-top: 20px;
    }

    #tasks-table thead th {
        position: sticky;
        top: 0;
        /* Ensure the same background as the table header */
        z-index: 10;
    }

    #tasks-table th,
    #tasks-table td {
        padding: 10px;
        text-align: center;
    }

    #starRating .star {
        font-size: 24px;
        color: #ccc;
        cursor: pointer;
    }

    #starRating .star.selected {
        color: #ffc107;
    }
</style>
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection


@section('content')
    <!-- departments list start -->
    @if (session('status'))
        <h6 class="alert alert-warning">{{ session('status') }}</h6>
    @endif
    <section class="app-task-list">

        <!-- list and filter start -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Task list</h4>

            </div>
            <div class="card-body border-bottom">
                <div class="card-datatable table-responsive pt-0">

                    @if ($type !== 'deleted' && $type !== 'dynamic_report')
                        <!-- Filter Inputs -->
                        {{-- <input type="text" id="filter-title" placeholder="Filter by Title"> --}}
                        <div class="row mb-2">
                            <div class="mb-1 col-3">
                                <label class="form-label" for="filter-status">Task Or Ticket</label>
                                <select id="task" class="form-select select2">
                                    <option value="">ALL</option>
                                    <option value="0">Task</option>
                                    <option value="1">Ticket</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-status">Filter by Status
                                    By</label>
                                <select class="  form-select select2" id="filter-status">
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-project">Filter by Project</label>
                                <select class="form-select select2" id="filter-project">
                                    <option value="">ALL</option>
                                </select>
                            </div>


                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-created-by">Filter by Created
                                    By</label>
                                <select id="filter-created-by" class="form-select select2">
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3">
                                <label class="form-label" for="filter-assignee">Filter by Task Assign To</label>
                                <select id="filter-assignee" class="form-select select2" multiple>
                                    <option value="">ALL</option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="filter-department">Filter by department</label>
                                <select id="filter-department"class=" form-select select2">
                                    <option value="">ALL </option>
                                </select>
                            </div>
                            <div class="mb-1 col-3 ">
                                <label class="form-label" for="dt_date">Start Date
                                    By</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input"
                                    data-mb-1column="5" id="dt_date" placeholder="StartDate to EndDate"
                                    data-mb-1column-index="4" name="dt_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_end_date" data-column="5" data-column-index="4" />
                            </div>
                            <div class="mb-1  col-3">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input" data-column="5"
                                    id="end_date" placeholder="StartDate to EndDate" data-column-index="4"
                                    name="end_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_end_date" data-column="5" data-column-index="4" />
                            </div>

                            <div class="mb-1 col-3">
                                <label class="form-label" for="accepted_task_date">Accepted Task Date</label>
                                <input type="text" class="form-control dt-date flatpickr-range dt-input"
                                    data-column="5" id="accepted_task_date" placeholder="Start Date to End Date"
                                    data-column-index="4" name="accepted_task_date" />
                                <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                    data-column-index="4" name="value_from_accepted_task_start_date" />
                                <input type="hidden" class="form-control dt-date end_date dt-input"
                                    name="value_from_accepted_task_end_date" data-column="5" data-column-index="4" />
                            </div>
                            @if ($type !== 'recurring_main')
                                <div class="mb-1 col-3">
                                    <label class="form-label" for="task_type">Task Type</label>
                                    <select class="form-control form-select select2 dt-input" id="task_type"
                                        name="task_type" data-column="6">
                                        <option value="">Select Task Type</option>
                                        <option value="0">Normal Tasks</option>
                                        <option value="1">Recurring Tasks</option>
                                    </select>
                                </div>
                            @endif
                            {{-- <input type="date" id="filter-start-date" class="col  form-control"
                                placeholder="Filter by Start Date"> --}}
                            <div class="col-md-12 text-end">
                                <button id="apply-filters" class="mt-2 col btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-row mb-1 col-auto gap-1">
                        <a href="{{ route('app-task-add') }}" class=" justify-content-center btn btn-warning"> <i
                                data-feather="plus-circle" class="font-medium-4"></i> Add Task
                        </a>

                        @if ($type != 'requested' && $type != 'my_and_team' && $type != 'deleted')
                            <a href="{{ route('app-task-cardView', $type) }}" class="btn btn-info">Card View</a>
                        @endif
                        {{-- <a href="{{ route('export-tasks') }}" class="btn btn-success">Export</a> --}}
                        @if (auth()->user()->hasRole('Super Admin') && $type == 'list')
                            <a href="{{ route('export-total-tasks') }}" class="btn btn-success">Export</a>
                        @endif
                    </div>


                    <table class="user-list-table table table-responsive" id="tasks-table">
                        <thead>
                            <tr class="">

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
                                <th>Task Status</th>
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
                                <th>Owner Contactinfo</th>
                                <th>Close Date</th>
                                @if ($type == 'mytask')
                                    <th>Is Pinned</th>
                                @endif


                                <th>Status</th>

                                @if ($type == 'assign_by_me')
                                    <th>Rating</th>
                                    <th>Feedback</th>
                                @endif
                                {{-- <th>Is Pinned</th> --}}

                                {{-- Fields For Excel Starts --}}
                                <th>Created Date</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th>Completed Date</th>
                                <th>Accepted task Date</th>
                                <th>Close Date</th>
                                {{-- Fields For Excel Ends --}}
                                <th>Assign To Status</th>
                                <th>Assign To ReportIng</th>



                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        <!-- list and filter end -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="rejectForm" action="" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Remark</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">


                            <div class="form-group">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea name="remark" class="form-control" id="message-text"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    {{-- reassign --}}


    {{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const allUsers = @json($reassign_users);

        document.querySelectorAll('.open-reassign-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const encryptedTaskId = this.getAttribute('data-id');
                const currentUserId = this.getAttribute('data-user-id');
                const dropdown = document.getElementById('assignTo');
                const hiddenInput = document.getElementById('modalTaskId');

                hiddenInput.value = encryptedTaskId;

                // Clear and populate dropdown
                dropdown.innerHTML = '<option value="">Select User</option>';

                allUsers.forEach(user => {
                    if (user.id != currentUserId) {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.first_name;
                        dropdown.appendChild(option);
                    }
                });
            });
        });
    });
</script> --}}


    <!-- Reassign Modal -->
    <div class="modal fade" id="reassignModal" tabindex="-1" aria-labelledby="reassignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="reassignForm" method="POST" action="{{ route('app-task-reassign') }}">
                @csrf
                <input type="hidden" name="task_id" id="modalTaskId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reassignModalLabel">Reassign Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="assignTo" class="form-label">Assign To</label>
                            <select class="form-select" name="assign_to" id="assignTo" required>
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Reassign</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <form id="feedbackForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="feedbackModalLabel">Submit Feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- <div class="modal-body">
                        <input type="hidden" name="task_id" id="feedbackTaskId">



                        <div class="mb-3">
                            <label class="form-label">Feedback Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input feedback-type-radio" type="radio" name="feedback_type"
                                    id="positiveFeedback" value="positive" checked>
                                <label class="form-check-label" for="positiveFeedback">Positive</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input feedback-type-radio" type="radio" name="feedback_type"
                                    id="negativeFeedback" value="negative">
                                <label class="form-check-label" for="negativeFeedback">Negative</label>
                            </div>
                        </div>

                        <div class="mb-3" id="ratingSection">
                            <label class="form-label">Rating</label>
                            <div id="starRating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingValue">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feedback</label>
                            <textarea name="feedback" class="form-control" id="feedbackText" rows="3"></textarea>
                        </div>


                    </div> --}}

                    <div class="modal-body">
                        <input type="hidden" name="task_id" id="feedbackTaskId">

                        {{-- Read-Only View Mode --}}
                        <div id="readonlyFeedback" style="display: none;">
                            <div class="mb-2 ">
                                <label class="form-label">Feedback Type:</label>
                                <p class="form-control-plaintext mb-2" id="readonlyType"></p>

                            </div>

                            <div class="mb-2">
                                <label class="form-label">Rating:</label>
                                {{-- <p class="form-control-plaintext" id="readonlyRating"></p> --}}
                                <div id="readonlyRating" class="form-control-plaintext"></div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Feedback:</label>
                                <p class="form-control-plaintext border p-2 bg-light rounded" id="readonlyText"></p>
                            </div>
                        </div>

                        {{-- Editable Form --}}
                        <div id="editableFeedback">
                            <div class="mb-3">
                                <label class="form-label">Feedback Type</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input feedback-type-radio" type="radio"
                                        name="feedback_type" id="positiveFeedback" value="positive" checked>
                                    <label class="form-check-label" for="positiveFeedback">Positive</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input feedback-type-radio" type="radio"
                                        name="feedback_type" id="negativeFeedback" value="negative">
                                    <label class="form-check-label" for="negativeFeedback">Negative</label>
                                </div>
                            </div>

                            <div class="mb-3" id="ratingSection">
                                <label class="form-label">Rating</label>
                                <div id="starRating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star star" data-value="{{ $i }}"></i>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingValue">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Feedback</label>
                                <textarea name="feedback" class="form-control" id="feedbackText" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="submitFeedbackBtn" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            const allUsers = @json($reassign_users);

            // Delegated event listener for dynamic DataTable buttons
            $(document).on('click', '.open-reassign-modal', function() {
                const encryptedTaskId = $(this).data('id');
                const currentUserId = $(this).data('user-id');
                const $dropdown = $('#assignTo');
                const $hiddenInput = $('#modalTaskId');

                console.log('Clicked for task:', encryptedTaskId, 'Current user:', currentUserId);

                $hiddenInput.val(encryptedTaskId);
                $dropdown.html('<option value="">Select User</option>');

                allUsers.forEach(user => {
                    if (user.id != currentUserId) {
                        $dropdown.append(
                            $('<option>', {
                                value: user.id,
                                text: user.first_name + ' ' + user.last_name + ' (' + user
                                    .department_name + ')'
                            })
                        );
                    }
                });

                // Open the modal
                $('#reassignModal').modal('show');
            });
        });
    </script>



    {{-- reassign --}}

    {{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Remark</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="message-text" class="col-form-label">Message:</label>
                            <textarea name="remark" class="form-control" id="message-text"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send message</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <!-- users list ends -->
    @php
        $selectedColumnsRaw = auth()->user()->selected_fields;
        $selectedColumns = is_string($selectedColumnsRaw) ? json_decode($selectedColumnsRaw, true) : [];

        if (empty($selectedColumns)) {
            $selectedColumns = [
                '0',
                '3',
                '4',
                '5',
                '7',
                '8',
                '9',
                '10',
                '11',
                '12',
                '13',
                '14',
                '15',
                '16',
                '17',
                '18',
                '19',
                '20',
                '21',
                '22',
            ];
        }
    @endphp
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

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
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>

    <script>
        // Datepicker for advanced filter
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
                onClose: function(selectedDates, dateStr, instance) {
                    var startDate = '',
                        endDate = new Date();
                    if (selectedDates[0] != undefined) {
                        startDate =
                            selectedDates[0].getMonth() + 1 + '/' + selectedDates[0].getDate() + '/' +
                            selectedDates[0].getFullYear();
                        $('.start_date').val(startDate);
                    }
                    if (selectedDates[1] != undefined) {
                        endDate =
                            selectedDates[1].getMonth() + 1 + '/' + selectedDates[1].getDate() + '/' +
                            selectedDates[1].getFullYear();
                        $('.end_date').val(endDate);
                    }
                    $(rangePickr).trigger('change').trigger('keyup');
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {

            var type = @json($type);
            var selectedColumns = @json($selectedColumns);
            var method_type = 'GET';
            // alert(selectedColumns.includes("0"));

            @if ($type === 'accepted')
                {
                    ajaxUrl = "{{ route('app-task-get-accepted') }}";
                }
            @elseif ($type === 'team_task') {
                    ajaxUrl = "{{ route('app-task-get-team_task-list') }}";
                }
            @elseif ($type === 'requested') {
                    ajaxUrl = "{{ route('app-task-get-requested') }}";
                }
            @elseif ($type == 'main') {
                    ajaxUrl = "{{ route('app-task-get-main') }}";
                }
            @elseif ($type === 'recurring_main') {
                    ajaxUrl = "{{ route('app-task-get-recurring_main') }}";
                }
            @elseif ($type === 'due_date_past') {
                    ajaxUrl = "{{ route('app-task-get-due_date_past') }}";
                }
            @elseif ($type === 'todays_due_task') {
                    ajaxUrl = "{{ route('app-task-get-todays_due_task') }}";
                }
            @elseif ($type === 'pending_task') {
                    ajaxUrl = "{{ route('app-task-get-pending_task') }}";
                }
            @elseif ($type === 'completed_task') {
                    ajaxUrl = "{{ route('app-task-get-completed_task') }}";
                }
            @elseif ($type === 'overall_task') {
                    ajaxUrl = "{{ route('app-task-get-overall_task') }}";
                }
            @elseif ($type === 'reassign_task') {
                    ajaxUrl = "{{ route('app-task-get-reassign_task') }}";
                }
            @elseif ($type === 'list') {
                    ajaxUrl = "{{ route('app-task-getAll_total_task-get') }}";
                }
            @elseif ($type == 'mytask') {
                    ajaxUrl = "{{ route('app-task-mytask-get') }}";
                }
            @elseif ($type == 'accepted_by_me') {
                    ajaxUrl = "{{ route('app-task-getAll_accepted_by_me-get') }}";
                }
            @elseif ($type == 'assign_by_me') {
                    ajaxUrl = "{{ route('app-task-getAll_assign_by_me-get') }}";
                    method_type = 'POST';
                }
            @elseif ($type == 'requested_me') {
                    ajaxUrl = "{{ route('app-task-getAll_requested_me-get') }}";
                }
            @elseif ($type == 'conceptualization') {
                    ajaxUrl = "{{ route('app-task-getAll_conceptualization-get') }}";
                }
            @elseif ($type == 'close') {
                    ajaxUrl = "{{ route('app-task-getAll_close-get') }}";
                }
            @elseif ($type == 'dynamic_report') {
                    const dateField = @json($dynamic_date_field);
                    const fromDate = @json($dynamic_from_date);
                    const toDate = @json($dynamic_to_date);

                    ajaxUrl = "{{ route('app-task-get-dynamic_report_list') }}" +
                        "?date_field=" + encodeURIComponent(dateField) +
                        "&from_date=" + encodeURIComponent(fromDate) +
                        "&to_date=" + encodeURIComponent(toDate);
                }
            @elseif ($type == 'scope_defined') {
                    ajaxUrl = "{{ route('app-task-getAll_scope_defined-get') }}";
                }
            @elseif ($type == 'completed') {
                    ajaxUrl = "{{ route('app-task-getAll_completed-get') }}";
                }
            @elseif ($type == 'hold') {
                    ajaxUrl = "{{ route('app-task-getAll_hold-get') }}";
                }
            @elseif ($type == 'in_execution') {
                    ajaxUrl = "{{ route('app-task-getAll_in_execution-get') }}";
                }
            @elseif ($type == 'admin_acc') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_acc-get') }}";
                }
            @elseif ($type == 'admin_req') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_req-get') }}";
                }
            @elseif ($type == 'admin_rej') {
                    ajaxUrl = "{{ route('app-task-getAll_admin_rej-get') }}";
                }
            @elseif ($type == 'total_task') {
                    ajaxUrl = "{{ route('app-task-getAll_total_task-get') }}";
                }
            @elseif ($type == 'deleted') {
                    ajaxUrl = "{{ route('app-task-getAll_deleted-get') }}";
                }
            @elseif ($type == 'my_and_team') {
                    ajaxUrl = "{{ route('app-task-my_and_team-get') }}";
                }
            @elseif ($type == 'tasks.requested_to_us' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_rejected_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_rejected_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_overall_total' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_overall_total_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_to_us']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_rejected_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_rejected_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_overall_total' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_overall_total_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'requested_by_me']) }}";
                }
            @elseif ($type == 'tasks.total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_status' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_status_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_pending_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_pending_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_over_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_over_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_today_due' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_today_due_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_finished_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_finished_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_total_task' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_total_task_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.requested_to_us_footer_total' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_to_us_footer_total_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.requested_by_us_footer_total' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.requested_by_us_footer_total_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @elseif ($type == 'tasks.total_task_footer_total' && $user_id != '') {
                    ajaxUrl =
                        "{{ route('tasks.total_task_footer_total_list', ['user_id' => $user_id, 'status_id' => $status_id, 'type' => 'total_task']) }}";
                }
            @endif
            var table = $('#tasks-table').DataTable({
                dom: '<"export-buttons"B>lfrtip',
                processing: true,
                serverSide: true,
                pageLength: 10,
                filter: true,
                deferRender: true,

                scroller: true,
                buttons: [{
                    extend: 'excel',
                    text: '<i class="ficon" data-feather="file-text"></i> Export to Excel',
                    action: newexportaction,
                    title: '',
                    filename: 'Task',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        modifier: {
                            search: 'applied',
                            order: 'applied'
                        },
                        columns: [23, 2, 3, 4, 5, 6, 7, 8, 9, 10, 24, 25, 26, 27, 28, 16, 17, 18,
                            19, 20, 21, 29,
                            @if ($type == 'assign_by_me')
                                30,
                                31,
                                32,
                                33 // Added assign_to_status and assign_to_report_to columns for assign_by_me
                            @elseif ($type == 'mytask')
                                30, 31, 32
                            @else
                                30,
                                31 // Added assign_to_status and assign_to_report_to columns for other types
                            @endif
                        ]
                        //Add 1 in columns for pin task column
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        $('row c[r]', sheet).each(function() {
                            var cell = $(this);
                            var cellRef = cell.attr('r');

                            // Check if the column belongs to one of the date fields
                            if (cellRef.match(
                                    /^(K|L|M|N|O|P|Q)$/
                                )) { // Adjust based on column index
                                cell.attr('s', '2'); // Apply date format
                            }
                        });
                    }
                }],

                ajax: {
                    url: ajaxUrl,
                    method: method_type,
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.department = $('#filter-department').val();
                        d.assignees = $('#filter-assignee').val();
                        d.dt_date = $('#dt_date').val();
                        d.fild_search = $('#select-filter').val();
                        d.task = $('#task').val();
                        d.accepted_task_date = $('#accepted_task_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#filter-status').val();
                        d.project = $('#filter-project').val();
                        d.created_by = $('#filter-created-by').val();
                        d.start_date = $('#filter-start-date').val();
                        d.task_type = $('#task_type').val();

                    }
                },
                searching: true,

                order: [
                    @if ($type == 'mytask')

                        [23, 'desc'],
                    @endif
                    [2, 'desc']
                ],
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
                        data: 'task_status',
                        name: 'task_status',
                        searchable: true,
                        visible: selectedColumns.includes("10")
                    },
                    {
                        data: 'Created_Date',
                        name: 'Created_Date',
                        searchable: true,
                        visible: selectedColumns.includes("11")

                    },

                    {
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
                            export: false
                        },
                    @endif


                    {
                        data: 'status',
                        name: 'status',
                        searchable: true,
                        visible: true,
                        // selectedColumns.includes(
                        //     "23"), // Checks if "23" is in the selectedColumns array
                        render: function(data, type, row) {
                            // Conditionally return the appropriate label based on the status value
                            switch (data) {
                                case 0:
                                    return 'Requested';
                                case 1:
                                    return 'Accepted';
                                case 2:
                                    return 'Rejected';
                                default:
                                    return '-'; // For unexpected values
                            }
                        }
                    },


                    @if ($type == 'assign_by_me')
                        {
                            data: 'rating',
                            name: 'rating',
                            searchable: true,
                        }, {
                            data: 'task_feedback',
                            name: 'task_feedback',
                            searchable: true,
                        },
                    @endif

                    {
                        data: 'Created_Date',
                        name: 'Created_Date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'due_date',
                        name: 'due_date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'completed_date',
                        name: 'completed_date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'accepted_date',
                        name: 'accepted_date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'close_date',
                        name: 'close_date',
                        visible: false,
                        searchable: true,
                        exportOnly: true,
                        render: function(data) {
                            if (data && data.includes('/')) {
                                var parts = data.split(
                                    '/'); // Splitting 'd/m/Y' into [day, month, year]
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[
                                    0]; // Convert to 'Y-m-d' for Date object
                                var date = new Date(formattedDate);

                                // Return date in 'd-m-Y' format for display & ensure Excel recognizes it as a date
                                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1))
                                    .slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                            }
                            return '';
                        }
                    },
                    {
                        data: 'assign_to_status',
                        name: 'assign_to_status',
                        searchable: true,
                        visible: true,
                        export: true,
                    }, {
                        data: 'assign_to_report_to',
                        name: 'assign_to_report_to',
                        searchable: true,
                        visible: true,
                        export: true,

                    },
                ],

                drawCallback: function() {
                    feather.replace();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });


            $(document).ready(function() {
                $('#columnVisibilityModal').on('change', '.column-toggle', function() {
                    var columnIndex = $(this).data('column');
                    var column = table.column(columnIndex);
                    column.visible($(this).prop('checked'));
                });
            });

            function newexportaction(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;

                dt.one('preXhr', function(e, s, data) {
                    data.start = 0;
                    data.length = dt.page.info().recordsTotal; // Fetch all records

                    dt.one('preDraw', function(e, settings) {
                        // Perform export
                        if (button.hasClass('buttons-excel')) {
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button,
                                config);
                        } else if (button.hasClass('buttons-csv')) {
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button,
                                config);
                        } else if (button.hasClass('buttons-pdf')) {
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button,
                                config);
                        }

                        // Restore previous pagination
                        settings._iDisplayStart = oldStart;
                        setTimeout(function() {
                            dt.ajax.reload();
                        }, 0);
                    });
                });

                dt.ajax.reload(); // Do not remove this, ensures export is performed after full data load
            }



            // Apply Filters
            $('#apply-filters').on('click', function() {
                table.draw();
            });
        });

        // 03-03-2025
        // function newexportaction(e, dt, button, config) {
        //     var self = this;
        //     var oldStart = dt.settings()[0]._iDisplayStart;
        //     dt.one('preXhr', function(e, s, data) {
        //         // Just this once, load all data from the server...
        //         data.start = 0;
        //         data.length = 2147483647;
        //         dt.one('preDraw', function(e, settings) {
        //             // Call the original action function
        //             if (button[0].className.indexOf('buttons-copy') >= 0) {
        //                 $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button,
        //                     config);
        //             } else if (button[0].className.indexOf('buttons-excel') >= 0) {
        //                 $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt,
        //                         button, config) :
        //                     $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt,
        //                         button, config);
        //             } else if (button[0].className.indexOf('buttons-csv') >= 0) {
        //                 $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button,
        //                         config) :
        //                     $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button,
        //                         config);
        //             } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
        //                 $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
        //                     $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button,
        //                         config) :
        //                     $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button,
        //                         config);
        //             } else if (button[0].className.indexOf('buttons-print') >= 0) {
        //                 $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
        //             }
        //             dt.one('preXhr', function(e, s, data) {
        //                 settings._iDisplayStart = oldStart;
        //                 data.start = oldStart;
        //             });
        //             // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
        //             setTimeout(dt.ajax.reload, 0);
        //             // Prevent rendering of the full data to the DOM
        //             return false;
        //         });
        //     });
        //     // Requery the server with the new one-time export settings
        //     dt.ajax.reload();
        // }
        // 03-03-2025


        $(document).ready(function() {
            // Fetch status options and populate the dropdown
            $.ajax({
                url: '{{ route('get-status') }}', // Assuming this is the endpoint to fetch status options
                method: 'GET',
                success: function(response) {
                    // Assuming response is an array of status objects
                    response.forEach(function(status) {
                        $('#filter-status').append('<option value="' + status.id + '">' + status
                            .displayname + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching status options:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-projects') }}', // Define this route for fetching project options
                method: 'GET',
                success: function(response) {
                    // Assuming response is an array of project objects
                    response.forEach(function(project) {
                        $('#filter-project').append('<option value="' + project.id + '">' +
                            project.project_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching project options:', error);
                }
            });

            $.ajax({
                url: '{{ route('get-users') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(user) {
                        $('#filter-created-by').append('<option value="' + user.id + '">' + user
                            .full_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching created by options:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-users') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(user) {
                        $('#filter-assignee').append('<option value="' + user.id + '">' + user
                            .full_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching assignees:', error);
                }
            });
            $.ajax({
                url: '{{ route('get-departments') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach(function(department) {
                        $('#filter-department').append('<option value="' + department.id +
                            '">' + department.department_name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching department options:', error);
                }
            });

        });



        $(document).on("click", ".pin-task-icon", function(e) {
            e.preventDefault();

            var taskId = $(this).data("task-id"); // Retrieve the task ID from the clicked icon
            var isPinned = $(this).css('color') === 'rgb(255, 0, 0)';

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: isPinned ? "Do you want to unpin this task?" : "Do you want to pin this task?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: isPinned ? 'Yes unpin it!' : 'Yes, pin it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Send AJAX request to pin the task
                    $.ajax({
                        url: '/app/task/pin', // Update this URL to match your backend endpoint
                        method: 'POST',
                        data: {
                            task_id: taskId,
                            _token: $('meta[name="csrf-token"]').attr(
                                'content') // Include CSRF token
                        },
                        success: function(response) {
                            // Handle success
                            Swal.fire({
                                icon: 'success',
                                title: 'Pinned!',
                                text: isPinned ?
                                    'The task has been unpinned Successfully' :
                                    'The task has been pinned successfully.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                            // Optionally refresh the DataTable to reflect changes
                            $('#tasks-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            // Handle error
                            Swal.fire({
                                title: 'Error',
                                text: 'You Cannot Pin This Task Because It Is Not Assigned To You.',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Handle cancellation
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'The task was not pinned.',
                        icon: 'info',
                        customClass: {
                            confirmButton: 'btn btn-info'
                        }
                    });
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
                    window.location.href = '/app/task/destroy/' + id;
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



        $(document).on("click", ".confirm-recurring_destroy", function(e) {
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
                    window.location.href = '/app/task/recurring_destroy/' + id;
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
        $(document).on("click", ".accept-task", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            // First SweetAlert: Confirm task acceptance
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, accept it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    // Make an AJAX call to accept the task without refreshing the page
                    $.ajax({
                        url: "{{ route('app-task-accept', ':id') }}".replace(':id', id),
                        method: "GET",
                        data: {
                            // Include any necessary data like CSRF token and task data
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Success message after task is accepted
                            // Swal.fire({
                            //     icon: 'success',
                            //     title: 'Accepted!',
                            //     text: 'Your task has been accepted.',
                            //     customClass: {
                            //         confirmButton: 'btn btn-success'
                            //     }
                            // }).then(function() {
                            // Show the second SweetAlert for comment decision
                            Swal.fire({
                                title: 'Do you want to add a comment now or later?',
                                showCancelButton: true,
                                confirmButtonText: 'Add Now',
                                cancelButtonText: 'Add Later',
                                customClass: {
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-outline-danger ms-1'
                                },
                                buttonsStyling: false
                            }).then(function(result) {
                                if (result.value) {
                                    // If 'Add Now' is chosen, redirect to the update task page
                                    window.location.href =
                                        "{{ route('app-task-edit', ':id') }}"
                                        .replace(':id', id);
                                } else {
                                    // Refresh the page after the message is shown
                                    location.reload();
                                }

                                // if (result.dismiss === Swal
                                //     .DismissReason.cancel) {
                                //     // If 'Add Later' is chosen, do nothing or handle accordingly
                                //     Swal.fire({
                                //         title: 'You can add a comment later.',
                                //         text: 'The task will remain accepted.',
                                //         icon: 'info',
                                //         customClass: {
                                //             confirmButton: 'btn btn-info'
                                //         }
                                //     }).then(function() {
                                //         // Refresh the page after the message is shown
                                //         location.reload();
                                //     });
                                // }
                            });
                            // });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'There was an error accepting the task. Please try again.',
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                }
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // If task acceptance is cancelled
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


        $(document).on("click", ".confirm-retrieve", function(e) {
            e.preventDefault();
            var id = $(this).data("idos");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, retrieve it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '/app/task/retrive/' + id;
                    Swal.fire({
                        icon: 'success',
                        title: 'retrieved!',
                        text: 'Your file has been retrieved.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Cancelled',
                        text: 'Your imaginary file is deleted :)',
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.open-reassign-modal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const encryptedId = this.getAttribute('data-id');
                    document.getElementById('modalTaskId').value = encryptedId;
                });
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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).on('click', '.reject-btn', function() {
        var taskId = $(this).data('id');
        var formAction = '{{ route('app-task-reject', ':taskId') }}';
        formAction = formAction.replace(':taskId', taskId);
        $('#rejectForm').attr('action', formAction);
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<script>
    $(document).ready(function() {
        // Existing feedback button click logic
        $(document).on('click', '.give-feedback-btn', function(e) {
            e.preventDefault();

            let taskId = $(this).data('id');
            let rating = $(this).data('rating');
            let feedback = $(this).data('feedback');
            let isGiven = $(this).data('given') == 1;

            $('#feedbackTaskId').val(taskId);

            if (isGiven) {
                // Show read-only view
                $('#readonlyFeedback').show();
                $('#editableFeedback').hide();
                $('#submitFeedbackBtn').hide();

                $('#readonlyType').text(rating == 0 ? 'Negative' : 'Positive');
                // $('#readonlyRating').text(rating == 0 ? '0 (Negative)' : rating);
                if (rating == 0) {
                    $('#readonlyRating').html('<span class="text-danger">Negative</span>');
                } else {
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= rating) {
                            starsHtml += '<i class="fa fa-star text-warning"></i>'; // filled star
                        } else {
                            starsHtml += '<i class="fa fa-star text-secondary"></i>'; // empty/gray star
                        }
                    }
                    $('#readonlyRating').html(starsHtml);
                }
                $('#readonlyText').text(feedback || 'No feedback given.');
            } else {
                // Show editable form
                $('#readonlyFeedback').hide();
                $('#editableFeedback').show();
                $('#submitFeedbackBtn').show();

                // Reset values
                $('#feedbackText').val('');
                $('#ratingValue').val('');
                $('#starRating .star').removeClass('selected');
                $('#positiveFeedback').prop('checked', true);
                $('#ratingSection').show();
            }

            $('#feedbackModal').modal('show');
        });

        // Star click rating logic
        $('#starRating .star').on('click', function() {
            let rating = $(this).data('value');
            $('#ratingValue').val(rating);
            $('#starRating .star').removeClass('selected');
            $('#starRating .star').each(function() {
                if ($(this).data('value') <= rating) {
                    $(this).addClass('selected');
                }
            });
        });

        // Toggle rating section based on radio button
        $('.feedback-type-radio').on('change', function() {
            if ($('#negativeFeedback').is(':checked')) {
                $('#ratingSection').hide();
                $('#ratingValue').val(0);
                $('#starRating .star').removeClass('selected');
            } else {
                $('#ratingSection').show();
                $('#ratingValue').val('');
            }
        });

        // Submit form
        $('#feedbackForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('app-task-feedback-store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#feedbackModal').modal('hide');
                    toastr.success('Feedback submitted successfully!', 'Success', {
                        closeButton: true,
                        progressBar: true
                    });
                },
                error: function() {
                    toastr.error(errorMsg, 'Error', {
                        closeButton: true,
                        progressBar: true
                    });
                }
            });
        });
    });
</script>
