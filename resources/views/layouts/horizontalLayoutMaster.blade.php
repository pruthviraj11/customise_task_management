<body
    class="horizontal-layout horizontal-menu {{ $configData['contentLayout'] }} {{ $configData['horizontalMenuType'] }} {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }} {{ $configData['footerType'] }}"
    data-open="hover" data-menu="horizontal-menu"
    data-col="{{ $configData['showMenu'] ? $configData['contentLayout'] : '1-column' }}" data-framework="laravel"
    data-asset-path="{{ asset('/') }}">

    <!-- BEGIN: Header-->
    @include('panels.navbar')

    {{-- Include Sidebar --}}
    @if (isset($configData['showMenu']) && $configData['showMenu'] === true)
        @include('panels.horizontalMenu')
    @endif

    <!-- BEGIN: Content-->
    <div class="app-content content {{ $configData['pageClass'] }}">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        @if ($configData['contentLayout'] !== 'default' && isset($configData['contentLayout']))
            <div class="content-area-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                <div class="{{ $configData['sidebarPositionClass'] }}">
                    <div class="sidebar">
                        {{-- Include Sidebar Content --}}
                        @yield('content-sidebar')
                    </div>
                </div>
                <div class="{{ $configData['contentsidebarClass'] }}">
                    <div class="content-wrapper">
                        <div class="content-body">
                            {{-- Include Page Content --}}
                            <div class="toast-container">
                                <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert"
                                    aria-live="assertive" aria-atomic="true">
                                    <div class="toast-header">
                                        <img src="{{ asset('images/logo/logo.png') }}" class="me-1" alt="Toast image"
                                            height="18" width="25" />
                                        <strong class="me-auto">Vue Admin</strong>
                                        <small class="text-muted">11 mins ago</small>
                                        <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">Hello, world! This is a toast message. Hope you're doing
                                        well.. :)</div>
                                </div>
                            </div>
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="content-wrapper {{ $configData['layoutWidth'] === 'boxed' ? 'container-xxl p-0' : '' }}">
                {{-- Include Breadcrumb --}}
                @if ($configData['pageHeader'] == true)
                    @include('panels.breadcrumb')
                @endif

                <div class="content-body">

                    {{-- Include Page Content --}}
                    @yield('content')

                </div>
            </div>
        @endif

    </div>
    <!-- End: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('panels/footer')

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
                    positionClass: 'toast-top-center',
                    closeButton: true,
                    tapToDismiss: false
                });
            @elseif (Session::has('success'))
                toastr['success']('{{ Session::get('success') }}', 'Success!', {
                    positionClass: 'toast-top-center',
                    closeButton: true,
                    tapToDismiss: false
                });
            @endif
        });

        function customOnClickFunction(followUpId) {
            $.ajax({
                url: '/app/follow-ups/followupdata',
                method: 'GET',
                data: {
                    followup_id: followUpId
                },
                success: function(data) {
                    $('#modalContent').html(data.modalContent);
                    $('#myModal').modal('show');

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>
    <script>
        function callNotificationApi(call = 1) {
            $.ajax({
                url: "{{ route('api-notification-recent') }}", // Replace with the actual API URL
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    setTimeout(callNotificationApi, 10000);
                    $('#notification_html').html(data.notification_html)
                    if (data.new_notifications > 0) {
                        $('#list-count').css('display', 'block').html(data.new_notifications + " new")
                    }
                    if (data.total_notification > 0) {
                        $('#total-notification-count').css('display', 'block').html(data.total_notification)
                    }
                    if ((data.total_notification > 0 || data.new_notifications > 0) && call === 0 && data
                        .total_notification === data.new_notifications) {
                        var message = 'Your are having total ' + data.total_notification +
                            ' Unread Notifications';
                        toastr['warning'](message, `Notification  Alert..!!`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 3000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    }
                    if ((data.total_notification > 0 || data.new_notifications > 0) && call === 0 && data
                        .total_notification !== data.new_notifications) {
                        var message = 'Your are having total ' + data.total_notification +
                            ' Unread Notifications and ' + data.new_notifications + ' New Notifications';
                        toastr['warning'](message, `Notification  Alert..!!`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 0,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    setTimeout(callNotificationApi, 10000);
                    console.error(error);
                }
                // var followupId = $(this).data("id");
            })
        }

        $(document).on('click', '.notification-list-item', function() {
            alert("hola");
            console.log($(this).data(''));
        });


        $(document).ready(function() {
            $(document).on('click', 'button.read_notification_index', function(event) {
                event.preventDefault();
                var notifiactionId = $(this).data('internal-notification-id');
                console.log(notifiactionId);
                $.ajax({
                    url: '{{ route('app-notifications-read', '') }}/' + notifiactionId,
                    method: 'GET',
                    success: function(response) {
                        $(this).remove();
                        toastr['success'](`${response.message}`, `Success`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    },
                    error: function(response) {
                        toastr['error'](`${response.message}`, `Error`, {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                            timeOut: 2000,
                            tapToDismiss: false,
                            extendedTimeOut: 0,
                            disableTimeOut: true,
                        });
                    }
                });
            });

            callNotificationApi(0);
            setTimeout(function() {
                callApi();
            }, 25000);
            setTimeout(function() {
                callNotificationApi();
            }, 10000);
            @if (Session::has('error'))
                toastr['error']('{{ Session::get('error') }}', 'Error!', {
                    positionClass: 'toast-top-center',
                    closeButton: true,
                    tapToDismiss: false
                });
            @elseif (Session::has('success'))
                toastr['success']('{{ Session::get('success') }}', 'Success!', {
                    positionClass: 'toast-top-center',
                    closeButton: true,
                    tapToDismiss: false
                });
            @endif
        });
    </script>
</body>

</html>
