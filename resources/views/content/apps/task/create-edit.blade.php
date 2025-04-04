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
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.rateyo.min.css')) }}">
    {{-- <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-ratings.css')) }}"> --}}

@endsection

@section('page-style')
    {{-- Page Css files --}}
@endsection



@section('content')
    @if ($page_data['form_title'] == 'Add New Task')
        <form action="{{ route('app-task-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-task-update', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif
    @php
        if ($page_data['form_title'] == 'Add New Task') {
            // If the page title is 'Add New Task', grant access unconditionally
            $isCreator = true;
        } else {
            // Check if the user is the creator of the task, or if the user has ID 1 (admin)
            $isCreator = auth()->user()->id == 1 || ($task && $task->creator->id == auth()->user()->id);
        }

    @endphp
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
                                @if ($page_data['form_title'] == 'Add New Task')
                                    <div class="form-check m-2 form-check-success">
                                        <input type="checkbox" class="form-check-input" id="recurring" name="recurring"
                                            value="1"
                                            {{ old('recurring') == '1' || ($task != '' && $task->recurring == 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recurring">Recurring</label>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                @if ($task != '')
                                    <a class=" btn-sm btn-primary "> Task # {{ $task->id }}</a>
                                    <a class=" btn-sm btn-primary "> Task Created By {{ $task->creator->first_name }}
                                        {{ $task->creator->last_name }}</a>
                                    <a class=" btn-sm btn-primary "> Task Created At : {{ $task->created_at }}</a>
                                @endif

                            </div>
                            <div class="col-md-6 col-sm-12 mb-1 mt-1">
                                <label class="form-label" for="title">
                                    Title<span class="red">*</span>
                                </label>
                                <input type="text" id="title" class="form-control" placeholder="Enter Task Name"
                                    name="title" value="{{ old('title') ?? ($task != '' ? $task->title : '') }}"
                                    @if ($isCreator == false) readonly @endif required>
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1 mt-1">
                                <label class="form-label" for="subject">
                                    Subject<span class="red">*</span>
                                </label>
                                <input type="text" id="subject" class="form-control" placeholder="Enter subject"
                                    name="subject" value="{{ old('subject') ?? ($task != '' ? $task->subject : '') }}"
                                    @if ($isCreator == false) readonly @endif required>
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
                                <select id="project_id" class="form-select select2" name="project_id"
                                    @if ($isCreator == false) disabled @endif required>
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
                                <label class="form-label" for="user_id">Assign To</label><span class="red">*</span>
                                <select id="user_id" class="form-select select2" name="user_id[]" multiple
                                    @if ($isCreator == false) disabled @endif required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') && in_array($user->id, old('user_id')) ? 'selected' : ($task && $task->users->pluck('id')->contains($user->id) ? 'selected' : '') }}>
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





                            {{-- <div class="col-md-3 col-sm-12 mb-1 position-relative">
                                <label class="form-label" for="start_date">Start Date</label><span
                                    class="red">*</span>
                                <input type="date" id="start_date" class="form-control" name="start_date"
                                    value="{{ old('start_date') ?? ($task != '' ? $task->start_date : date('Y-m-d')) }}"
                                    @if ($isCreator == false) readonly @endif required>
                                <span class="text-danger">
                                    @error('start_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-3 col-sm-12 mb-1" id="end_date_container">
                                <label class="form-label" for="due_date_form">End Date</label><span
                                    class="red">*</span>
                                <input type="date" id="due_date_form" class="form-control" name="due_date_form"
                                    value="{{ old('due_date_form') ?? ($task != '' ? $task->due_date : date('Y-m-d')) }}"
                                    required>
                                <span class="text-danger">
                                    @error('due_date_form')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div> --}}
                            <div class="col-md-3 col-sm-12 mb-1 position-relative">
                                <label class="form-label" for="start_date">Start Date</label><span
                                    class="red">*</span>
                                <input type="date" id="start_date" class="form-control" name="start_date"
                                    value="{{ old('start_date') ?? ($task != '' ? $task->start_date : date('Y-m-d')) }}"
                                    @if ($isCreator == false) readonly @endif required>
                                <span class="text-danger">
                                    @error('start_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-3 col-sm-12 mb-1" id="end_date_container">
                                <label class="form-label" for="due_date_form">End Date</label><span
                                    class="red">*</span>
                                <input type="date" id="due_date_form" class="form-control" name="due_date_form"
                                    value="{{ old('due_date_form') ?? ($task != '' ? $task->due_date : date('Y-m-d')) }}"
                                    required>

                                <span class="text-danger">
                                    @error('due_date_form')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div id="recurring_options" style="display: none;" class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="recurring_type">Recurring Type</label>
                                <select id="recurring_type" class="form-control select2" name="recurring_type">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="half_quarterly">Half Yearly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>

                            <div id="number_of_time_container" style="display: none;" class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="number_of_time">Number of Times</label>
                                <input type="number" id="number_of_time" class="form-control" name="number_of_time"
                                    placeholder="Enter number of days">
                            </div>

                            <div class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="priority_id">Priority</label><span class="red">*</span>
                                <select id="priority_id" class="form-select select2" name="priority_id"
                                    @if ($isCreator == false) disabled @endif required>
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


{{-- {{dd($task)}} --}}


                            <div class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="task_status">Status</label><span class="red">*</span>
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
                                        @if ($isCreator == false) disabled @endif multiple>
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
                            </div>

                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" class="form-control" placeholder="Enter Description" name="description"
                                    @if ($isCreator == false) disabled @endif>{{ old('description') ?? ($task != '' ? html_entity_decode($task->description) : '') }}</textarea>
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
                                            multiple @if ($isCreator == false) disabled @endif required>
                                            <option value="">Select User</option>
                                            @foreach ($task->users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('comments_for') && in_array($user->id, old('comments_for')) ? 'selected' : ($task && $task->users->pluck('id')->contains($user->id) ? 'selected' : '') }}>
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

                                <div class="col-12 mt-3" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($getTaskComments as $comment)
                                     @if ($comment->creator)
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
                                                            {{-- {{ $loggedInUserId == $comment->created_by
                                                                ? auth()->user()->first_name
                                                                : optional($comment->creator)->first_name }} --}}
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
                                        @endif
                                    @endforeach
                                </div>




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

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <!-- Card Body -->
                    <div class="card-body">
                        <h2>Sub tasks</h2>
                        @if ($SubTaskData == [])
                            <p>No subtasks found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mx-auto" id="sub_tasks_list">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Task Number</th>
                                            <th>Assigned To</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($SubTaskData as $subtask)
                                            <tr>
                                                <td>
                                                    <!-- Button to trigger edit action -->
                                                    <a class="btn btn-primary btn-sm edit-btn"
                                                        data-subtask-id="{{ $subtask->id }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Edit Subtask">
                                                        <i class="feather-icon" data-feather="edit"></i>
                                                    </a>


                                                    @if (auth()->user()->can('task-reassign') ||
                                                            (Auth::user()->id === $subtask->created_by && !in_array($subtask->task_status, [7, 4])))
                                                        <a class="btn btn-warning btn-sm reassign-btn"
                                                            data-subtask-id="{{ $subtask->id }}"
                                                            data-bs-toggle="tooltip"
                                                            data-old-user-id="{{ $subtask->user_id }}"
                                                            data-bs-placement="top" title="Reassign Task"
                                                            data-bs-target="#reassignTaskModal" data-bs-toggle="modal">
                                                            <i class="feather-icon" data-feather="users"></i>
                                                        </a>
                                                    @endif
                                                    @if (in_array($subtask->task_status, [7]) && !isset($subtask->rating))
                                                        <a class="btn btn-primary btn-sm feedback-btn"
                                                            data-subtask-id="{{ $subtask->id }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Feedback & Ratings">
                                                            <i class="feather-icon" data-feather="message-circle"></i>
                                                        </a>
                                                    @endif
                                                    {{-- {{ dd($subtask->id); }} --}}
                                                    {{-- <!-- Button to trigger AJAX request to mark as completed -->
                                                    <a class="btn btn-success btn-sm mark-completed-btn"
                                                        data-subtask-id="{{ $subtask->id }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Mark as Completed">
                                                        <i class="feather-icon" data-feather="check-circle"></i>
                                                    </a> --}}
                                                    <!-- Button to reopen the task when status is 7 or 4 -->
                                                    {{-- @if (in_array($subtask->task_status, [7, 4]))
                                                        <a class="btn btn-warning btn-sm reopen-btn"
                                                            data-subtask-id="{{ $subtask->id }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Reopen Task">
                                                            <i class="feather-icon" data-feather="refresh-cw"></i>
                                                        </a>
                                                    @endif --}}


                                                    @if (in_array($subtask->task_status, [7, 4]))
                                                        <a class="btn btn-warning btn-sm reopen-btn"
                                                            data-subtask-id="{{ $subtask->id }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Reopen Task">
                                                            <i class="feather-icon" data-feather="refresh-cw"></i>
                                                        </a>
                                                    @endif
                                                    <!-- Button to remove user from task (only visible to creator) -->
                                                    @if (Auth::user()->id === $subtask->created_by)
                                                        <a class="btn btn-danger btn-sm remove-user-btn"
                                                            data-subtask-id="{{ $subtask->id }}"
                                                            data-user-id="{{ $subtask->user->id }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Remove User">
                                                            <i class="feather-icon" data-feather="user-x"></i>

                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $subtask->task_number }}</td>
                                                <td>{{ $subtask->user->first_name . ' ' . $subtask->user->last_name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($subtask->due_date)->format('d/m/Y') }}</td>
                                                <td>{{ $subtask->taskStatus->displayname ?? '' }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>


                    </div>
                    @endif
                </div>
            </div>
            <div class="modal fade" id="editSubtaskModal" tabindex="-1" aria-labelledby="editSubtaskModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubtaskModalLabel">Edit Subtask</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Due Date -->
                            <input type="hidden" id="subtaskIdInput" value="">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date_sub" name="due_date">
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <!-- Status options will be populated dynamically -->
                                </select>
                            </div>

                            <!-- Comment -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment_sub" name="comment" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="reasonModal" tabindex="-1" aria-labelledby="reasonModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reasonModalLabel">Enter Reopen Reason</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea class="form-control" name="reason" id="reopen-reason"
                                placeholder="Enter the reason for reopening the task" rows="4"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="submitReopenReason">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="reassignTaskModal" tabindex="-1" aria-labelledby="reassignTaskModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reassignTaskModalLabel">Reassign Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="reassignTaskForm">
                                <input type="hidden" name="subtask_id" id="reassignSubtaskId">
                                <input type="hidden" id="olduserId" name="olduserId">

                                <div class="mb-3">
                                    <label for="re_assign_to" class="form-label">Re Assign To</label>
                                    <select class="form-select" name="re_assign_to" id="re_assign_to" required>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') && in_array($user->id, old('user_id')) ? 'selected' : ($task && $task->users->pluck('id')->contains($user->id) ? 'selected' : '') }}>
                                                <span class="fixed-width">{{ $user->first_name }}
                                                    {{ $user->last_name }}</span>
                                                |{{ $user->department->department_name ?? '' }}|
                                                {{ $user->sub_department->sub_department_name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div>
                                <button type="button" class="btn btn-primary" id="saveReassignTask">Reassign
                                    Task</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog"
                aria-labelledby="feedbackModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="feedbackModalLabel">Feedback & Ratings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="feedbackForm">

                                <h4>*Please note: Feedback and ratings cannot be changed once submitted.</h4>
                                <input type="hidden" id="subtaskId" name="subtask_id">
                                <div class="mb-3">
                                    <label for="feedback_text" class="form-label">Feedback</label>
                                    <textarea id="feedback_text" class="form-control" rows="3"></textarea>
                                </div>
                                {{-- <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <input type="number" id="rating" class="form-control" min="1"
                                    max="5">

                                    <div class="col-md d-flex flex-column align-items-start">
                                        <p class="card-text fw-semibold mb-25">onChange Event</p>
                                        <div class="onChange-event-ratings"></div>
                                        <div class="counter-wrapper mt-1">
                                            <strong>Ratings:</strong>
                                            <span class="counter"></span>
                                        </div>
                                    </div>

                            </div> --}}
                                <div class="col-xl-6 col-12">
                                    <div class="col-md d-flex flex-column align-items-start mb-sm-0 mb-1">
                                        {{-- <p class="card-text fw-semibold mb-25">onSet Event</p> --}}
                                        <label for="rating" class="form-label">Rating</label>

                                        <div class="onset-event-ratings" id="rating" data-rateyo-half-star="true">
                                        </div>
                                        <input type="hidden" id="eventRating" name="eventRating" value="">
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveFeedback">Save Feedback</button>
                        </div>
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
    <script src="{{ asset(mix('vendors/js/extensions/jquery.rateyo.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-ratings.js')) }}"></script>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            $('#sub_tasks_list').DataTable({
                responsive: true, // Enables responsive design
                autoWidth: true, // Prevents fixed column widths
                columnDefs: [{
                        orderable: false,
                        targets: -1
                    }, // Makes the last column non-sortable
                ],
                language: {
                    search: "Search Tasks:", // Customizes the search input placeholder
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                },
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var onSetEvents = $(".onset-event-ratings");

            if (onSetEvents.length) {
                onSetEvents.rateYo({
                    rtl: false, // Adjust based on your layout
                    halfStar: true,
                    onSet: function(rating) {
                        $("#eventRating").val(rating); // Save the rating to the hidden input field
                    }
                });
            }
        });
    </script>
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
    document.addEventListener("DOMContentLoaded", function () {
    const recurringCheckbox = document.getElementById("recurring");
    const taskStatusSelect = document.getElementById("task_status");

    function toggleClosedOption() {
        const closedOption = Array.from(taskStatusSelect.options).find(option => option.text.toLowerCase() === "closed");

        if (recurringCheckbox.checked) {
            if (closedOption) closedOption.remove(); // Remove the "Closed" option
        } else {
            // Re-add the "Closed" option if not present
            if (!closedOption) {
                const newOption = document.createElement("option");
                newOption.value = "7"; // Replace with the actual ID of "Closed"
                newOption.text = "Closed";
                taskStatusSelect.appendChild(newOption);
            }
        }
    }

    // Run on page load
    toggleClosedOption();

    // Add event listener
    recurringCheckbox.addEventListener("change", toggleClosedOption);
});
</script>
    {{-- <script>
        $(document).ready(function() {
            // Initialize RateYo
            $("#starRating").rateYo({
                rating: 0, // Default rating
                fullStar: true, // Enable full-star ratings
                starWidth: "30px", // Star size
                ratedFill: "#f39c12", // Filled star color
                normalFill: "#gray", // Empty star color
                onSet: function(rating) {
                    // Update the hidden input value when a rating is selected
                    $("#rating").val(rating);
                }
            });
        });
    </script> --}}

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Handle Feedback and Ratings button click
            $('.reassign-btn').on('click', function() {
                var subtaskId = $(this).data('subtask-id'); // Get the subtask ID
                var olduserId = $(this).data('old-user-id');
                $('#subtaskId').val(subtaskId);
                $('#olduserId').val(olduserId);
                $('#reassignTaskModal').modal('show');
                // Show SweetAlert confirmation before proceeding

            });
        });
    </script>

    <script>
        $('#saveReassignTask').on('click', function() {
            var reAssignTo = $('#re_assign_to').val();
            var subtaskId = $('#subtaskId').val();
            var olduserId = $('#olduserId').val();
            $.ajax({
                url: '{{ route('subtask.saveReAssignTo') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add CSRF token
                    subtask_id: subtaskId,
                    reAssignTo: reAssignTo,
                    olduserId: olduserId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            'User Assigned Successfully.',
                            'success'
                        );
                        $('#reassignTaskModal').modal('hide');
                    }
                    location.reload(); // Reload the page to see changes

                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'There was an issue Assigning to user.',
                        'error'
                    );
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Handle Feedback and Ratings button click
            $('.feedback-btn').on('click', function() {
                var subtaskId = $(this).data('subtask-id'); // Get the subtask ID

                // Show SweetAlert confirmation before proceeding
                Swal.fire({
                    title: 'Provide Feedback and Ratings',
                    text: 'You are about to give feedback for this subtask.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Proceed',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#subtaskId').val(subtaskId);
                        $('#feedbackModal').modal('show');

                    }
                });
            });
        });
    </script>

    <script>
        $('#saveFeedback').on('click', function() {
            var feedbackText = $('#feedback_text').val();
            var rating = $('#eventRating').val();
            var subtaskId = $('#subtaskId').val(); // Assuming you set the subtask ID to the modal
            $.ajax({
                url: '{{ route('subtask.saveFeedback') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add CSRF token
                    subtask_id: subtaskId,
                    feedback: feedbackText,
                    rating: rating
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            'Your feedback has been saved.',
                            'success'
                        );
                        $('#feedbackModal').modal('hide');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'There was an issue saving your feedback.',
                        'error'
                    );
                }
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

    <script>
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
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var startDateInput = document.getElementById('start_date');
            var dueDateInput = document.getElementById('due_date_form');

            // Function to format date as 'YYYY-MM-DD'
            function formatDate(date) {
                return date.toISOString().split('T')[0];
            }

            var today = new Date();

            // Check if the page is "Add New Task"
            var isAddNewTask = <?php echo json_encode($page_data['form_title'] == 'Add New Task'); ?>;

            if (isAddNewTask) {
                // Apply start date validation only if page is "Add New Task"
                var oneWeekAgo = new Date();
                oneWeekAgo.setDate(today.getDate() - 7);
                startDateInput.min = formatDate(oneWeekAgo);
            }

            // Set initial minimum value for due date
            // if (startDateInput.value) {
            //     dueDateInput.min = startDateInput.value;
            // }

            // Update due date's minimum when start date changes
            startDateInput.addEventListener('change', function() {
                var selectedStartDate = new Date(this.value);
                dueDateInput.min = formatDate(selectedStartDate); // Update min value
                dueDateInput.value = ''; // Clear due date if start date changes
            });

            // Ensure due date cannot be less than start date on load
            if (dueDateInput.value && new Date(dueDateInput.value) < new Date(startDateInput.value)) {
                dueDateInput.value = ''; // Clear invalid value
            }
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






        $(document).ready(function() {
            // Initialize the RateYo rating system
            $(".onset-event-ratings").rateYo({
                halfStar: true,
                onSet: function(rating, rateYoInstance) {
                    // Set the value of the hidden input field to the selected rating
                    $('#eventRating').val(rating);
                }
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get recurring checkbox and related containers
            const recurringCheckbox = document.getElementById('recurring');
            const endDateContainer = document.getElementById('end_date_container');
            const endDateInput = document.getElementById('due_date_form');
            const recurringOptionsContainer = document.getElementById('recurring_options');
            const numberOfDaysContainer = document.getElementById('number_of_time_container');

            // Initially check the state of the "Recurring" checkbox
            if (recurringCheckbox.checked) {
                endDateContainer.style.display = 'none'; // Hide End Date
                endDateInput.removeAttribute('required');
                recurringOptionsContainer.style.display = 'block'; // Show Recurring options
                numberOfDaysContainer.style.display = 'block'; // Show Number of Days input
            }

            // Toggle End Date visibility based on Recurring checkbox
            recurringCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    endDateContainer.style.display = 'none'; // Hide End Date when Recurring is selected
                    endDateInput.removeAttribute('required'); // Remove required attribute
                    recurringOptionsContainer.style.display = 'block'; // Show Recurring options
                    numberOfDaysContainer.style.display = 'block'; // Show Number of Days input
                } else {
                    endDateContainer.style.display = 'block'; // Show End Date if not recurring
                    endDateInput.setAttribute('required', 'required'); // Add required attribute
                    recurringOptionsContainer.style.display = 'none'; // Hide Recurring options
                    numberOfDaysContainer.style.display = 'none'; // Hide Number of Days input
                }
            });
        });
    </script>



@endsection
