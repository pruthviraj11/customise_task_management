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

    <form action="{{ route('app-task-update', encrypt($taskAss->task_id)) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <section id="multiple-column-form">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ $page_data['form_title'] }}</h4>
                            <div class="float-end">
                                @if ($taskAss && $taskAss->task)
                                    <div class="float-end mb-1">
                                        <button type="button" onclick="printTaskDetails()" class="btn btn-success">
                                            <i class="feather-icon" data-feather="printer"></i> Print/View Task
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                @if ($taskAss != '')
                                    <a class=" btn-sm btn-primary "> Task # {{ $taskAss->task_id }}</a>
                                    <a class=" btn-sm btn-primary "> Task Created By {{ $taskAss->creator->first_name }}
                                        {{ $taskAss->creator->last_name }}</a>
                                @endif

                            </div>

                            {{-- <h4 class="card-title">{{$page_data['form_title']}}</h4> --}}

                        </div>
                        <div class="card-body">
                            <div class="row">


                                <input type="hidden" name="task_created_by" value="{{ $taskAss->created_by }}">
                                <div class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="due_date_form">End Date</label><span
                                        class="red">*</span>
                                    <input type="date" id="due_date_form" class="form-control" name="due_date_form"
                                        value="{{ old('due_date_form') ?? ($taskAss != '' ? $taskAss->due_date : date('Y-m-d')) }}"
                                        required>
                                    <span class="text-danger">
                                        @error('due_date_form')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-1">
                                    <label class="form-label" for="task_status">Status</label><span class="red">*</span>
                                    <select id="task_status" class="form-select select2" name="task_status"
                                        {{ $taskAss ? ($taskAss->task_status == 7 ? 'disabled' : '') : '' }} required>
                                        {{-- <option value="">Select Status</option> --}}
                                        @foreach ($Status as $Statu)
                                            <option value="{{ $Statu->id }}"
                                                @php $isDisabled = $Statu->disabled == true && ($taskAss == ' ' || (is_object($taskAss) && $taskAss->created_by != auth()->user()->id)); @endphp
                                                {{ $isDisabled ? 'disabled' : '' }} {{-- {{ $Statu->disabled == true && ($taskAss && $taskAss->created_by != auth()->user()->id) ? 'disabled' : '' }} --}}
                                                {{ old('task_status') == $Statu->id ? 'selected' : ($taskAss ? ($taskAss->task_status == $Statu->id ? 'selected' : '') : '') }}>
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
                                    <label class="form-label" for="description">Description</label>
                                    <h5>{{ $taskAss && $taskAss->description ? trim(strip_tags($taskAss->description)) : '-' }}
                                    </h5>
                                    <span class="text-danger">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                {{-- @if ($taskAss && $taskAss->task && $taskAss->task->attachments->count()) --}}
                                <div class="col-6 col-sm-12 mb-1">
                                    <label class="form-label">Attachments</label>
                                    <div class="input-group mb-3 w-100">
                                        <input type="file" class="form-control" id="attachments" name="attachments[]"
                                            multiple>
                                        <label class="input-group-text btn btn-info" for="attachments">+ Choose</label>
                                    </div>

                                    <ul id="attachment-list">
                                        @foreach ($taskAss->task->attachments as $attachment)
                                            <li id="attachment-{{ $attachment->id }}" class="">
                                                <a href="{{ route('attachment.download', ['attachmentId' => $attachment->id]) }}"
                                                    target="_blank">
                                                    {{ basename($attachment->file) }}
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger ms-2 delete-attachment"
                                                    data-id="{{ $attachment->id }}">
                                                    Delete
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <span class="text-danger">
                                        @error('attachments')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                {{-- @endif --}}
                                @if ($taskAss != '')
                                    <div class="col-md-12 col-sm-12 mt-3">
                                        {{-- <form action="{{ route('comments.store') }}" method="POST">
                                        @csrf --}}
                                        {{-- {{ dd($taskAss); }} --}}
                                        <input type="hidden" name="task_id" value="{{ $taskAss->task_id }}">
                                        <div class="mb-3">
                                            <label for="comment_form" class="form-label">Add Comment</label>
                                            <textarea class="form-control" id="comment_form" name="comment_form" rows="4"></textarea>
                                        </div>
                                        {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
                                        {{-- </form> --}}
                                    </div>
                                    <div class="col-12 mt-3" style="max-height: 400px; overflow-y: auto;">
                                        @foreach ($getTaskComments as $comment)
                                            @php
                                                // Get the logged-in user ID
                                                $loggedInUserId = auth()->id();

                                                // Split the comma-separated list of users to whom the comment is directed
                                                $toUserIds = explode(',', $comment->to_user_id); // if comma-separated IDs are stored
                                            @endphp

                                            {{-- Check if the logged-in user can view the comment --}}
                                            @if (
                                                $loggedInUserId == $comment->created_by || // Show for comment creator
                                                    in_array($loggedInUserId, $toUserIds) || // Show for users the comment is directed to
                                                    $loggedInUserId == $taskAss->created_by)
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
                                                                            alt="Profile Image" width="48"
                                                                            height="48">
                                                                    @else
                                                                        <img src="http://127.0.0.1:8000/images/avatars/AvtarIMG.png"
                                                                            alt="Default Avatar" width="48"
                                                                            height="48">
                                                                    @endif
                                                                @else
                                                                    {{-- Display the comment creator's profile image --}}
                                                                    @if (!empty($comment->creator->profile_img))
                                                                        <img src="{{ asset('storage/' . $comment->creator->profile_img) }}"
                                                                            alt="Profile Image" width="48"
                                                                            height="48">
                                                                    @else
                                                                        <img src="http://127.0.0.1:8000/images/avatars/AvtarIMG.png"
                                                                            alt="Default Avatar" width="48"
                                                                            height="48">
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

            {{-- {{dd($taskAss->user)}} --}}

            <div id="printable-task-details" class="print-area" style="display: none;">
                <div class="receipt-container">
                    <div class="receipt-header">
                        <h2>TASK RECEIPT</h2>
                        <p>{{ config('app.name', 'Task Management System') }}</p>
                        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
                    </div>

                    @if ($taskAss && $taskAss->task)
                        <div class="receipt-section">
                            <div class="section-title">TASK INFORMATION</div>

                            <div class="receipt-row">
                                <span>Task Number:</span>
                                <span>{{ $taskAss->task_number ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Title:</span>
                                <span>{{ $taskAss->task->title ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Subject:</span>
                                <span>{{ $taskAss->task->subject ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Description:</span>
                                <span>{{ strip_tags($taskAss->task->description ?? 'N/A') }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Start Date:</span>
                                <span>{{ \Carbon\Carbon::parse($taskAss->task->start_date)->format('d/m/Y') }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>End Date:</span>
                                <span>{{ \Carbon\Carbon::parse($taskAss->task->due_date)->format('d/m/Y') }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Priority:</span>
                                <span>{{ $taskAss->task->priority_name ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Project:</span>
                                <span>{{ $taskAss->task->project_name ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Status:</span>
                                <span>{{ $taskAss->task->status_name ?? 'N/A' }}</span>
                            </div>

                            <div class="receipt-row">
                                <span>Assign To:</span>
                                <span>{{ $taskAss->user->first_name . " " . $taskAss->user->last_name ?? 'N/A' }}</span>
                            </div>


                            <div class="receipt-row">
                                <span>Assign By:</span>
                                <span>{{  $taskAss->creator->first_name . " " . $taskAss->creator->last_name ?? 'N/A' }}</span>
                            </div>
                            {{-- $taskAss->creator->first_name --}}
                        </div>
                    @endif

                    <div style="text-align: center; margin-top: 30px; border-top: 2px solid #333; padding-top: 15px;">
                        <p>*** END OF Task ***</p>
                        <p>Thank you for using our Task Management System</p>
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
        function printTaskDetails() {
            const printArea = document.getElementById('printable-task-details');
            printArea.style.display = 'block';

            const printContent = printArea.innerHTML;
            const printWindow = window.open('', '_blank', 'width=800,height=600');

            printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Task Receipt</title>
                <style>
                    body {
                        font-family: 'Courier New', monospace;
                        margin: 20px;
                        line-height: 1.4;
                    }
                    .receipt-container {
                        max-width: 800px;
                        margin: 0 auto;
                        border: 2px solid #333;
                        padding: 20px;
                    }
                    .receipt-header {
                        text-align: center;
                        border-bottom: 2px solid #333;
                        padding-bottom: 15px;
                        margin-bottom: 20px;
                    }
                    .receipt-row {
                        display: flex;
                        justify-content: space-between;
                        margin: 8px 0;
                        padding: 3px 0;
                        border-bottom: 1px dotted #ccc;
                    }
                    .receipt-section {
                        margin: 20px 0;
                    }
                    .section-title {
                        font-weight: bold;
                        font-size: 16px;
                        margin-bottom: 10px;
                        text-decoration: underline;
                    }
                    .print-btn {
                        margin: 10px auto;
                        display: block;
                        padding: 10px 20px;
                        background: #28a745;
                        color: white;
                        border: none;
                        font-size: 16px;
                        cursor: pointer;
                    }
                    @media print {
                        .print-btn { display: none; }
                    }
                </style>
            </head>
            <body>
                <button class="print-btn" onclick="window.print()">Print Task</button>
                ${printContent}
            </body>
            </html>
        `);

            printWindow.document.close();
            printArea.style.display = 'none';
        }
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attachmentList = document.getElementById('attachment-list');

            attachmentList.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-attachment')) {
                    const attachmentId = e.target.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You won’t be able to revert this!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ route('attachment.destroy', ':id') }}`.replace(':id',
                                    attachmentId), {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) throw new Error(
                                        'Failed to delete attachment.');
                                    return response.json();
                                })
                                .then(data => {
                                    const li = document.getElementById(
                                        `attachment-${attachmentId}`);
                                    if (li) li.remove();

                                    Swal.fire(
                                        'Deleted!',
                                        'The attachment has been deleted.',
                                        'success'
                                    );
                                })
                                .catch(error => {
                                    console.error(error);
                                    Swal.fire(
                                        'Error!',
                                        'There was a problem deleting the attachment.',
                                        'error'
                                    );
                                });
                        }
                    });
                }
            });
        });
    </script>

    <script>
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
