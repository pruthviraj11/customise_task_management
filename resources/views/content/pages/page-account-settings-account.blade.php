@extends('layouts/contentLayoutMaster')

@section('title', 'Account')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">


            <!-- profile -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <!-- header section -->
                    <form class="validate-form mt-2 pt-50"action="{{ route('profile-update', encrypt($data->id)) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="d-flex">
                            {{-- <a href="#" class="me-25">
                                <img src="{{ asset('images/portrait/small/avatar-s-11.jpg') }}" id="account-upload-img"
                                    class="uploadedAvatar rounded me-50" alt="profile image" height="100"
                                    width="100" />
                            </a> --}}
                            <!-- upload and reset button -->
                            {{-- <div class="d-flex align-items-end mt-75 ms-1">
                                <div>
                                    <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75">Upload</label>
                                    <input type="file" id="account-upload" hidden accept="image/*" />
                                    <button type="button" id="account-reset"
                                        class="btn btn-sm btn-outline-secondary mb-75">Reset</button>
                                    <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                </div>
                            </div> --}}
                            <!--/ upload and reset button -->
                        </div>
                        <!--/ header section -->

                        <!-- form -->
                        <div class="row">
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountFirstName">First Name</label>
                                <input type="text" class="form-control" id="accountFirstName" name="first_name"
                                    placeholder="John" value="{{ $data->first_name }}"
                                    data-msg="Please enter first name" />
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountLastName">Last Name</label>
                                <input type="text" class="form-control" id="accountLastName" name="last_name"
                                    placeholder="Doe" value="{{ $data->last_name }}" data-msg="Please enter last name" />
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountEmail">Email</label>
                                <input type="email" class="form-control" id="accountEmail" name="email"
                                    placeholder="Email" value="{{ $data->email }}" />
                                {{-- {{ dd($data) }} --}}
                            </div>
                            {{-- <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountOrganization">Organization</label>
                                <input type="text" class="form-control" id="accountOrganization" name="organization"
                                    placeholder="Organization name" value="PIXINVENT" />
                            </div> --}}
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Phone Number</label>
                                <input type="text" class="form-control account-number-mask" id="accountPhoneNumber"
                                    name="phone_no" placeholder="Phone Number" value="{{ $data->phone_no }}" />
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="row align-items-md-end">
                                    <div class="col-md-9 col-sm-12">
                                        <label class="form-label" for="password">
                                            Password</label>
                                        {{-- <div class="input-group input-group-merge form-password-toggle">
                                                            <input
                                                                type="password"
                                                                class="form-control form-control-merge"
                                                                id="password"
                                                                name="password"
                                                                value=""
                                                            />
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div> --}}
                                        <input type="text" id="password" class="form-control"
                                            placeholder="{{ $data ? ($data->password ? 'Enter or Click Generate to Change Password' : 'Password') : '' }}"
                                            name="password" value="{{ old('password') ?? old('password') }}">
                                        <span class="text-danger">
                                            @error('password')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <button type="button" class="btn btn-outline-primary"
                                            id="generatePassword">Generate Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="profile_img">Profile Photo</label>
                                <input type="file" id="profile_img" class="form-control" name="profile_img">

                                {{-- {{ dd($data) }} --}}
                                @if (!empty($data->profile_img))
                                    <img width="150" src="{{ Storage::url($data->profile_img) }}" class="mt-2 "
                                        alt="Profile Photo">
                                @else
                                    <img src="{{ asset('images/avatars/10.png') }}" class="mt-2" alt="Default Avatar"
                                        style="height: auto;width: 100px;">
                                @endif

                                <span class="text-danger">
                                    @error('profile_img')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <!-- Outlook Integration Fields -->
                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="outlook_client_id">Outlook Client ID</label>
                                <input type="text" id="outlook_client_id" class="form-control"
                                    name="outlook_client_id"
                                    value="{{ old('outlook_client_id', $data->outlook_client_id ?? '') }}">
                                <span class="text-danger">
                                    @error('outlook_client_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="outlook_client_secret">Outlook Client Secret</label>
                                <input type="text" id="outlook_client_secret" class="form-control"
                                    name="outlook_client_secret"
                                    value="{{ old('outlook_client_secret', $data->outlook_client_secret ?? '') }}">
                                <span class="text-danger">
                                    @error('outlook_client_secret')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="outlook_redirect_url">Outlook Redirect URL</label>
                                <input type="url" id="outlook_redirect_url" class="form-control"
                                    name="outlook_redirect_url"
                                    value="{{ old('outlook_redirect_url', $data->outlook_redirect_url ?? '') }}">
                                <span class="text-danger">
                                    @error('outlook_redirect_url')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-6 col-sm-12 mb-1">
                                <label class="form-label" for="outlook_tenant_id">Outlook Tenant ID</label>
                                <input type="text" id="outlook_tenant_id" class="form-control"
                                    name="outlook_tenant_id"
                                    value="{{ old('outlook_tenant_id', $data->outlook_tenant_id ?? '') }}">
                                <span class="text-danger">
                                    @error('outlook_tenant_id')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <input type="hidden" id="selectedColumns" name="selected_columns" value="">

                            <div class="card">
                                <div class="card-body ">
                                    <div id="columnVisibilityModal">
                                        <div class="row">
                                            {{-- @php
                                                $selectedColumns = json_decode(auth()->user()->selected_fields, true); // Get the selected columns array

                                           @endphp --}}

                                            @php
                                                $selectedColumnsRaw = auth()->user()->selected_fields;
                                                $selectedColumns = is_string($selectedColumnsRaw)
                                                    ? json_decode($selectedColumnsRaw, true)
                                                    : [];

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
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="0"
                                                    @if (is_null($selectedColumns) || (is_array($selectedColumns) && in_array(0, $selectedColumns))) checked @endif>
                                                Actions
                                            </div>

                                            {{--
                                            @if ($type == 'mytask')
                                                <div class="list-group-item col">
                                                    <input type="checkbox" class="column-toggle" data-column="1" checked>
                                                    Pin Task
                                                </div>
                                            @endif  --}}

                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="2" checked>
                                                Task ID
                                            </div>

                                            {{-- Code for Haystack option to select fields code Starts --}}

                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="3"
                                                    @if (in_array(3, $selectedColumns)) checked @endif>
                                                Task Number
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="4"
                                                    @if (in_array(4, $selectedColumns)) checked @endif>
                                                Task/Ticket
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="5"
                                                    @if (in_array(5, $selectedColumns)) checked @endif>
                                                Title
                                            </div>

                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="7"
                                                    @if (in_array(7, $selectedColumns)) checked @endif>
                                                Subject
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="8"
                                                    @if (in_array(8, $selectedColumns)) checked @endif>
                                                Assign By
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="9"
                                                    @if (in_array(9, $selectedColumns)) checked @endif>
                                                Assigned To
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="10"
                                                    @if (in_array(10, $selectedColumns)) checked @endif>
                                                Status
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="11"
                                                    @if (in_array(11, $selectedColumns)) checked @endif>
                                                Created Date
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="12"
                                                    @if (in_array(12, $selectedColumns)) checked @endif>
                                                Start Date
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="13"
                                                    @if (in_array(13, $selectedColumns)) checked @endif>
                                                Due Date
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="14"
                                                    @if (in_array(14, $selectedColumns)) checked @endif>
                                                Completed Date
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="15"
                                                    @if (in_array(15, $selectedColumns)) checked @endif>
                                                Accepted Date
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="16"
                                                    @if (in_array(16, $selectedColumns)) checked @endif>
                                                Project
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="17"
                                                    @if (in_array(17, $selectedColumns)) checked @endif>
                                                Department
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="18"
                                                    @if (in_array(18, $selectedColumns)) checked @endif>
                                                Sub Department
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="19"
                                                    @if (in_array(19, $selectedColumns)) checked @endif>
                                                Owner Department
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="20"
                                                    @if (in_array(20, $selectedColumns)) checked @endif>
                                                Owner Sub Department
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="21"
                                                    @if (in_array(21, $selectedColumns)) checked @endif>
                                                Owner Contact Info
                                            </div>
                                            <div class="list-group-item col">
                                                <input type="checkbox" class="column-toggle" data-column="22"
                                                    @if (in_array(22, $selectedColumns)) checked @endif>
                                                Close Date
                                            </div>

                                            {{-- Code for Haystack option to select fields code Ends --}}

                                            {{-- @if ($type == 'mytask')
                                                <div class="list-group-item col">
                                                    <input type="checkbox" class="column-toggle" data-column="23"
                                                        @if (in_array(23, $selectedColumns)) checked @endif>
                                                    Is Pinned
                                                </div>
                                            @endif --}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mt-1 me-1">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary mt-1">Discard</button>
                            </div>
                        </div>
                    </form>
                    <!--/ form -->
                </div>
            </div>

            <!-- deactivate account  -->
            {{-- <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Delete Account</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">Are you sure you want to delete your account?</h4>
                        <div class="alert-body fw-normal">
                            Once you delete your account, there is no going back. Please be certain.
                        </div>
                    </div>

                    <form id="formAccountDeactivation" class="validate-form" onsubmit="return false">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accountActivation"
                                id="accountActivation" data-msg="Please confirm you want to delete account" />
                            <label class="form-check-label font-small-3" for="accountActivation">
                                I confirm my account deactivation
                            </label>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-danger deactivate-account mt-1">Deactivate
                                Account</button>
                        </div>
                    </form>
                </div>
            </div> --}}
            <!--/ profile -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>


@endsection

@section('page-script')


    <script src="{{ asset(mix('js/scripts/components/components-bs-toast.js')) }}"></script>
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/page-account-settings-account.js')) }}"></script>

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

        $(document).ready(function() {
            // Generate password when the button is clicked
            $("#generatePassword").click(function() {
                const generatedPassword = generateRandomPassword(
                    10); // You can adjust the length of the password here
                $("#password").val(generatedPassword);
                $("#password").select();
                document.execCommand("copy");
                alert("Password copied to clipboard!");
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const columnToggles = document.querySelectorAll('.column-toggle');
            const selectedColumnsField = document.getElementById('selectedColumns');

            // Update the hidden input field on checkbox change
            columnToggles.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const selectedColumns = Array.from(columnToggles)
                        .filter((checkbox) => checkbox.checked)
                        .map((checkbox) => checkbox.dataset.column)
                        .join(',');

                    selectedColumnsField.value = selectedColumns;
                });
            });

            // Trigger change event on page load to prepopulate the hidden field
            columnToggles.forEach((checkbox) => checkbox.dispatchEvent(new Event('change')));
        });
    </script>



@endsection
