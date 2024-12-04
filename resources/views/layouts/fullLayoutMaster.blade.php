@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
@php $configData = Helper::applClasses(); @endphp

<html class="loading {{ $configData['theme'] === 'light' ? '' : $configData['layoutTheme'] }}"
    lang="@if (session()->has('locale')) {{ session()->get('locale') }}@else{{ $configData['defaultLanguage'] }} @endif"
    data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}"
    @if ($configData['theme'] === 'dark') data-layout="dark-layout" @endif>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>@yield('title') -{{ env('APP_NAME') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('images/ico/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/ico/logo_fav.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')
</head>



<body
    class="vertical-layout vertical-menu-modern {{ $configData['bodyClass'] }} {{ $configData['theme'] === 'dark' ? 'dark-layout' : '' }} {{ $configData['blankPageClass'] }} blank-page"
    data-menu="vertical-menu-modern" data-col="blank-page" data-framework="laravel"
    data-asset-path="{{ asset('/') }}">

    <!-- BEGIN: Content-->
    <div class="app-content content {{ $configData['pageClass'] }}">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-body">

                {{-- Include Startkit Content --}}
                <div class="toast-container">
                    <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive"
                        aria-atomic="true">
                        <div class="toast-header">
                            <img src="{{ asset('images/logo/logo.png') }}" class="me-1" alt="Toast image"
                                height="18" width="25" />
                            <strong class="me-auto">Vue Admin</strong>
                            <small class="text-muted">11 mins ago</small>
                            <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                        <div class="toast-body">Hello, world! This is a toast message. Hope you're doing well.. :)</div>
                    </div>
                </div>
                @yield('content')

            </div>
        </div>
    </div>
    <!-- End: Content-->

    {{-- include default scripts --}}
    @include('panels/scripts')
    {{-- @include('content/apps/user/script_links') --}}

    <script type="text/javascript">
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
        $(document).ready(function() {
            @if (Session::has('error'))
                toastr['error']('{{ Session::get('error') }}', 'Error!', {
                    closeButton: true,
                    tapToDismiss: false
                });
            @elseif (Session::has('success'))
                toastr['success']('{{ Session::get('success') }}', 'Success!', {
                    closeButton: true,
                    tapToDismiss: false
                });
            @endif
        });
    </script>

</body>

</html>
