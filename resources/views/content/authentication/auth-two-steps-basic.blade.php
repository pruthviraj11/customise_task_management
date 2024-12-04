@extends('layouts/fullLayoutMaster')

@section('title', 'Two Steps Basic')

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- two steps verification basic-->
            <div class="card mb-0">
                <div class="card-body">
                    <a href="#" class="brand-logo">
                        <img src="{{ asset('images/ico/logo.png') }}" alt="">
                    </a>

                    <h2 class="card-title fw-bolder mb-1">Two Step Verification 💬</h2>
                    <p class="card-text mb-75">
                        We sent a verification code to your mobile. Enter the code from the mobile in the field below.
                    </p>
                    <p class="card-text fw-bolder mb-2">******0789</p>

                    <form class="mt-2" action="{{ asset('/') }}" method="GET">
                        <h6>Type your 6 digit security code</h6>
                        <div class="auth-input-wrapper d-flex align-items-center justify-content-between">
                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1" maxlength="1"
                                autofocus="" />

                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1"
                                maxlength="1" />

                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1"
                                maxlength="1" />

                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1"
                                maxlength="1" />

                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1"
                                maxlength="1" />

                            <input type="text"
                                class="form-control auth-input height-50 text-center numeral-mask mx-25 mb-1"
                                maxlength="1" />
                        </div>
                        <button type="submit" class="btn btn-primary w-100" tabindex="4">Sign in</button>
                    </form>

                    <p class="text-center mt-2">
                        <span>Didn’t get the code?</span><a href="Javascript:void(0)"><span>&nbsp;Resend</span></a>
                        <span>or</span>
                        <a href="Javascript:void(0)"><span>&nbsp;Call Us</span></a>
                    </p>
                </div>
            </div>
            <!-- /two steps verification basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/auth-two-steps.js')) }}"></script>
@endsection
