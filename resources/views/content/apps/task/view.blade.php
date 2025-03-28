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
                <div class="card" style="background-color: #F6F6F6">
                    <div class="card-header">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <button class="btn btn-primary" onclick="printDiv('printableArea')">Print</button>
                        <a href="{{ route('app-task-list') }}" class="col-md-2 btn btn-primary float-end">Task
                            List</a>

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
                            </div>
                            <div class="col-md-6  d-md-block d-flex col-sm-12 ">
                                @if ($task != '')
                                    <a class=" btn-sm btn-primary me-1"> Task # {{ $task->id }}</a>
                                    <a class=" btn-sm btn-primary me-1"> Task Created By {{ $task->creator->first_name }}
                                        {{ $task->creator->last_name }}</a>
                                @endif
                                @if ($hasAcceptedTask)
                                    @if ($task->created_by == auth()->user()->id)
                                        @php $encrypted_id = encrypt($task->id) @endphp
                                    @else
                                        @php $encrypted_id = encrypt($task->task_id) @endphp
                                    @endif
                                    {{-- Button to edit the task if accepted --}}
                                    <a data-bs-toggle='tooltip' data-bs-placement='top' title='Update Task'
                                        class='btn-sm btn-warning me-1' href="{{ route('app-task-edit', $encrypted_id) }}"
                                        target='_blank'>
                                        <i class='ficon' data-feather='edit'></i>
                                    </a>
                                @else
                                    {{-- Button to go back to task list if not accepted --}}
                                    <a data-bs-toggle='tooltip' data-bs-placement='top' title='Go to Task List'
                                        class='btn-sm btn-secondary me-1' href="{{ route('app-task-requested') }}">
                                        <i class='ficon' data-feather='list'></i>
                                    </a>
                                @endif

                            </div>

                            <div class="col-md-6 col-sm-12 mb-1 mt-1">
                                <label class="form-label" for="title">
                                    Title<span class="red">*</span></label>
                                <input readonly type="text" id="title" class="form-control"
                                    placeholder="Enter Task Name" name="title"
                                    value="{{ old('title') ?? ($task != '' ? $task->title : '') }}">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>





                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="subject">
                                    Subject</label>
                                <input readonly type="text" id="subject" class="form-control"
                                    placeholder="Enter subject" name="subject"
                                    value="{{ old('subject') ?? ($task != '' ? $task->subject : '') }}">
                                <span class="text-danger">
                                    @error('subject')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="project_id">Project</label>
                                <select disabled id="project_id" class="form-select select2" name="project_id">
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

                            {{-- <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="user_id">Assign To</label>

                                <select disabled id="user_id" class="form-select select2" name="user_id[]" multiple>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') ? (in_array(old('user_id')[$loop->index] == $user->id) ? 'selected' : '') : ($task ? (in_array($user->id, $task->users->pluck('id')->toArray()) ? 'selected' : '') : '') }}>
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('user_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div> --}}
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
                                {{-- {{ dd($task->users) }} --}}
                                <select disabled id="user_id" class="form-select select2" name="user_id[]" multiple
                                    required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') && in_array($user->id, old('user_id')) ? 'selected' : ($taskAssigne && $taskAssigne->users->pluck('id')->contains($user->id) ? 'selected' : '') }}>
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
                            <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="start_date">Start Date</label>
                                <input readonly type="date" id="start_date" class="form-control" name="start_date"
                                    value="{{ old('start_date') ?? ($task != '' ? $task->start_date : '') }}">
                                <span class="text-danger">
                                    @error('start_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="due_date">End Date</label>
                                <input readonly type="date" id="due_date" class="form-control" name="due_date"
                                    value="{{ old('due_date') ?? ($task != '' ? $task->due_date : '') }}">
                                <span class="text-danger">
                                    @error('due_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="priority_id">Priority</label>
                                <select disabled id="priority_id" class="form-select select2" name="priority_id">
                                    <option value="">Select Priority</option>
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
                            {{-- <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="department_id">Department</label>
                                <select disabled id="department_id" class="form-select select2" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : ($task ? ($task->department_id == $department->id ? 'selected' : '') : '') }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    @error('department_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="sub_department_id">Sub Department</label>
                                <select disabled id="sub_department_id" class="form-select select2"
                                    name="sub_department_id">
                                    <option value="">Select Sub Department</option>
                                    @if ($task)
                                        @foreach ($Subdepartments as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('sub_department_id') == $data->id ? 'selected' : ($task ? ($task->sub_department_id == $data->id ? 'selected' : '') : '') }}>
                                                {{ $data->sub_department_name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                                <span class="text-danger">
                                    @error('sub_department_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div> --}}


                            <div class="col-md-4 col-sm-12 mb-1">
                                <label class="form-label" for="task_status">Status</label>
                                <select disabled id="task_status" class="form-select select2" name="task_status">
                                    <option value="">Select Sub Department</option>
                                    @foreach ($Status as $Statu)
                                        <option value="{{ $Statu->id }}"
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
                            {{-- {{dd($taskAssigne->completed_date,$task->completed_date)}} --}}
                            @if ($task->completed_date)
                                <div class="col-md-4 col-sm-12 mb-1">
                                    <label class="form-label" for="completed_dates">Completed Date</label>
                                    <input readonly type="date" id="completed_dates" class="form-control"
                                        name="completed_dates"
                                        value="{{ old('completed_dates') ?? ($task && $task->completed_date ? \Carbon\Carbon::parse($task->completed_date)->format('Y-m-d') : ($taskAssigne && $taskAssigne->completed_date ? \Carbon\Carbon::parse($taskAssigne->completed_date)->format('Y-m-d') : '')) }}">

                                    <span class="text-danger">
                                        @error('completed_dates')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                            @endif
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="attachments">Attachments</label>
                                {{-- <label class="form-label" for="attachments">Attachments</label>
                                <div class="input-group mb-3">
                                    <input readonly type="file" class="form-control" id="attachments"
                                        name="attachments[]" multiple>
                                    <label class="input-group-text btn btn-info" for="attachments">+ Choose</label>
                                </div> --}}
                                @if ($taskAssigne)
                                    <ul>
                                        @foreach ($taskAssigne->attachments as $attachment)
                                            <li><a target="_blank"
                                                    href="{{ Storage::url('app/' . $attachment->file) }}">{{ last(explode('/', $attachment->file)) }}</a>
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
                                <label class="form-label" for="description">Description</label> <br>
                                {{ old('description') ?? ($task != '' ? $task->description : '') }}
                                {{-- <textarea id="description" class="form-control" placeholder="Enter Description" name="description">{{ old('description') ?? ($task != '' ? $task->description : '') }}</textarea> --}}
                                <span class="text-danger">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="rating">Rating</label> <br>
                                {{ old('rating') ?? ($task != '' ? $task->rating : '') }}
                                <span class="text-danger">
                                    @error('rating')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-12 col-sm-12 mt-3">
                                <form action="{{ route('comments.store') }}" method="POST">
                                    @csrf
                                    @if ($creator == 1)
                                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                                    @else
                                        <input type="hidden" name="task_id" value="{{ $task->task_id }}">
                                        <input type="hidden" name="task_created_by" value="{{ $task->created_by }}">
                                    @endif

                                    <div class="mb-3">
                                        <label for="comment_form" class="form-label">Add Comment</label>
                                        <textarea class="form-control" id="comment_form" name="comment_form" rows="4"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>

                            @if ($task->created_by == auth()->user()->id)
                                <div class="col-md-6 col-sm-12 mb-1">
                                    <label class="form-label" for="comments_for">Comments For </label><span
                                        class="red">*</span>
                                    <select id="comments_for" class="form-select select2" name="comments_for[]" multiple
                                        required>
                                        <option value="">Select User</option>
                                        @foreach ($users as $user)
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
                                @foreach ($taskAssigne->comments as $comment)
                                    @php
                                        // Get the logged-in user ID
                                        $loggedInUserId = auth()->id();

                                        // Split the comma-separated list of users to whom the comment is directed
                                        $toUserIds = explode(',', $comment->to_user_id); // if comma-separated IDs are stored
                                        // dump($toUserIds);
                                    @endphp

                                    {{-- Check if the logged-in user can view the comment --}}
                                    @if (
                                        $loggedInUserId == $comment->created_by || // Show for comment creator
                                            in_array($loggedInUserId, $toUserIds) || // Show for users the comment is directed to
                                            $loggedInUserId == $task->created_by)
                                        {{-- // Show for task creator --}}
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
                                                        {{-- Check if the logged-in user is the comment creator, or show the creator --}}
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
                                    @endif
                                @endforeach
                            </div>






                        </div>
                    </div>
                </div>







            </div>
        </div>
        </div>
        </div>
        <div class="card" class="d-none" style="display: none">
            <div class="card-body" id="printableArea">

                <div class="text-start mb-3">
                    {{-- @php $base64 = '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';
                        @endphp --}}
                    <img style="max-width: 160px;"
                        src='data:image/png;base64,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';'
                        alt="Logo" style="max-width: 150px; height: auto">
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Task/Ticket Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Task Type</th>
                            <td>
                                {{ $task != '' && $task->ticket == 1 ? 'Ticket' : 'Task' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Title <span class="red">*</span></th>
                            <td>
                                {{ old('title') ?? ($task != '' ? $task->title : '') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                {{ old('subject') ?? ($task != '' ? $task->subject : '') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Project</th>
                            <td>
                                @foreach ($projects as $project)
                                    @if (old('project_id') == $project->id || ($task && $task->project_id == $project->id))
                                        {{ $project->project_name }}
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Assign To <span class="red">*</span></th>
                            <td>
                                @foreach ($users as $user)
                                    @if (
                                        (old('user_id') && in_array($user->id, old('user_id'))) ||
                                            ($taskAssigne && $taskAssigne->users->pluck('id')->contains($user->id)))
                                        {{ $user->first_name }} {{ $user->last_name }} |
                                        {{ $user->department->department_name ?? '' }} |
                                        {{ $user->sub_department->sub_department_name ?? '' }}<br>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>
                                {{ old('start_date') ?? ($task != '' ? $task->start_date : '') }}
                            </td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>
                                {{ old('due_date') ?? ($task != '' ? $task->due_date : '') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>
                                @foreach ($Prioritys as $Priority)
                                    @if (old('priority_id') == $Priority->id || ($task && $task->priority_id == $Priority->id))
                                        {{ $Priority->displayname }}
                                    @endif
                                @endforeach
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                @foreach ($Status as $Statu)
                                    @if (old('task_status') == $Statu->id || ($task && $task->task_status == $Statu->id))
                                        {{ $Statu->displayname }}
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        @if ($task->completed_date)
                            <tr>
                                <th>Completed Date</th>
                                <td>
                                    {{ old('completed_dates') ?? ($task != '' ? $task->completed_date : '') }}
                                </td>
                            </tr>
                        @endif
                        @if ($taskAssigne && $taskAssigne->attachments->count() > 0)


                            <tr>
                                <th>Attachments</th>
                                <td>
                                    @if ($taskAssigne)
                                        <ul>
                                            @foreach ($taskAssigne->attachments as $attachment)
                                                <li>
                                                    <a target="_blank"
                                                        href="{{ Storage::url('app/' . $attachment->file) }}">
                                                        {{ last(explode('/', $attachment->file)) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @endif

                        @if ($task->description)
                            <tr>
                                <th>Description</th>
                                <td>
                                    {{ old('description') ?? ($task != '' ? $task->description : '') }}
                                </td>
                            </tr>
                        @endif
                        @if ($task->rating)
                            <tr>
                                <th>Rating</th>
                                <td>
                                    {{ old('rating') ?? ($task != '' ? $task->rating : '') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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
        function printDiv(divId) {
            const printContents = document.getElementById(divId).innerHTML;
            const originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            // Restore the original page
            window.location.reload();
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
