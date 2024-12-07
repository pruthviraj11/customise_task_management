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
                        <a href="{{ route('app-task-list') }}" class="col-md-2 btn btn-primary float-end">Task List</a>

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
                            <div class="col-md-6">
                                @if ($task != '')
                                    <a class=" btn-sm btn-primary "> Task # {{ $task->id }}</a>
                                      <a class=" btn-sm btn-primary "> Task Created By {{ $task->creator->first_name }}  {{ $task->creator->last_name }}</a>
                                @endif

                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="title">
                                    Title<span class="red">*</span>
                                </label>
                                <input type="text" id="title" class="form-control" placeholder="Enter Task Name"
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
                                    Subject</label><span class="red">*</span>
                                <input type="text" id="subject" class="form-control" placeholder="Enter subject"
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
                                <select id="project_id" class="form-select select2" name="project_id" required>
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
                               <label class="form-label" for="department_id">Department</label><span
                                   class="red">*</span>
                               <select id="department_id" class="form-select select2" name="department_id" required>
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
                           </div> --}}

                           {{-- <div class="col-md-6 col-sm-12 mb-1">
                               <label class="form-label" for="sub_department_id">Sub Department</label><span
                                   class="red">*</span>
                               <select id="sub_department_id" class="form-select select2" name="sub_department_id"
                                   required>
                                   <option value="">Select Sub Department</option><span class="red">*</span>
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
                            <style>
                                .fixed-width {
                                    display: inline-block;
                                    width: 50px; /* Set the width as needed */
                                    overflow: hidden;
                                    white-space: nowrap;
                                }
                            </style>
                             <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="user_id">Assign To</label><span class="red">*</span>
                                 <select id="user_id" class="form-select select2" name="user_id[]" multiple required>
                                     <option value="">Select User</option>
                                     @foreach ($users as $user)
                                         <option value="{{ $user->id }}"
                                             {{ old('user_id') && in_array($user->id, old('user_id')) ? 'selected' : ($task && $task->users->pluck('id')->contains($user->id) ? 'selected' : '') }}>
                                             <span class="fixed-width">{{ $user->first_name }} {{ $user->last_name }}</span>
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


                           {{-- <div class="col-md-6 col-sm-12 mb-1" id="assign_to_section">
                               <label class="form-label" for="user_id">Assign To</label><span class="red">*</span>
                               <select id="user_id" class="form-select select2" name="user_id[]" multiple required>
                                   <option value="">Select User</option>
                                   @foreach ($users as $user)
                                       <option value="{{ $user->id }}"
                                           {{ old('user_id') ? (in_array($user->id, old('user_id')) ? 'selected' : '') : ($task ? (in_array($user->id, $task->users->pluck('id')->toArray()) ? 'selected' : '') : '') }}>
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


                            <div class="col-md-3 col-sm-12 mb-1 position-relative">
                                <label class="form-label" for="start_date">Start Date</label><span
                                    class="red">*</span>
                                <input type="date" id="start_date" class="form-control" name="start_date"
                                    value="{{ old('start_date') ?? ($task != '' ? $task->start_date : date('Y-m-d')) }}"
                                    required>
                                <span class="text-danger">
                                    @error('start_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="due_date">End Date</label><span class="red">*</span>
                                <input type="date" id="due_date" class="form-control" name="due_date"
                                    value="{{ old('due_date') ?? ($task != '' ? $task->due_date : date('Y-m-d')) }}"
                                    required>
                                <span class="text-danger">
                                    @error('due_date')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-1">
                                <label class="form-label" for="priority_id">Priority</label><span class="red">*</span>
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
                            <div class="col-md-6 col-sm-12 ">
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
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" class="form-control" placeholder="Enter Description" name="description">{{ old('description') ?? ($task != '' ? html_entity_decode($task->description) : '') }}</textarea>
                                <span class="text-danger">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            @if ($task != '')
                                <div class="col-md-12 col-sm-12 mt-3">
                                    {{-- <form action="{{ route('comments.store') }}" method="POST">
                                        @csrf --}}
                                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Add Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
                                    </div>
                                    {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
                                    {{-- </form> --}}
                                </div>
                                <div class="col-12 mt-3">
                                    @foreach ($task->comments as $comment)
                                        <div class="card bg-white shadow-lg">
                                            <div class="card-header email-detail-head">
                                                <div
                                                    class="user-details d-flex justify-content-between align-items-center flex-wrap">
                                                    <div class="avatar me-75">
                                                        @if (!empty($comment->creator->profile_img))
                                                            <img src="{{ asset('storage/' . $comment->creator->profile_img) }}"
                                                                class="" alt="Profile Image" width="48"
                                                                height="48">
                                                        @else
                                                            <img src="http://127.0.0.1:8000/images/avatars/AvtarIMG.png"
                                                                class="" alt="Default Avatar" width="48"
                                                                height="48">
                                                        @endif
                                                    </div>
                                                    <div class="mail-items">
                                                        <h5 class="mt-0">{{ $comment->creator->first_name }}</h5>
                                                        <div class="email-info-dropup dropdown">
                                                            <span role="button"
                                                                class="dropdown-toggle font-small-3 text-muted"
                                                                id="card_top01" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                {{ $comment->creator->email }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mail-meta-item d-flex align-items-center">
                                                    <small
                                                        class="mail-date-time text-muted">{{ $comment->created_at }}</small>
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
@endsection
