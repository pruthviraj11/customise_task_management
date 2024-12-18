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
    
            <form action="{{ route('app-task-update', encrypt($task->task_id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

    
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
                                    @foreach ($getTaskComments->comments as $comment)
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

        {{-- <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <!-- Card Body -->
                    <div class="card-body">
                        <h2>Sub tasks</h2>
                        @if ($SubTaskData == [])
                            <p>No subtasks found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mx-auto">
                                    <thead>
                                        <tr>
                                            <th>Task Number</th>
                                            <th>Assigned by</th>
                                            <th>Assigned To</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($SubTaskData as $subtask)
                                            <tr>
                                                <td>{{ $subtask->task_number }}</td>
                                                <td>{{ $subtask->creator->first_name . ' ' . $subtask->creator->last_name }}
                                                </td>
                                                <td>{{ $subtask->user->first_name . ' ' . $subtask->user->last_name }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($subtask->task->due_date)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $subtask->taskStatus->displayname }}</td>
                                                <td>
                                                    <!-- Button to trigger AJAX request to mark as completed -->
                                                    <a class="btn btn-success btn-sm mark-completed-btn"
                                                        data-subtask-id="{{ $subtask->id }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Mark as Completed">
                                                        <i class="feather-icon" data-feather="check-circle"></i>
                                                    </a>
                                                    <!-- Button to reopen the task when status is 7 or 4 -->
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
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                    </div>
                    @endif
                </div>
            </div>
        </div> --}}




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
            // Handle the click event on the "Mark as Completed" button
            $('.mark-completed-btn').on('click', function() {
                var subtaskId = $(this).data(
                    'subtask-id'); // Get the subtask ID from the button's data attribute

                // Show SweetAlert confirmation before proceeding
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Once marked as completed, you will not be able to change this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, mark as completed!',
                    cancelButtonText: 'No, keep it pending'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform the AJAX request if user confirms
                        $.ajax({
                            url: '{{ route('subtask.complete', ['subtask' => '__subtaskId__']) }}'
                                .replace('__subtaskId__', subtaskId),
                            method: 'POST', // Change method to POST
                            data: {
                                _token: '{{ csrf_token() }}', // CSRF token for security
                                _method: 'POST' // Add a hidden _method field to mimic PUT request
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Completed!',
                                        'The subtask has been marked as completed.',
                                        'success'
                                    );
                                    // Optionally, change the button to "Completed"
                                    $(this).prop('disabled', true).text('Completed');

                                    // Reload the page after successful completion
                                    location.reload(); // This will reload the page
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'There was an issue marking the subtask as completed.',
                                    'error'
                                );
                            }
                        });
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
                        // Send AJAX request to update status to 1 (Reopened)
                        $.ajax({
                            url: '{{ route('subtask.reopen', '__subtaskId__') }}'.replace(
                                '__subtaskId__', subtaskId
                            ), // Correct dynamic URL replacement
                            method: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}", // CSRF token for security
                                status: 1
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Reopened!',
                                        'The task has been reopened successfully.',
                                        'success'
                                    );

                                    location.reload();
                                    // Optionally, update the UI to reflect the status change
                                    // $(this).closest('tr').find('.status-column').text('Reopened');
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
                    }
                });
            });
        });
    </script>




@endsection