<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin') | NeetCommon</title>
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/font-awesome/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('src/assets/css/vertical-light-layout/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('src/assets/images/favicon.png') }}" />
    @stack('styles')
</head>
<body>
<div class="container-scroller">

    <!-- NAVBAR -->
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
                <span class="text-white fw-bold fs-5">NeetCommon</span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                <span class="text-white fw-bold">NC</span>
            </a>
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center">
            <h5 class="mb-0 font-weight-medium d-none d-lg-flex">Welcome, {{ Auth::user()->name }}!</h5>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                        <img class="img-xs rounded-circle ms-2" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="Profile">
                        <span class="font-weight-normal">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown" style="z-index:9999;">
                        <div class="dropdown-header text-center">
                            <img class="img-md rounded-circle" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="Profile">
                            <p class="mb-1 mt-3">{{ Auth::user()->name }}</p>
                            <p class="font-weight-light text-muted mb-0">{{ Auth::user()->gmail }}</p>
                        </div>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-nav">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="dropdown-item-icon icon-power text-primary"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </li>
                {{-- Always-visible logout button --}}
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-btn">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm ms-2" style="margin-top:6px;">
                            <i class="icon-power me-1"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                <span class="icon-menu"></span>
            </button>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper">

        <!-- SIDEBAR -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item nav-profile">
                    <a href="#" class="nav-link">
                        <div class="profile-image">
                            <img class="img-xs rounded-circle" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="profile">
                            <div class="dot-indicator bg-success"></div>
                        </div>
                        <div class="text-wrapper">
                            <p class="profile-name">{{ Auth::user()->name }}</p>
                            <p class="designation">{{ ucfirst(Auth::user()->user_type) }}</p>
                        </div>
                    </a>
                </li>

                <li class="nav-item nav-category"><span class="nav-link">Main Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="menu-title">Dashboard</span>
                        <i class="icon-screen-desktop menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item nav-category"><span class="nav-link">Management</span></li>

                <li class="nav-item {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.classes.index') }}">
                        <span class="menu-title">Classes (Folders)</span>
                        <i class="icon-folder menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#courses" data-bs-toggle="collapse" data-bs-target="#courses" aria-expanded="{{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }}">
                        <span class="menu-title">Courses</span>
                        <i class="icon-book-open menu-icon"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.courses.*') ? 'show' : '' }}" id="courses">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.courses.index') }}">All Courses</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.courses.create') }}">Add Course</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#subjects" data-bs-toggle="collapse" data-bs-target="#subjects" aria-expanded="{{ request()->routeIs('admin.subjects.*') ? 'true' : 'false' }}">
                        <span class="menu-title">Subjects</span>
                        <i class="icon-layers menu-icon"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.subjects.*') ? 'show' : '' }}" id="subjects">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.subjects.index') }}">All Subjects</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.subjects.create') }}">Add Subject</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#questions" data-bs-toggle="collapse" data-bs-target="#questions" aria-expanded="{{ request()->routeIs('admin.questions.*') ? 'true' : 'false' }}">
                        <span class="menu-title">Questions</span>
                        <i class="icon-question menu-icon"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.questions.*') ? 'show' : '' }}" id="questions">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.questions.index') }}">All Questions</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.questions.create') }}">Add Question</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.answers.*') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#answers" data-bs-toggle="collapse" data-bs-target="#answers" aria-expanded="{{ request()->routeIs('admin.answers.*') ? 'true' : 'false' }}">
                        <span class="menu-title">Answers</span>
                        <i class="icon-check menu-icon"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.answers.*') ? 'show' : '' }}" id="answers">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.answers.index') }}">All Answers</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.answers.create') }}">Add Answer</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.question-papers.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.question-papers.index') }}">
                        <span class="menu-title">Question Papers</span>
                        <i class="icon-doc menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.resources.index') }}">
                        <span class="menu-title">Resources</span>
                        <i class="icon-cloud-upload menu-icon"></i>
                    </a>
                </li>

                <li class="nav-item nav-category"><span class="nav-link">Account</span></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-start w-100" style="background:none;border:none;">
                            <span class="menu-title">Sign Out</span>
                            <i class="icon-power menu-icon"></i>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- MAIN PANEL -->
        <div class="main-panel">
            <div class="content-wrapper">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="footer">
                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                        Copyright &copy; {{ date('Y') }} NeetCommon. All rights reserved.
                    </span>
                </div>
            </footer>
        </div>
    </div>
</div>

<script src="{{ asset('src/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('src/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('src/assets/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('src/assets/js/misc.js') }}"></script>
<script src="{{ asset('src/assets/js/settings.js') }}"></script>
{{-- ============================================================
     Global: Prevent duplicate form submissions by disabling the
     submit button immediately on first click / form submit.
     Logout forms are excluded so they always work normally.
================================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    /**
     * Attach a one-time submit handler to every <form> on the page
     * EXCEPT the logout forms.
     */
    document.querySelectorAll('form').forEach(function (form) {

        // Skip logout forms — they should never be blocked
        if (form.id && form.id.startsWith('logout-form')) return;
        if (form.action && form.action.includes('/logout')) return;

        form.addEventListener('submit', function (e) {

            // Find ALL submit buttons inside this form
            var buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

            buttons.forEach(function (btn) {
                // Save original text so we can read it
                var original = btn.innerHTML || btn.value;

                // Disable immediately
                btn.disabled = true;

                // Add a subtle spinner to give visual feedback
                if (btn.tagName === 'BUTTON') {
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                        original;
                } else {
                    btn.value = 'Processing…';
                }
            });
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
