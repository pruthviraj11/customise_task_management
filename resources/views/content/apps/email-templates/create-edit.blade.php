@extends('layouts/contentLayoutMaster')

@section('title', $page_data['page_title'])

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endsection

@section('content')

    @if ($page_data['page_title'] == 'Add New Email Template')
        <form action="{{ route('app-email-templates-store') }}" class="mt-2" method="POST"
              enctype="multipart/form-data">
            @csrf
    @else
        <form action="{{ route('app-email-templates-update', encrypt($email_template->id)) }}" class="mt-2"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')
    @endif
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $page_data['form_title'] }}</h4>
                        <a href="{{ route('app-email-templates-list') }}"
                           class="col-md-2 btn btn-primary float-end">Email Template
                            List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="title">
                                    Title</label>
                                <input type="text" id="title" class="form-control"
                                       placeholder="Title"
                                       name="title"
                                       id="title"
                                       value="{{ old('title') ?? ($email_template ? $email_template->title : '') }}">
                                <span class="text-danger">
                                                    @error('title')
                                    {{ $message }}
                                    @enderror
                                                </span>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="subject">
                                    Subject</label>
                                <input type="text" id="subject" class="form-control"
                                       placeholder="Subject"
                                       name="subject"
                                       id="subject"
                                       value="{{ old('subject') ?? ($email_template ? $email_template->subject : '') }}">
                                <span class="text-danger">
                                                    @error('title')
                                    {{ $message }}
                                    @enderror
                                                </span>
                            </div>
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="description">
                                    Description</label>
                                <textarea
                                    class="form-control"
                                    id="exampleFormControlTextarea1 description"
                                    rows="3"
                                    name="description"
                                    placeholder="Textarea"
                                >{{ old('description') ?? ($email_template ? $email_template->description : '') }}</textarea>
                                <span class="text-danger">
                                                    @error('description')
                                    {{ $message }}
                                    @enderror
                                                </span>
                            </div>
                            <div class="col-md-8 col-sm-12 mb-1">
                                <label class="card-title">
                                    Email Structure
                                </label>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="hidden" name="html" id="editorHTML" value="">
                                        <div id="full-wrapper">
                                            <div id="full-container">
                                                <div class="editor" id="editor" style="min-height: 260px !important;">
                                                    {!! old('html') ?? ($email_template ? $email_template->html : '') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 mb-1">
                                <h4 class="text-center mt-1">To Add Click Buttons</h4>
                                <p class="text-center mb-1">Field will be added to the cursor point.</p>
                                <div class="row">
                                    <div class="col-6 parent">
                                        <button type="button" class="w-100 mb-1 btn btn-primary addTextBtn"
                                                data-field="first_name"
                                                data-value="{first_name}">First Name
                                        </button>
                                    </div>
                                    <div class="col-6 parent">
                                        <button type="button" class="w-100 mb-1 btn btn-primary addTextBtn"
                                                data-field="last_name"
                                                data-value="{last_name}">Last Name
                                        </button>
                                    </div>
                                    <div class="col-6 parent">
                                        <button type="button" class="w-100 mb-1 btn btn-primary addTextBtn"
                                                data-field="full_name"
                                                data-value="{full_name}">Full Name
                                        </button>
                                    </div>
                                    <div class="col-6 parent">
                                        <button type="button" class="w-100 mb-1 btn btn-primary addTextBtn"
                                                data-field="email"
                                                data-value="{email}">Email
                                        </button>
                                    </div>
                                    <div class="col-6 parent">
                                        <button type="button" class="w-100 mb-1 btn btn-primary addTextBtn"
                                                data-field="mobile"
                                                data-value="{mobile_no}">Mobile No
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 mb-1">
                                <label class="form-label" for="status-column">Status</label>
                                <div class="form-check form-check-success form-switch">
                                    <input type="checkbox" name="status"
                                           {{ $email_template != '' && $email_template->status == true ? 'checked' : '' }}
                                           class="form-check-input" id="active"/>
                                </div>
                                <span class="text-danger">
                                                    @error('active')
                                    {{ $message }}
                                    @enderror
                                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-1" id="submit">Submit</button>
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
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>


@endsection

@section('page-script')
    {{-- Page js files --}}

    {{--    <script src="{{ asset(mix('js/scripts/forms/form-quill-editor.js')) }}"></script>--}}
    <script>
        $(document).ready(function () {

            $("#editorHTML").val($('#editor').html());
            var quill = new Quill('.editor', {
                bounds: '#full-container .editor',
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [{font: []}, {size: []}],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{color: []}, {background: []}],
                        [{script: 'super'}, {script: 'sub'}],
                        [{header: '1'}, {header: '2'}, 'blockquote', 'code-block'],
                        [{list: 'ordered'}, {list: 'bullet'}, {indent: '-1'}, {indent: '+1'}],
                        ['direction', {align: []}],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });
            // let quill = new Quill('#editor');
            $(document).on('click', '.addTextBtn', function () {
                var dataValue = $(this).parents('.parent').find('.addTextBtn').data('value');
                var cursorPos = quill.getSelection();
                // Insert the text at the cursor position.
                quill.insertText(cursorPos, dataValue);
                var html = $('#editor').html();
                $("#editorHTML").val(html);
            })
            // document.addEventListener("DOMContentLoaded", function() {
            //     // Get references to the elements
            //     var editorContentInput = document.getElementById("editorHTML");
            //     var richTextEditor = document.getElementById("editor");
            //     var form = document.querySelector("form");
            //
            //     // Listen for the form's submission
            //     form.addEventListener("submit", function(event) {
            //         console.log(richTextEditor.innerHTML);
            //         // Update the hidden input field with the content from the rich text editor
            //         editorContentInput.value = richTextEditor.innerHTML;
            //     });
            // });
            $(document).on('keyup', '#editor', function (event) {
                event.preventDefault();
                // var email_template_id = $('#email_template_id').val();
                // var title = $('#title').val();
                // var subject = $('#subject').val();
                // var description = $('#description').val();
                // var status = $('#active').val();
                var html = $('#editor').html();
                $("#editorHTML").val(html);
                {{--console.log(title + " " + subject + " " + html + " " + status);--}}
                {{--$.ajax({--}}
                {{--    url: '{{ route('app-email-templates-store') }}',--}}
                {{--    method: 'POST',--}}
                {{--    data: {--}}
                {{--        _token: '{{ csrf_token() }}',--}}
                {{--        email_template_id: email_template_id,--}}
                {{--        title: title,--}}
                {{--        subject: subject,--}}
                {{--        description: description,--}}
                {{--        html: html,--}}
                {{--        status: status,--}}
                {{--    },--}}
                {{--    success: function (response) {--}}
                {{--        console.log(response);--}}
                {{--    }--}}
                {{--})--}}
            })
        });
    </script>
@endsection
