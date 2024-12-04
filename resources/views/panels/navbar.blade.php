@if ($configData['mainLayoutType'] === 'horizontal' && isset($configData['mainLayoutType']))
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center {{ $configData['navbarColor'] }}"
        data-nav="brand-center">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <span class="brand-logo">
                            <img src="{{ asset('images/ico/logo_fav.png') }}" alt=""></span>
                    </a>
                </li>
            </ul>
        </div>
    @else
        <nav
            class="header-navbar navbar navbar-expand-lg align-items-center {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }} {{ $configData['layoutWidth'] === 'boxed' && $configData['verticalMenuNavbarType'] === 'navbar-floating' ? 'container-xxl' : '' }}">
@endif
<div class="navbar-container d-flex content">
    <div class="bookmark-wrapper d-flex align-items-center">
        <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon"
                        data-feather="menu"></i></a></li>
        </ul>
        <ul class="nav navbar-nav bookmark-icons">

            {{-- <li class="nav-item nav-search">
                                <a class="nav-link nav-link-search">
                                    <i class="ficon" data-feather="search"></i>
                                </a>
                                <div class="search-input">
                                    <div class="search-input-icon"><i data-feather="search"></i></div>
                                    <input class="form-control input" type="text"
                                           placeholder="Globally Search with id, Phone No, Email, Name..." tabindex="-1"
                                           data-search="search">
                                    <div class="search-input-close"><i data-feather="x"></i></div>
                                    <ul class="search-list search-list-main"></ul>
                                </div>
                            </li> --}}
        </ul>
    </div>
    <ul class="nav navbar-nav align-items-center ms-auto">
        {{-- <li class="nav-item nav-search">
            <a class="nav-link nav-link-search">
                <i class="ficon" data-feather="search"></i>
            </a>
            <div class="search-input">
                <div class="search-input-icon"><i data-feather="search"></i></div>
                <input class="form-control input" type="text"
                    placeholder="Globally Search with id, Phone No, Email, Name..." tabindex="-1" data-search="search">
                <div class="search-input-close"><i data-feather="x"></i></div>
                <ul class="search-list search-list-main"></ul>
            </div>
        </li> --}}
        <li class="nav-item d-none d-lg-block">
            {{-- <a href="{{ route('send-mail') }}"><i class="ficon" data-feather="mail"></i></a> --}}
            {{-- <button
                                type="button"
                                class="compose-email btn w-100"
                                data-bs-backdrop="false"
                                data-bs-toggle="modal"
                                data-bs-target="#compose-mail"
                            >
                                <i class="ficon" data-feather="mail"></i>
                            </button> --}}
            {{--                            <a class="nav-link" href="" --}}
            {{--                                 data-bs-toggle="tooltip" data-bs-placement="bottom" --}}
            {{--                                 title="Email"> --}}
            {{--                                <i class="ficon" data-feather="mail"></i> --}}
            {{--                            </a> --}}
        </li>
        <li class="nav-item dropdown dropdown-notification me-25">
            <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                <i class="ficon" data-feather="bell"></i>
                <span class="badge rounded-pill bg-danger badge-up" style="display: none !important;"
                    id="total-notification-count"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">

                <li class="dropdown-menu-header">
                    <div class="dropdown-header d-flex">
                        <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                        <div class="badge rounded-pill badge-light-primary" style="display: none !important;"
                            id="list-count"></div>
                    </div>
                </li>
                <li class="scrollable-container media-list" id="notification_html">

                </li>
                <li class="dropdown-menu-footer">
                    {{-- <a class="btn btn-primary w-100" href="{{ route('app-notifications') }}">Read all
                                        notifications</a> --}}
                </li>
            </ul>
        </li>
        <li class="nav-item dropdown dropdown-user">
            <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);"
                data-bs-toggle="dropdown" aria-haspopup="true">
                <div class="user-nav d-sm-flex d-none">
                    <span class="user-name fw-bolder">
                        @if (Auth::check())
                            {{ Auth::user()->name }}
                        @else
                            User
                        @endif
                    </span>
                    <span class="user-status">
                        {{ Auth::user() ? Auth::user()->first_name : 'User' }}
                    </span>
                </div>
                <span class="avatar">
                    <img class="round"
                        src="{{ Auth::user() ? (Auth::user()->profile_img ? asset('storage/' . Auth::user()->profile_img) : asset('images/avatars/10.png')) : asset('images/avatars/10.png') }}"
                        alt="avatar" height="40" width="40">

                    <span class="avatar-status-online"></span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                <h6 class="dropdown-header">Manage Profile</h6>
                <div class="dropdown-divider"></div>
                {{-- Encrypt the ID --}}
                @php
                    $encrypted_id = encrypt(auth()->user()->id);
                @endphp

                {{-- Generate the URL --}}
                @php
                    $route = Route::has('profile.show')
                        ? route('profile.show', ['encrypted_id' => $encrypted_id])
                        : 'javascript:void(0)';
                @endphp
                <a class="dropdown-item" href="{{ $route }}">
                    <i class="me-50" data-feather="user"></i> Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="me-50" data-feather="settings"></i> Settings
                </a>

                {{-- @if (Auth::User())
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Manage Team</h6>
                                    <div class="dropdown-divider"></div>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">
                                        Switch Teams
                                    </h6>
                                    <div class="dropdown-divider"></div>
                                    @if (Auth::user())
                                    @endif
                                @endif --}}
                @if (Auth::check())
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="me-50" data-feather="power"></i> Logout
                    </a>
                    <form method="POST" id="logout-form" action="{{ route('logout') }}">
                        @csrf
                    </form>
                @else
                    <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : 'javascript:void(0)' }}">
                        <i class="me-50" data-feather="log-in"></i> Login
                    </a>
                @endif
            </div>
        </li>
    </ul>
