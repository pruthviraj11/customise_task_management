@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection



@section('content')

    <form action="{{ route('app-task-recurring-update', encrypt($task->id)) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- @endphp --}}
        <section id="multiple-column-form">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $page_data['form_title'] }}</h4>
                            <a href="{{ route('app-task-list') }}" class="col-md-2 btn btn-primary float-end">Task List</a>
                            {{-- <a href="{{ route('check-tasks') }}" class="btn btn-primary">Check and Create Today's Tasks</a> --}}

                            {{-- <h4 class="card-title">{{$page_data['form_title']}}</h4> --}}

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 d-flex col-sm-12 mb-1">
                                    <div class="form-check m-2 form-check-success">
                                        <input type="radio" class="form-check-input" id="ticket" name="task_type"
                                            value="1" {{ $task != '' && $task->ticket == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ticket">Ticket</label>
                                    </div>
                                    <div class="form-check m-2 form-check-success">
                                        <input type="radio" class="form-check-input" id="task" name="task_type"
                                            value="0" {{ $task != '' && $task->ticket == 0 ? 'checked' : '' }}
                                            @if ($task == '') checked @endif>
                                        <label class="form-check-label" for="task">Task</label>
                                    </div>
                                    <div class="form-check m-2 form-check-success">
                                        <input type="checkbox" class="form-check-input" id="recurring" name="recurring"
                                            value="1" checked>
                                        <label class="form-check-label" for="recurring">Recurring</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    @if ($task != '')
                                        <a class=" btn-sm btn-primary "> Task # {{ $task->id }}</a>
                                        <a class=" btn-sm btn-primary "> Task Created By {{ $task->creator->first_name }}
                                            {{ $task->creator->last_name }}</a>
                                    @endif

                                </div>
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="title">
                                        Title<span class="red">*</span>
                                    </label>
                                    <input type="text" readonly id="title" class="form-control"
                                        placeholder="Enter Task Name" name="title"
                                        value="{{ old('title') ?? ($task != '' ? $task->title : '') }}" required>
                                    <span class="text-danger">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="subject">
                                        Subject<span class="red">*</span>
                                    </label>
                                    <input type="text" readonly id="subject" class="form-control"
                                        placeholder="Enter subject" name="subject"
                                        value="{{ old('subject') ?? ($task != '' ? $task->subject : '') }}" required>
                                    <span class="text-danger">
                                        @error('subject')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var titleInput = document.getElementById('title');
                                        var subjectInput = document.getElementById('subject');

                                        // Set subject value to match title on page load if subject is empty
                                        if (!subjectInput.value) {
                                            subjectInput.value = titleInput.value;
                                        }

                                        // Update subject value when title value changes
                                        titleInput.addEventListener('input', function() {
                                            subjectInput.value = titleInput.value;
                                        });
                                    });
                                </script>
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="project_id">Project</label><span class="red">*</span>
                                    <select id="project_id" disable class="form-select select2" name="project_id" required>
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ old('project_id') == $project->id ? 'selected' : ($task ? ($task->project_id == $project->id ? 'selected' : '') : '') }}>
                                                {{ $project->project_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('project_id')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>


                                <style>
                                    .fixed-width {
                                        display: inline-block;
                                        width: 50px;
                                        /* Set the width as needed */
                                        overflow: hidden;
                                        white-space: nowrap;
                                    }
                                </style>
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="user_id">Assign To</label><span
                                        class="red">*</span>
                                    <select id="user_id" class="form-select select2" name="user_id[]" multiple
                                        required>
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $assignedUserIds) ? 'selected' : '' }}>
                                                <span class="fixed-width">{{ $user->first_name }}
                                                    {{ $user->last_name }}</span>
                                                |{{ $user->department->department_name ?? '' }}|
                                                {{ $user->sub_department->sub_department_name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('user_id')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>







                                <div class="col-md-3 col-sm-12 mb-1 position-relative">
                                    <label class="form-label" for="start_date">Start Date</label><span
                                        class="red">*</span>
                                    <input type="date" readonly id="start_date" class="form-control"
                                        name="start_date"
                                        value="{{ old('start_date') ?? ($task != '' ? $task->start_date : date('Y-m-d')) }}"
                                        required>
                                    <span class="text-danger">
                                        @error('start_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>


                                <input type="hidden" name="recurring_type" value="{{ $task->recurring_type }}">
                                <div id="recurring_options" class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="recurring_type">Recurring Type</label>
                                    <select id="recurring_type" disabled class="form-control select2"
                                        name="recurring_type">
                                        <option value="daily"
                                            {{ old('recurring_type', $task->recurring_type) == 'daily' ? 'selected' : '' }}>
                                            Daily
                                        </option>
                                        <option value="weekly"
                                            {{ old('recurring_type', $task->recurring_type) == 'weekly' ? 'selected' : '' }}>
                                            Weekly
                                        </option>
                                        <option value="monthly"
                                            {{ old('recurring_type', $task->recurring_type) == 'monthly' ? 'selected' : '' }}>
                                            Monthly
                                        </option>
                                        <option value="quarterly"
                                            {{ old('recurring_type', $task->recurring_type) == 'quarterly' ? 'selected' : '' }}>
                                            Quarterly
                                        </option>
                                        <option value="half_quarterly"
                                            {{ old('recurring_type', $task->recurring_type) == 'half_quarterly' ? 'selected' : '' }}>
                                            Half Quarterly
                                        </option>
                                        <option value="yearly"
                                            {{ old('recurring_type', $task->recurring_type) == 'yearly' ? 'selected' : '' }}>
                                            Yearly
                                        </option>
                                    </select>
                                </div>


                                <div id="number_of_time_container" class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="number_of_time">Number of Times</label>
                                    <input type="number" id="number_of_time"
                                        value="{{ old('number_of_days') ?? ($task != '' ? $NotCompletedtask : '') }}"
                                        class="form-control" name="number_of_time" placeholder="Enter number of days"
                                        required>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="priority_id">Priority</label><span
                                        class="red">*</span>
                                    <select id="priority_id" class="form-select select2" name="priority_id" required>
                                        {{-- <option value="">Select Priority</option> --}}
                                        @foreach ($Prioritys as $Priority)
                                            <option value="{{ $Priority->id }}"
                                                {{ old('priority_id') == $Priority->id ? 'selected' : ($task ? ($task->priority_id == $Priority->id ? 'selected' : '') : '') }}>
                                                {{ $Priority->displayname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('priority_id')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>



                                <div class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="task_status">Status</label><span
                                        class="red">*</span>
                                    <select id="task_status" class="form-select select2" name="task_status"
                                        {{ $task ? ($task->task_status == 7 ? 'disabled' : '') : '' }} required>
                                        {{-- <option value="">Select Status</option> --}}
                                        @foreach ($Status as $Statu)
                                            <option value="{{ $Statu->id }}"
                                                @php $isDisabled = $Statu->disabled == true && ($task == ' ' || (is_object($task) && $task->created_by != auth()->user()->id)); @endphp
                                                {{ $isDisabled ? 'disabled' : '' }} {{-- {{ $Statu->disabled == true && ($task && $task->created_by != auth()->user()->id) ? 'disabled' : '' }} --}}
                                                {{ old('task_status') == $Statu->id ? 'selected' : ($task ? ($task->task_status == $Statu->id ? 'selected' : '') : '') }}>
                                                {{ $Statu->displayname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('task_status')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-md-12 col-sm-12 ">
                                    <label class="form-label" for="attachments">Attachments</label>
                                    <div class="input-group mb-3 w-100">
                                        <input type="file" class="form-control" id="attachments" name="attachments[]"
                                            multiple>
                                        <label class="input-group-text btn btn-info" for="attachments">+ Choose</label>
                                    </div>
                                    @if ($task)
                                        <ul>
                                            @foreach ($attachmentsrec as $attachment)
                                                <li>

                                                    <a
                                                        href="{{ route('attachment.download', ['attachmentId' => $attachment->id]) }}">

                                                        {{ last(explode('/', $attachment->file)) }}

                                                    </a>


                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <span class="text-danger">
                                        @error('attachments')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-1">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea id="description" class="form-control" placeholder="Enter Description" name="description">{{ old('description') ?? ($task != '' ? html_entity_decode($task->description) : '') }}</textarea>
                                    <span class="text-danger">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-1">
                                    <a class="btn btn-danger btn-sm cancel-btn" data-task-id="{{ $task->id }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel">Cancel
                                        <i class="feather-icon" data-feather="x-circle"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" name="submit" value="submit" class="btn btn-primary me-1">Submit
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </div>
            </div>




            </div>
            </div>
        </section>
    </form>
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
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>

@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/components/components-tooltips.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
    <script>
        // Function to generate a random password
        function generateRandomPassword(length) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            let password = "";
            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset.charAt(randomIndex);
            }
            return password;
        }
    </script>


    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#status').select2();

            $('.cancel-btn').on('click', function() {
                var subtaskId = $(this).data('task-id'); // Get the subtask ID
                // Set the subtask ID in the hidden field or any element you want to store it
                $('#subtaskIdInput').val(subtaskId); // Save the subtask ID to the hidden input

                // Show SweetAlert confirmation before proceeding
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to Cancel this task.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Cancel it!',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('app-task-recurring_cancel', ['encrypted_id' => 'subtaskId']) }}'
                                .replace('subtaskId', subtaskId),
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Cancelled!',
                                        'The Recurring Task has been canceled.',
                                        'success'
                                    );
                                    // Reload the page after success
                                    location.reload();
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'There was an issue fetching the subtask data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $("#editorHTML").val($('#editor').html());
            var quill = new Quill('.editor', {
                bounds: '#full-container .editor',
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [{
                            font: []
                        }, {
                            size: []
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            color: []
                        }, {
                            background: []
                        }],
                        [{
                            script: 'super'
                        }, {
                            script: 'sub'
                        }],
                        [{
                            header: '1'
                        }, {
                            header: '2'
                        }, 'blockquote', 'code-block'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }, {
                            indent: '-1'
                        }, {
                            indent: '+1'
                        }],
                        ['direction', {
                            align: []
                        }],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });
            // let quill = new Quill('#editor');
            $(document).on('click', '.addTextBtn', function() {
                var dataValue = $(this).parents('.parent').find('.addTextBtn').data('value');
                var cursorPos = quill.getSelection();
                // Insert the text at the cursor position.
                quill.insertText(cursorPos, dataValue);
                var html = $('#editor').html();
                $("#description").val(html);
            })

            $(document).on('keyup', '#editor', function(event) {
                event.preventDefault();

                var html = $('#editor').html();

            })
        });
    </script>

    {{-- <script>
        $(document).ready(function() {



            $('#department_id').change(function() {
                var departmentId = $(this).val();
                $('#sub_department_id').prop("disabled", true);
                if (departmentId) {
                    $.ajax({
                        url: '{{ route('app-sub-departments', ':department_id') }}'.replace(
                            ':department_id', departmentId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            $('#sub_department_id').empty();

                            var data_load = '<option value="">Select Sub Department</option>';
                            $.map(data, function(item) {
                                data_load += '<option value="' + item
                                    .id + '">' + item.sub_department_name +
                                    '</option>';
                            });
                            $('#sub_department_id').append(data_load);
                            $('#sub_department_id').select2();
                            $('#sub_department_id').prop("disabled", false);
                        }
                    });

                    // Fetch users based on department
                    $.ajax({
                        url: '{{ route('app-users-by-department', ':department_id') }}'.replace(
                            ':department_id', departmentId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#user_id').empty();
                            $('#user_id').append('<option value="">Select User</option>');
                            $.each(data, function(key, value) {
                                $('#user_id').append('<option value="' + value.id +
                                    '">' + value.first_name + ' ' + value
                                    .last_name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#sub_department_id').empty();
                    $('#sub_department_id').append('<option value="">Select Sub Department</option>');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function enableAssignToSection() {
                $('#assign_to_section').find('select, input').prop('disabled', false);
            }

            function disableAssignToSection() {
                $('#assign_to_section').find('select, input').prop('disabled', true);
            }

            function fetchUsersBySubDepartment(subDepartmentId) {
                $.ajax({
                    url: '{{ route('app-users-by-department', ':sub_department_id') }}'.replace(
                        ':sub_department_id', subDepartmentId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#user_id').empty();
                        $('#user_id').append('<option value="">Select User</option>');
                        $.each(data, function(key, value) {
                            $('#user_id').append('<option value="' + value.id + '">' + value
                                .first_name + ' ' + value.last_name + '</option>');
                        });
                        enableAssignToSection();
                        // Preselect users if editing
                        @if ($task)
                            var selectedUsers = @json($task->users->pluck('id'));
                            selectedUsers.forEach(function(userId) {
                                $('#user_id option[value="' + userId + '"]').prop('selected',
                                    true);
                            });
                            $('#user_id').trigger('change');
                        @endif
                    }
                });
            }

            // Initial check to see if sub_department_id is already selected
            var initialSubDepartmentId = $('#sub_department_id').val();
            if (initialSubDepartmentId) {
                fetchUsersBySubDepartment(initialSubDepartmentId);
            } else {
                disableAssignToSection();
            }

            $('#department_id').change(function() {
                var departmentId = $(this).val();
                $('#sub_department_id').prop("disabled", true);

                if (departmentId) {
                    $.ajax({
                        url: '{{ route('app-sub-departments', ':department_id') }}'.replace(
                            ':department_id', departmentId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#sub_department_id').empty();
                            var data_load = '<option value="">Select Sub Department</option>';
                            $.map(data, function(item) {
                                data_load += '<option value="' + item.id + '">' + item
                                    .sub_department_name + '</option>';
                            });
                            $('#sub_department_id').append(data_load);
                            $('#sub_department_id').select2();
                            $('#sub_department_id').prop("disabled", false);
                        }
                    });

                    // Clear user selection and disable Assign To section
                    disableAssignToSection();
                    $('#user_id').empty();
                    $('#user_id').append('<option value="">Select User</option>');
                } else {
                    $('#sub_department_id').empty();
                    $('#sub_department_id').append('<option value="">Select Sub Department</option>');
                    $('#user_id').empty();
                    $('#user_id').append('<option value="">Select User</option>');
                }
            });

            // Fetch users based on sub-department selection
            $('#sub_department_id').change(function() {
                var subDepartmentId = $(this).val();
                if (subDepartmentId) {
                    fetchUsersBySubDepartment(subDepartmentId);
                } else {
                    disableAssignToSection();
                    $('#user_id').empty();
                    $('#user_id').append('<option value="">Select User</option>');
                }
            });
        });
    </script> --}}



    <script>
        $(document).ready(function() {
            var startDateInput = document.getElementById('start_date');
            var dueDateInput = document.getElementById('due_date');
            var today = new Date().toISOString().split('T')[0];

            // Set today's date as the default start date if not already set
            if (!startDateInput.value) {
                startDateInput.value = today;
            }

            // Disable past dates for the due date
            dueDateInput.min = startDateInput.value || today;

            // Update due date min value when start date changes
            startDateInput.addEventListener('change', function() {
                dueDateInput.min = this.value;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#status').select2();

            $('.edit-btn').on('click', function() {
                var subtaskId = $(this).data('subtask-id'); // Get the subtask ID

                // Set the subtask ID in the hidden field or any element you want to store it
                $('#subtaskIdInput').val(subtaskId); // Save the subtask ID to the hidden input

                // Show SweetAlert confirmation before proceeding
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to edit this subtask.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Edit it!',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('subtask.edit', ['subtask' => '__subtaskId__']) }}'
                                .replace('__subtaskId__', subtaskId),
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    // Pre-fill the modal fields with the subtask data
                                    $('#due_date_sub').val(response.subtask.due_date ?
                                        response.subtask.due_date : '');

                                    // Populate the status dropdown dynamically
                                    $('#status').empty(); // Clear any existing options
                                    response.statuses.forEach(function(status) {
                                        var isSelected = status.id == response
                                            .subtask.status ? 'selected' : '';
                                        var isDisabled = status.disabled ?
                                            'disabled' :
                                            ''; // Disable option if status is disabled

                                        $('#status').append(
                                            '<option value="' + status.id +
                                            '" ' + isSelected + ' ' +
                                            isDisabled + '>' + status
                                            .displayname + '</option>'
                                        );
                                    });

                                    // Reinitialize Select2 after adding options
                                    $('#status').select2();

                                    // Show the modal
                                    $('#editSubtaskModal').modal('show');
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'There was an issue fetching the subtask data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Handle the save changes button in the modal
            $('#saveChangesBtn').on('click', function() {
                var subtaskId = $('#subtaskIdInput').val(); // Get the subtask ID from the hidden input
                var dueDate = $('#due_date_sub').val();
                var status = $('#status').val();
                var comment = $('#comment_sub').val();
                // alert(comment);
                $.ajax({
                    url: '{{ route('subtask.update', ['subtask' => '__subtaskId__']) }}'.replace(
                        '__subtaskId__', subtaskId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        due_date: dueDate,
                        status: status,
                        comment: comment
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Success!',
                                'The subtask has been updated.',
                                'success'
                            );
                            $('#editSubtaskModal').modal('hide'); // Hide the modal
                            location.reload(); // Reload the page to see changes
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue updating the subtask.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'There was an issue saving the changes.',
                            'error'
                        );
                    }
                });
            });
        });








        $(document).on('click', '.remove-user-btn', function(e) {
            e.preventDefault();

            var subtaskId = $(this).data('subtask-id');
            var userId = $(this).data('user-id');


            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to remove this user from the task!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove user!',
                cancelButtonText: 'No, keep user',
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to remove user from task
                    $.ajax({
                        url: '{{ route('subtask.removeUser', ['subtask' => '__subtaskId__']) }}'
                            .replace('__subtaskId__', subtaskId),
                        type: 'DELETE',
                        data: {
                            user_id: userId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Removed!',
                                    'The user has been removed from the task.',
                                    'success'
                                );
                                // Reload the page after success
                                location.reload();
                            }
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                'There was a problem removing the user.',
                                'error'
                            );
                        }
                    });
                }
            });
        });


        // $(document).ready(function() {
        //     // Reopen button click event
        //     $('.reopen-btn').click(function(e) {
        //         e.preventDefault();
        //         let subtaskId = $(this).data('subtask-id');

        //         // SweetAlert confirmation
        //         Swal.fire({
        //             title: 'Are you sure?',
        //             text: "Do you want to reopen this task?",
        //             icon: 'warning',
        //             showCancelButton: true,
        //             confirmButtonText: 'Yes, reopen!',
        //             cancelButtonText: 'No, keep it closed'
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 // Send AJAX request to update status to 1 (Reopened)
        //                 $.ajax({
        //                     url: '{{ route('subtask.reopen', '__subtaskId__') }}'.replace(
        //                         '__subtaskId__', subtaskId
        //                     ), // Correct dynamic URL replacement
        //                     method: 'POST',
        //                     data: {
        //                         _token: "{{ csrf_token() }}", // CSRF token for security
        //                         status: 1
        //                     },
        //                     success: function(response) {
        //                         if (response.success) {
        //                             Swal.fire(
        //                                 'Reopened!',
        //                                 'The task has been reopened successfully.',
        //                                 'success'
        //                             );

        //                             location.reload();
        //                             // Optionally, update the UI to reflect the status change
        //                             // $(this).closest('tr').find('.status-column').text('Reopened');
        //                         } else {
        //                             Swal.fire(
        //                                 'Error!',
        //                                 'There was an issue reopening the task.',
        //                                 'error'
        //                             );
        //                         }
        //                     },
        //                     error: function() {
        //                         Swal.fire(
        //                             'Error!',
        //                             'An error occurred while trying to reopen the task.',
        //                             'error'
        //                         );
        //                     }
        //                 });
        //             }
        //         });
        //     });
        // });

        $(document).ready(function() {
            // Reopen button click event
            $('.reopen-btn').click(function(e) {
                e.preventDefault();
                let subtaskId = $(this).data('subtask-id');

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to reopen this task?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reopen!',
                    cancelButtonText: 'No, keep it closed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open modal to input reason
                        $('#reasonModal').modal('show');
                        $('#submitReopenReason').off('click').on('click', function() {
                            let reason = $('#reopen-reason').val().trim();
                            if (reason === '') {
                                Swal.fire('Error!',
                                    'Please provide a reason to reopen the task.',
                                    'error');
                                return;
                            }

                            // Send AJAX request to update status to 1 (Reopened)
                            $.ajax({
                                url: '{{ route('subtask.reopen', '__subtaskId__') }}'
                                    .replace(
                                        '__subtaskId__', subtaskId
                                    ),
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}", // CSRF token for security
                                    status: 1,
                                    reason: reason
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Reopened!',
                                            'The task has been reopened successfully.',
                                            'success'
                                        );
                                        location.reload();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            'There was an issue reopening the task.',
                                            'error'
                                        );
                                    }
                                },
                                error: function() {
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred while trying to reopen the task.',
                                        'error'
                                    );
                                }
                            });

                            // Close the modal after form submission
                            $('#reasonModal').modal('hide');
                        });
                    }
                });
            });
        });
    </script>




@endsection
