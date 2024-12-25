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
@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection



@section('content')


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
                                    <input type="text"  readonly id="title" class="form-control" placeholder="Enter Task Name"
                                        name="title" value="{{ old('title') ?? ($task != '' ? $task->title : '') }}"
                                        required>
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
                                    <input type="text" readonly id="subject" class="form-control" placeholder="Enter subject"
                                        name="subject" value="{{ old('subject') ?? ($task != '' ? $task->subject : '') }}"
                                        required>
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
                                    <select id="project_id" disabled class="form-select select2" name="project_id" required>
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
                                    <select id="user_id" disabled class="form-select select2" name="user_id[]" multiple
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
                                    <input type="date" readonly id="start_date" class="form-control" name="start_date"
                                        value="{{ old('start_date') ?? ($task != '' ? $task->start_date : date('Y-m-d')) }}"
                                        required>
                                    <span class="text-danger">
                                        @error('start_date')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>



                                <div id="recurring_options" class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="recurring_type">Recurring Type</label>
                                    <select id="recurring_type" disabled class="form-control select2" name="recurring_type">
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
                                    <input type="number" readonly id="number_of_time"
                                        value="{{ old('number_of_days') ?? ($task != '' ? $task->number_of_days : '') }}"
                                        class="form-control" name="number_of_time" placeholder="Enter number of days"
                                        required>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="priority_id">Priority</label><span
                                        class="red">*</span>
                                    <select id="priority_id" disabled class="form-select select2" name="priority_id" required>
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
                                    <select id="task_status" disabled class="form-select select2" name="task_status"
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
                                {{-- <div class="col-md-12 col-sm-12 ">
                                <label class="form-label" for="attachments">Attachments</label>
                                <div class="input-group mb-3 w-100">
                                    <input type="file" class="form-control" id="attachments" name="attachments[]"
                                         multiple>
                                    <label class="input-group-text btn btn-info" for="attachments">+ Choose</label>
                                </div>
                                @if ($task)
                                    <ul>
                                        @foreach ($task->attachments as $attachment)
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
                            </div> --}}

                                <div class="col-md-12 col-sm-12 mb-1">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea id="description" readonly class="form-control" placeholder="Enter Description" name="description">{{ old('description') ?? ($task != '' ? html_entity_decode($task->description) : '') }}</textarea>
                                    <span class="text-danger">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                @if ($task != '')
                                    <div class="col-md-12 col-sm-12 mt-3">

                                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                                        <div class="mb-3">
                                            <label for="comment_form" class="form-label">Add Comment</label>
                                            <textarea class="form-control" id="comment_form" name="comment_form" rows="4"></textarea>
                                        </div>

                                    </div>
                                    @if ($task->created_by == auth()->user()->id)
                                        <div class="col-md-6 col-sm-12 mb-1">
                                            <label class="form-label" for="comments_for">Comments For </label><span
                                                class="red">*</span>
                                            <select id="comments_for" class="form-select select2" name="comments_for[]"
                                                multiple required>
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('comments_for') && in_array($user->id, old('comments_for'))
                                                            ? 'selected'
                                                            : ($task && in_array($user->id, $task->task_assignes ? explode(',', $task->task_assignes) : [])
                                                                ? 'selected'
                                                                : '') }}>
                                                        <span class="fixed-width">{{ $user->first_name }}
                                                            {{ $user->last_name }}</span>
                                                        |{{ $user->department->department_name ?? '' }}|
                                                        {{ $user->sub_department->sub_department_name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">
                                                @error('comments_for')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                    @endif

                                    <?php /*
                                <div class="col-12 mt-3" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($getTaskComments as $comment)
                                        @php
                                            // Get the logged-in user ID
                                            $loggedInUserId = auth()->id();

                                            // Split the comma-separated list of users to whom the comment is directed
                                            $toUserIds = explode(',', $comment->to_user_id); // if comma-separated IDs are stored
                                        @endphp

                                        {{-- Check if the logged-in user can view the comment --}}
                                        <div class="card bg-white shadow-lg">
                                            <div class="card-header email-detail-head">
                                                <div
                                                    class="user-details d-flex justify-content-between align-items-center flex-wrap">
                                                    <div class="avatar me-75">
                                                        {{-- Check if the comment creator is the logged-in user --}}
                                                        @if ($loggedInUserId == $comment->created_by)
                                                            {{-- Display logged-in user's profile image if they are the creator --}}
                                                            @if (!empty(auth()->user()->profile_img))
                                                                <img src="{{ asset('storage/' . auth()->user()->profile_img) }}"
                                                                    alt="Profile Image" width="48" height="48">
                                                            @else
                                                                <img src="http://127.0.0.1:8000/images/avatars/AvtarIMG.png"
                                                                    alt="Default Avatar" width="48" height="48">
                                                            @endif
                                                        @else
                                                            {{-- Display the comment creator's profile image --}}
                                                            @if (!empty($comment->creator->profile_img))
                                                                <img src="{{ asset('storage/' . $comment->creator->profile_img) }}"
                                                                    alt="Profile Image" width="48" height="48">
                                                            @else
                                                                <img src="http://127.0.0.1:8000/images/avatars/AvtarIMG.png"
                                                                    alt="Default Avatar" width="48" height="48">
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="mail-items">
                                                        <h5 class="mt-0">
                                                            {{ $loggedInUserId == $comment->created_by ? auth()->user()->first_name : $comment->creator->first_name }}
                                                        </h5>
                                                        <div class="email-info-dropup dropdown">
                                                            <span role="button"
                                                                class="dropdown-toggle font-small-3 text-muted"
                                                                id="card_top01" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                {{ $loggedInUserId == $comment->created_by ? auth()->user()->email : $comment->creator->email }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mail-meta-item d-flex align-items-center">
                                                    <small
                                                        class="mail-date-time text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </div>
                                            <div class="card-body mail-message-wrapper pt-2">
                                                <div class="mail-message">
                                                    {{ $comment->comment }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                */
                                    ?>
                                @endif


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
    <script>
        $(document).ready(function() {
            $('#department_id').change(function() {
                var departmentId = $(this).val();
                if (departmentId) {
                    $.ajax({
                        url: '{{ route('app-sub-departments', ':department_id') }}'.replace(
                            ':department_id', departmentId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#sub_department_id').empty();
                            $('#sub_department_id').append(
                                '<option value="">Select Sub Department</option>');
                            $.each(data, function(key, value) {
                                $('#sub_department_id').append('<option value="' + value
                                    .id + '">' + value.sub_department_name +
                                    '</option>');
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
@endsection