</div>
</nav>

{{-- Search Start Here --}}
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center">
        <a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Files</h6>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between w-100" href="{{ url('app/file-manager') }}">
            <div class="d-flex">
                <div class="me-75">
                    <img src="{{ asset('images/icons/xls.png') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p>
                    <small class="text-muted">Marketing Manager</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;17kb</small>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between w-100" href="{{ url('app/file-manager') }}">
            <div class="d-flex">
                <div class="me-75">
                    <img src="{{ asset('images/icons/jpg.png') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p>
                    <small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;11kb</small>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between w-100" href="{{ url('app/file-manager') }}">
            <div class="d-flex">
                <div class="me-75">
                    <img src="{{ asset('images/icons/pdf.png') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p>
                    <small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;150kb</small>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between w-100" href="{{ url('app/file-manager') }}">
            <div class="d-flex">
                <div class="me-75">
                    <img src="{{ asset('images/icons/doc.png') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p>
                    <small class="text-muted">Web Designer</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;256kb</small>
        </a>
    </li>
    <li class="d-flex align-items-center">
        <a href="javascript:void(0);">
            <h6 class="section-label mt-75 mb-0">Members</h6>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between py-50 w-100" href="{{ url('app/user/view') }}">
            <div class="d-flex align-items-center">
                <div class="avatar me-75">
                    <img src="{{ asset('images/portrait/small/avatar-s-8.jpg') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p>
                    <small class="text-muted">UI designer</small>
                </div>
            </div>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between py-50 w-100" href="{{ url('app/user/view') }}">
            <div class="d-flex align-items-center">
                <div class="avatar me-75">
                    <img src="{{ asset('images/portrait/small/avatar-s-1.jpg') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p>
                    <small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between py-50 w-100" href="{{ url('app/user/view') }}">
            <div class="d-flex align-items-center">
                <div class="avatar me-75">
                    <img src="{{ asset('images/portrait/small/avatar-s-14.jpg') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p>
                    <small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a>
    </li>
    <li class="auto-suggestion">
        <a class="d-flex align-items-center justify-content-between py-50 w-100" href="{{ url('app/user/view') }}">
            <div class="d-flex align-items-center">
                <div class="avatar me-75">
                    <img src="{{ asset('images/portrait/small/avatar-s-6.jpg') }}" alt="png" height="32">
                </div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p>
                    <small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a>
    </li>
</ul>

{{-- if main search not found! --}}
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion justify-content-between">
        <a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start">
                <span class="me-75" data-feather="alert-circle"></span>
                <span>No results found.</span>
            </div>
        </a>
    </li>
</ul>
{{-- Search Ends --}}
<!-- END: Header-->
