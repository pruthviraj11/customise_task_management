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
    @if ($page_data['form_title'] == 'Add New Task')
        <form action="{{ route('app-task-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        @else
            <form action="{{ route('app-task-update', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
    @endif

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-task-list') }}" class="col-md-2 btn btn-primary float-end">Task
                            List</a>

                        {{-- <h4 class="card-title">{{$page_data['form_title']}}</h4> --}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="title">
                                    Title<span class="red">*</span></label>
                                <input type="text" id="title" class="form-control" placeholder="Enter Task Name"
                                    name="title" value="{{ old('title') ?? ($task != '' ? $task->title : '') }}">
                                <span class="text-danger">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>




                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="subject">
                                    Subject</label>
                                <input type="text" id="subject" class="form-control" placeholder="Enter subject"
                                    name="subject" value="{{ old('subject') ?? ($task != '' ? $task->subject : '') }}">
                                <span class="text-danger">
                                    @error('subject')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="project_id">Project</label>
                                <select id="project_id" class="form-select select2" name="project_id">
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

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="department_id">Department</label>
                                <select id="department_id" class="form-select select2" name="department_id">
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

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="sub_department_id">Sub Department</label>
                                <select id="sub_department_id" class="form-select select2" name="sub_department_id">
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
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="user_id">Assign To</label>
                                <select id="user_id" class="form-select select2" name="user_id[]" multiple>
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
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="start_date">Start Date</label>
                                <input type="date" id="start_date" class="form-control" name="start_date"
                                    value="{{ old('start_date') ?? ($task != '' ? $task->start_date : '') }}">
                                <span class="text-danger">
                                    @error('start_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="due_date">End Date</label>
                                <input type="date" id="due_date" class="form-control" name="due_date"
                                    value="{{ old('due_date') ?? ($task != '' ? $task->due_date : '') }}">
                                <span class="text-danger">
                                    @error('due_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="priority_id">Priority</label>
                                <select id="priority_id" class="form-select select2" name="priority_id">
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



                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="task_status">Status</label>
                                <select id="task_status" class="form-select select2" name="task_status">
                                    <option value="">Select Status</option>
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
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="attachments">Attachments</label>
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" id="attachments" name="attachments[]"
                                        multiple>
                                    <label class="input-group-text btn btn-info" for="attachments">+ Choose</label>
                                </div>
                                @if ($task)
                                    <ul>
                                        @foreach ($task->attachments as $attachment)
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
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" class="form-control" placeholder="Enter Description" name="description">{{ old('description') ?? ($task != '' ? $task->description : '') }}</textarea>
                                <span class="text-danger">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            {{-- <div class="col-sm-12">
                                <label class="form-label" for="description">Description</label>
                                <input type="hidden" name="description" id="description" value="">
                                <div id="full-wrapper">
                                    <div id="full-container">
                                        <div class="editor" id="editor" style="min-height: 260px !important;">
                                            {{ old('description') ?? ($task != '' ? $task->description : '') }}
                                        </div>
                                    </div>
                                </div>
                            </div> --}}


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
    {{-- <script>
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
    </script> --}}
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
@endsection
