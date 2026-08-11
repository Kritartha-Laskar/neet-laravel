<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard | NeetCommon</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/font-awesome/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/jvectormap/jquery-jvectormap.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/chartist/chartist.min.css') }}">
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('src/assets/css/vertical-light-layout/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('src/assets/images/favicon.png') }}" />
</head>
<body>
    <div class="container-scroller">

        <!-- ===== NAVBAR ===== -->
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
                    <form class="search-form d-none d-md-block" action="#">
                        <i class="icon-magnifier"></i>
                        <input type="search" class="form-control" placeholder="Search Here" title="Search here">
                    </form>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
                        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                            <img class="img-xs rounded-circle ms-2" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="Profile image">
                            <span class="font-weight-normal">{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown" style="z-index:9999;">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="Profile image">
                                <p class="mb-1 mt-3">{{ Auth::user()->name }}</p>
                                <p class="font-weight-light text-muted mb-0">{{ Auth::user()->gmail }}</p>
                            </div>
                            <a class="dropdown-item"><i class="dropdown-item-icon icon-user text-primary"></i> My Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="dropdown-item-icon icon-power text-primary"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </li>
                    {{-- Always-visible logout button --}}
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
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
        <!-- ===== END NAVBAR ===== -->

        <div class="container-fluid page-body-wrapper">

            <!-- ===== SIDEBAR ===== -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item navbar-brand-mini-wrapper">
                        <a class="nav-link navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                            <span class="fw-bold text-primary">NC</span>
                        </a>
                    </li>
                    <li class="nav-item nav-profile">
                        <a href="#" class="nav-link">
                            <div class="profile-image">
                                <img class="img-xs rounded-circle" src="{{ asset('src/assets/images/faces/face8.jpg') }}" alt="profile image">
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

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.classes.index') }}">
                            <span class="menu-title">Classes (Folders)</span>
                            <i class="icon-folder menu-icon"></i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#courses" data-bs-toggle="collapse" data-bs-target="#courses" aria-expanded="false" aria-controls="courses">
                            <span class="menu-title">Courses</span>
                            <i class="icon-book-open menu-icon"></i>
                        </a>
                        <div class="collapse" id="courses">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.courses.index') }}">All Courses</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.courses.create') }}">Add Course</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#subjects" data-bs-toggle="collapse" data-bs-target="#subjects" aria-expanded="false" aria-controls="subjects">
                            <span class="menu-title">Subjects</span>
                            <i class="icon-layers menu-icon"></i>
                        </a>
                        <div class="collapse" id="subjects">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.subjects.index') }}">All Subjects</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.subjects.create') }}">Add Subject</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#questions" data-bs-toggle="collapse" data-bs-target="#questions" aria-expanded="false" aria-controls="questions">
                            <span class="menu-title">Questions</span>
                            <i class="icon-question menu-icon"></i>
                        </a>
                        <div class="collapse" id="questions">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.questions.index') }}">All Questions</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.questions.create') }}">Add Question</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item nav-category"><span class="nav-link">Users</span></li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.answers.index') }}">
                            <span class="menu-title">All Users</span>
                            <i class="icon-people menu-icon"></i>
                        </a>
                    </li>

                    <li class="nav-item nav-category"><span class="nav-link">Account</span></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100" style="background:none; border:none;">
                                <span class="menu-title">Sign Out</span>
                                <i class="icon-power menu-icon"></i>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- ===== END SIDEBAR ===== -->

            <div class="main-panel">
                <div class="content-wrapper">

                    <!-- Summary Cards Row -->
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row report-inner-cards-wrapper">
                                        <div class="col-md-6 col-xl report-inner-card">
                                            <div class="inner-card-text">
                                                <span class="report-title">COURSES</span>
                                                <h4>{{ \App\Models\CourseName::count() }}</h4>
                                                <span class="report-count">Total Courses</span>
                                            </div>
                                            <div class="inner-card-icon bg-success">
                                                <i class="icon-book-open"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl report-inner-card">
                                            <div class="inner-card-text">
                                                <span class="report-title">SUBJECTS</span>
                                                <h4>{{ \App\Models\Subject::count() }}</h4>
                                                <span class="report-count">Total Subjects</span>
                                            </div>
                                            <div class="inner-card-icon bg-danger">
                                                <i class="icon-layers"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl report-inner-card">
                                            <div class="inner-card-text">
                                                <span class="report-title">QUESTIONS</span>
                                                <h4>{{ \App\Models\Question::count() }}</h4>
                                                <span class="report-count">Total Questions</span>
                                            </div>
                                            <div class="inner-card-icon bg-warning">
                                                <i class="icon-question"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl report-inner-card">
                                            <div class="inner-card-text">
                                                <span class="report-title">USERS</span>
                                                <h4>{{ \App\Models\User::count() }}</h4>
                                                <span class="report-count">Total Users</span>
                                            </div>
                                            <div class="inner-card-icon bg-primary">
                                                <i class="icon-people"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Classes & Study Materials Management -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h4 class="mb-1 fw-bold text-dark"><i class="icon-graduation me-2 text-primary"></i>Classes & Study Materials</h4>
                                        <p class="text-muted mb-0 small">Create study classes and assign serialized study materials (videos, PDFs, images) to them.</p>
                                    </div>
                                    <div class="d-flex gap-2 mt-2 mt-sm-0">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addClassModal" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                            <i class="icon-plus me-1"></i> Add Class
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#assignResourceModal" data-bs-toggle="modal" data-bs-target="#assignResourceModal">
                                            <i class="icon-link me-1"></i> Assign Study Material
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($classes->isEmpty())
                                        <div class="text-center py-5 text-muted">
                                            <i class="icon-graduation d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5>No Classes Available</h5>
                                            <p class="mb-0">Click the "Add Class" button above to create your first class category.</p>
                                        </div>
                                    @else
                                        <div class="row">
                                            @foreach($classes as $class)
                                                <div class="col-md-12 mb-4">
                                                    <div class="card border shadow-sm" style="border-radius: 10px;">
                                                        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h5 class="mb-0 fw-bold text-dark">{{ $class->name }}</h5>
                                                                <small class="text-muted">{{ $class->description ?? 'No description provided.' }}</small>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge badge-info">{{ $class->resources->count() }} Materials</span>
                                                                <span class="badge bg-purple text-white">{{ $class->questions->count() }} MCQs</span>
                                                                <button type="button" class="btn btn-purple btn-xs text-white bg-purple" 
                                                                        data-toggle="modal" data-target="#createMcqModal{{ $class->id }}" 
                                                                        data-bs-toggle="modal" data-bs-target="#createMcqModal{{ $class->id }}" 
                                                                        title="Add MCQ Question">
                                                                    <i class="icon-question"></i> Add MCQ
                                                                </button>
                                                                <button type="button" class="btn btn-primary btn-xs edit-class-btn" 
                                                                        data-id="{{ $class->id }}" 
                                                                        data-name="{{ $class->name }}" 
                                                                        data-description="{{ $class->description }}" 
                                                                        data-sort="{{ $class->sort_order }}"
                                                                        data-toggle="modal" data-target="#editClassModal"
                                                                        data-bs-toggle="modal" data-bs-target="#editClassModal"
                                                                        title="Edit Class">
                                                                    <i class="icon-pencil"></i>
                                                                </button>
                                                                <form action="{{ route('dashboard.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this class? Materials inside it will be unassigned.')">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs" title="Delete Class">
                                                                        <i class="icon-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-3">
                                                            @if($class->resources->isEmpty() && $class->questions->isEmpty())
                                                                <p class="text-muted text-center py-3 mb-0 small">No study materials or MCQ questions assigned to this class yet. Click "Assign Study Material" or "Add MCQ" to add some!</p>
                                                            @else
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th style="width: 80px;">Serial / Order</th>
                                                                                <th>Title / Question</th>
                                                                                <th>Type</th>
                                                                                <th>Subject</th>
                                                                                <th>Info</th>
                                                                                <th>Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            {{-- 1. Class Resources (Videos, PDFs, Images) --}}
                                                                            @foreach($class->resources as $res)
                                                                                <tr>
                                                                                    <td>
                                                                                        <input type="number" class="form-control form-control-sm res-sort-input text-center fw-bold" 
                                                                                               data-id="{{ $res->id }}" value="{{ $res->sort_order }}" style="width: 70px;">
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="fw-semibold">{{ $res->title }}</div>
                                                                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 300px;">{{ $res->file_name }}</small>
                                                                                    </td>
                                                                                    <td>
                                                                                        @if($res->type === 'video')
                                                                                            <span class="badge badge-primary"><i class="icon-film me-1"></i> Video</span>
                                                                                        @elseif($res->type === 'pdf')
                                                                                            <span class="badge badge-danger"><i class="icon-doc me-1"></i> PDF</span>
                                                                                        @else
                                                                                            <span class="badge badge-success"><i class="icon-picture me-1"></i> Image</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="text-muted">{{ $res->subject ?? '—' }}</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="text-muted">{{ $res->file_size_human }}</span>
                                                                                    </td>
                                                                                    <td>
                                                                                      <div class="d-flex gap-2">
                                                                             @if($res->type === 'video')
                                                                                 <button type="button" class="btn btn-outline-primary btn-xs play-video-btn" 
                                                                                         data-toggle="modal" data-target="#videoPlayerModal"
                                                                                         data-bs-toggle="modal" data-bs-target="#videoPlayerModal"
                                                                                         data-url="{{ $res->file_url }}" 
                                                                                         data-title="{{ $res->title }}">
                                                                                     <i class="icon-control-play"></i> Play
                                                                                 </button>
                                                                             @else
                                                                                 <a href="{{ $res->file_url }}" target="_blank" class="btn btn-outline-primary btn-xs">
                                                                                     <i class="icon-link"></i> View
                                                                                 </a>
                                                                             @endif
                                                                             <form action="{{ route('dashboard.resources.remove', $res->id) }}" method="POST">
                                                                                                                @csrf
                                                                                                                <button type="submit" class="btn btn-warning btn-xs">
                                                                                                                    <i class="icon-close"></i> Unassign
                                                                                                                </button>
                                                                                                            </form>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach

                                                                            {{-- 2. Class MCQ Questions --}}
                                                                            @foreach($class->questions as $q)
                                                                                <tr style="background-color: #fcfaff;">
                                                                                    <td>
                                                                                        <input type="number" class="form-control form-control-sm q-sort-input text-center fw-bold text-primary" 
                                                                                               data-id="{{ $q->id }}" value="{{ $q->sort_order }}" style="width: 70px; border-color: #8f5fe8;">
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="fw-bold text-dark"><i class="icon-question me-1 text-purple"></i> {{ $q->question }}</div>
                                                                                        <small class="text-muted">{{ $q->answers->count() }} Options (MCQ)</small>
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="badge bg-purple text-white"><i class="icon-question me-1"></i> MCQ</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="text-muted">{{ optional($q->subject)->name ?? '—' }}</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <span class="text-muted small">{{ $q->answers->count() }} Options</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="d-flex gap-2">
                                                                                            <button type="button" class="btn btn-outline-purple btn-xs"
                                                                                                    data-toggle="modal" data-target="#previewMcqModal{{ $q->id }}"
                                                                                                    data-bs-toggle="modal" data-bs-target="#previewMcqModal{{ $q->id }}">
                                                                                                <i class="icon-eye"></i> View
                                                                                            </button>
                                                                                            <form action="{{ route('admin.classes.destroy-question', $q->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this MCQ question?')">
                                                                                                @csrf @method('DELETE')
                                                                                                <button type="submit" class="btn btn-danger btn-xs">
                                                                                                    <i class="icon-trash"></i> Delete
                                                                                                </button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="d-flex justify-content-end mt-3">
                                                                    <button type="button" class="btn btn-secondary btn-sm save-sorting-btn" data-class-id="{{ $class->id }}">
                                                                        <i class="icon-refresh"></i> Save Serialization Order
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- content-wrapper ends -->

                <!-- ══════════════════════════════════════════════════════
                     MODAL: ADD CLASS
                ══════════════════════════════════════════════════════ -->
                <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="addClassModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('dashboard.classes.store') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="addClassModalLabel">Create Study Class</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="class_name" class="fw-semibold">Class Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="class_name" class="form-control" placeholder="e.g. Class One, Class Two" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="class_description" class="fw-semibold">Description</label>
                                        <textarea name="description" id="class_description" rows="3" class="form-control" placeholder="Optional brief details"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="class_sort" class="fw-semibold">Display Priority Order</label>
                                        <input type="number" name="sort_order" id="class_sort" class="form-control" value="0">
                                        <small class="text-muted">Lower numbers appear first.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Class</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     MODAL: EDIT CLASS
                ══════════════════════════════════════════════════════ -->
                <div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="editClassModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" id="editClassForm" action="">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="editClassModalLabel">Edit Study Class</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="edit_class_name" class="fw-semibold">Class Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="edit_class_name" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_class_description" class="fw-semibold">Description</label>
                                        <textarea name="description" id="edit_class_description" rows="3" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_class_sort" class="fw-semibold">Display Priority Order</label>
                                        <input type="number" name="sort_order" id="edit_class_sort" class="form-control">
                                        <small class="text-muted">Lower numbers appear first.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Class</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     MODAL: ASSIGN STUDY MATERIAL
                ══════════════════════════════════════════════════════ -->
                <div class="modal fade" id="assignResourceModal" tabindex="-1" role="dialog" aria-labelledby="assignResourceModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header pb-0 border-bottom-0">
                                <h5 class="modal-title fw-bold" id="assignResourceModalLabel">Assign Study Material</h5>
                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <!-- Tab Switcher -->
                            <div class="px-3 pt-2">
                                <ul class="nav nav-pills nav-fill bg-light p-1 rounded" id="assignTabs" style="font-size: 0.9rem;">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active py-2 border-0 w-100" id="tab-assign-existing" style="cursor: pointer;">
                                            <i class="icon-link me-1"></i> Assign Existing
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link py-2 border-0 w-100" id="tab-upload-new" style="cursor: pointer;">
                                            <i class="icon-cloud-upload me-1"></i> Upload &amp; Assign New
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <div class="modal-body pt-3">
                                <!-- TAB 1: ASSIGN EXISTING FORM -->
                                <div id="pane-assign-existing">
                                    <form method="POST" action="{{ route('dashboard.resources.assign') }}">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="study_class_id" class="fw-semibold">Target Class <span class="text-danger">*</span></label>
                                            <select name="study_class_id" id="study_class_id" class="form-select form-control" required>
                                                <option value="">-- Choose Class --</option>
                                                @foreach($classes as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="resource_id" class="fw-semibold">Select Study Material <span class="text-danger">*</span></label>
                                            <select name="resource_id" id="resource_id" class="form-select form-control" required>
                                                <option value="">-- Select Material --</option>
                                                @foreach($unassignedResources as $u)
                                                    <option value="{{ $u->id }}">{{ $u->title }} ({{ ucfirst($u->type) }})</option>
                                                @endforeach
                                            </select>
                                            @if($unassignedResources->isEmpty())
                                                <small class="text-danger d-block mt-1">No unassigned resources found. Use the Upload tab to add new files!</small>
                                            @endif
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="res_sort" class="fw-semibold">Serial Order / Sort Number</label>
                                            <input type="number" name="sort_order" id="res_sort" class="form-control" value="1" min="0">
                                            <small class="text-muted">Serial position (e.g. 1, 2, 3...) of this material inside the class.</small>
                                        </div>
                                        <div class="modal-footer px-0 pb-0 border-top-0 mt-4">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" {{ $unassignedResources->isEmpty() ? 'disabled' : '' }}>Assign Material</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- TAB 2: UPLOAD AND ASSIGN NEW FORM -->
                                <div id="pane-upload-new" class="d-none">
                                    <form method="POST" id="upload_assign_form" action="{{ route('dashboard.resources.upload_assign') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="upload_study_class_id" class="fw-semibold">Target Class <span class="text-danger">*</span></label>
                                            <select name="study_class_id" id="upload_study_class_id" class="form-select form-control" required>
                                                <option value="">-- Choose Class --</option>
                                                @foreach($classes as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="upload_type" class="fw-semibold">Material Type <span class="text-danger">*</span></label>
                                            <select name="type" id="upload_type" class="form-select form-control" required>
                                                <option value="video" selected>Video (MP4, AVI, WebM)</option>
                                                <option value="pdf">PDF Document</option>
                                                <option value="image">Image (JPEG, PNG, WebP)</option>
                                                <option value="mcq">MCQ Question (Interactive Quiz)</option>
                                            </select>
                                        </div>

                                        <!-- Container for File/Resource specific fields -->
                                        <div id="resource-fields-container">
                                            <div class="form-group mb-3">
                                                <label for="upload_title" class="fw-semibold">Material Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" id="upload_title" class="form-control" placeholder="e.g. Lecture 1 Video, Chapter 2 PDF" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_file" class="fw-semibold">Select File <span class="text-danger">*</span></label>
                                                <input type="file" name="file" id="upload_file" class="form-control" required>
                                                <small class="text-muted" id="file_type_help">Supported file: Video (Max 500MB - MP4, AVI, WebM)</small>
                                            </div>

                                            <div class="form-group mb-3" id="thumbnail_group">
                                                <label for="upload_thumbnail" class="fw-semibold">Video Thumbnail (Optional)</label>
                                                <input type="file" name="thumbnail" id="upload_thumbnail" class="form-control">
                                                <small class="text-muted">Supported: JPEG, PNG, WebP (Max 2MB)</small>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_subject" class="fw-semibold">Subject / Topic (Optional)</label>
                                                <input type="text" name="subject" id="upload_subject" class="form-control" placeholder="e.g. Physics, Biology">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_description" class="fw-semibold">Description (Optional)</label>
                                                <textarea name="description" id="upload_description" rows="3" class="form-control" placeholder="Brief details about the resource"></textarea>
                                            </div>
                                        </div>

                                        <!-- Container for MCQ Question specific fields -->
                                        <div id="mcq-fields-container" class="d-none">
                                            <div class="form-group mb-3">
                                                <label for="upload_subject_id" class="fw-semibold">Subject / Topic <span class="text-danger">*</span></label>
                                                <select name="subject_id" id="upload_subject_id" class="form-select form-control">
                                                    <option value="">-- Choose Subject --</option>
                                                    @foreach($subjects as $subj)
                                                        <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_question" class="fw-semibold">Question Text <span class="text-danger">*</span></label>
                                                <textarea name="question" id="upload_question" rows="3" class="form-control" placeholder="Type the question here..."></textarea>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label for="upload_ans_a" class="fw-semibold">Option A <span class="text-danger">*</span></label>
                                                <input type="text" name="answers[0]" id="upload_ans_a" class="form-control" placeholder="Answer option A">
                                            </div>

                                            <div class="form-group mb-2">
                                                <label for="upload_ans_b" class="fw-semibold">Option B <span class="text-danger">*</span></label>
                                                <input type="text" name="answers[1]" id="upload_ans_b" class="form-control" placeholder="Answer option B">
                                            </div>

                                            <div class="form-group mb-2">
                                                <label for="upload_ans_c" class="fw-semibold">Option C <span class="text-danger">*</span></label>
                                                <input type="text" name="answers[2]" id="upload_ans_c" class="form-control" placeholder="Answer option C">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_ans_d" class="fw-semibold">Option D <span class="text-danger">*</span></label>
                                                <input type="text" name="answers[3]" id="upload_ans_d" class="form-control" placeholder="Answer option D">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="upload_correct_index" class="fw-semibold">Correct Option <span class="text-danger">*</span></label>
                                                <select name="correct_index" id="upload_correct_index" class="form-select form-control">
                                                    <option value="0" selected>Option A</option>
                                                    <option value="1">Option B</option>
                                                    <option value="2">Option C</option>
                                                    <option value="3">Option D</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Shared sort order field -->
                                        <div class="form-group mb-3">
                                            <label for="upload_sort" class="fw-semibold">Serial Order / Sort Number</label>
                                            <input type="number" name="sort_order" id="upload_sort" class="form-control" value="1" min="0">
                                            <small class="text-muted">Serial position (e.g. 1, 2, 3...) of this item inside the class.</small>
                                        </div>

                                        <!-- Progress Bar Container (Hidden by default) -->
                                        <div id="upload-progress-container" class="d-none mt-3">
                                            <div class="progress" style="height: 25px;">
                                                <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2 text-muted small">
                                                <span id="upload-progress-status" class="fw-semibold text-primary">Uploading...</span>
                                                <span id="upload-progress-stats" class="fw-semibold">0.00 MB / 0.00 MB</span>
                                            </div>
                                        </div>

                                        <div class="modal-footer px-0 pb-0 border-top-0 mt-4">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="upload_submit_btn" class="btn btn-success">Upload &amp; Assign</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     MODAL: VIDEO PLAYER
                ══════════════════════════════════════════════════════ -->
                <div class="modal fade" id="videoPlayerModal" tabindex="-1" role="dialog" aria-labelledby="videoPlayerModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content" style="background-color: #1a1a1a; border-radius: 12px; border: 1px solid #333; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                            <div class="modal-header" style="border-bottom: 1px solid #2d2d2d; padding: 15px 20px;">
                                <h5 class="modal-title fw-bold text-white" id="videoPlayerModalLabel">Play Video</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; background: none; border: none; opacity: 0.8;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0" style="background-color: #000; position: relative;">
                                <div class="ratio ratio-16x9">
                                    <video id="dashboardVideoPlayer" class="w-100" controls controlsList="nodownload" playsinline style="max-height: 70vh; background-color: #000;">
                                        <source id="videoPlayerSource" src="" type="">
                                        Your browser does not support the video tag or this format/codec.
                                    </video>
                                </div>
                                <div id="videoCodecWarning" class="alert alert-warning m-3 d-none" role="alert" style="font-size: 0.85rem; border-radius: 6px; border: none; background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                                    <i class="icon-info me-1"></i> <strong>Note:</strong> MKV/H.265 videos may not play natively in some web browsers. If you see a black screen or hear audio only, try playing it in a media player like VLC or uploading an MP4 (H.264) file.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════
                     MODALS: MCQ QUESTION CREATION & PREVIEW (Per Class)
                ══════════════════════════════════════════════════════ -->
                @foreach($classes as $c)
                    <!-- Create MCQ Modal -->
                    <div class="modal fade" id="createMcqModal{{ $c->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.classes.store-question') }}">
                                    @csrf
                                    <input type="hidden" name="study_class_id" value="{{ $c->id }}">

                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold"><i class="icon-question me-2 text-purple"></i>Create MCQ Question for {{ $c->name }}</h5>
                                        <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.5rem;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label fw-bold">Question Text / Prompt <span class="text-danger">*</span></label>
                                                <textarea name="question" class="form-control" rows="3" placeholder="Enter MCQ question prompt..." required></textarea>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label fw-bold">Subject</label>
                                                <select name="subject_id" class="form-select form-control">
                                                    <option value="">-- Select Subject --</option>
                                                    @foreach($subjects as $subj)
                                                        <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="mt-3">
                                                    <label class="form-label fw-bold">Serial / Order Position</label>
                                                    <input type="number" name="sort_order" class="form-control" value="{{ $c->questions->count() + 1 }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mt-2 mb-3 text-purple">Answer Options (Select correct option):</h6>
                                        
                                        @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                                            <div class="input-group mb-2">
                                                <div class="input-group-text bg-purple text-white fw-bold d-flex align-items-center">
                                                    <input class="form-check-input mt-0 me-2" type="radio" name="correct_index" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }}>
                                                    Option {{ $label }}
                                                </div>
                                                <input type="text" name="answers[]" class="form-control" placeholder="Enter option {{ $label }} text" required>
                                            </div>
                                        @endforeach
                                        <small class="text-muted">Radio button marks the correct answer option.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-purple text-white bg-purple">Save MCQ Question</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Preview MCQ Modals -->
                    @foreach($c->questions as $q)
                        <div class="modal fade" id="previewMcqModal{{ $q->id }}" tabindex="-1" role="hidden" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-purple text-white">
                                        <h5 class="modal-title fw-bold text-white"><i class="icon-question me-2"></i>MCQ Assessment</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.5rem; opacity: 0.8;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="fw-bold mb-3 text-dark">{{ $q->question }}</h5>
                                        <div class="list-group mb-3">
                                            @foreach($q->answers as $aIndex => $ans)
                                                <div class="list-group-item d-flex justify-content-between align-items-center {{ $ans->is_correct ? 'list-group-item-success' : '' }}">
                                                    <span><strong>{{ chr(65 + $aIndex) }}.</strong> {{ $ans->answer }}</span>
                                                    @if($ans->is_correct)
                                                        <span class="badge bg-success text-white"><i class="icon-check me-1"></i> Correct Answer</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Copyright &copy; {{ date('Y') }} NeetCommon. All rights reserved.
                        </span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                            Hand-crafted &amp; made with <i class="icon-heart text-danger"></i>
                        </span>
                    </div>
                </footer>
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('src/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('src/assets/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('src/assets/vendors/chartist/chartist.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('src/assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('src/assets/js/misc.js') }}"></script>
    <script src="{{ asset('src/assets/js/settings.js') }}"></script>

    <script>
    (function () {
        // Tab switching in Assign/Upload modal
        const tabExisting = document.getElementById('tab-assign-existing');
        const tabUpload = document.getElementById('tab-upload-new');
        const paneExisting = document.getElementById('pane-assign-existing');
        const paneUpload = document.getElementById('pane-upload-new');

        if (tabExisting && tabUpload) {
            tabExisting.addEventListener('click', function () {
                tabExisting.classList.add('active');
                tabUpload.classList.remove('active');
                paneExisting.classList.remove('d-none');
                paneUpload.classList.add('d-none');
            });

            tabUpload.addEventListener('click', function () {
                tabUpload.classList.add('active');
                tabExisting.classList.remove('active');
                paneUpload.classList.remove('d-none');
                paneExisting.classList.add('d-none');
            });
        }

        // Toggle thumbnail and help text based on file upload type
        const uploadType = document.getElementById('upload_type');
        const thumbnailGroup = document.getElementById('thumbnail_group');
        const fileTypeHelp = document.getElementById('file_type_help');
        const uploadAssignForm = document.getElementById('upload_assign_form');
        const resourceFieldsContainer = document.getElementById('resource-fields-container');
        const mcqFieldsContainer = document.getElementById('mcq-fields-container');
        const uploadSubmitBtn = document.getElementById('upload_submit_btn');

        // Form inputs
        const uploadTitle = document.getElementById('upload_title');
        const uploadFile = document.getElementById('upload_file');
        const uploadSubjectId = document.getElementById('upload_subject_id');
        const uploadQuestion = document.getElementById('upload_question');
        const uploadAnsA = document.getElementById('upload_ans_a');
        const uploadAnsB = document.getElementById('upload_ans_b');
        const uploadAnsC = document.getElementById('upload_ans_c');
        const uploadAnsD = document.getElementById('upload_ans_d');

        if (uploadType) {
            uploadType.addEventListener('change', function () {
                const val = this.value;
                if (val === 'mcq') {
                    // Hide Resource Fields, Show MCQ Fields
                    resourceFieldsContainer.classList.add('d-none');
                    mcqFieldsContainer.classList.remove('d-none');
                    
                    // Toggle required flags
                    if (uploadTitle) uploadTitle.required = false;
                    if (uploadFile) uploadFile.required = false;
                    if (uploadSubjectId) uploadSubjectId.required = true;
                    if (uploadQuestion) uploadQuestion.required = true;
                    if (uploadAnsA) uploadAnsA.required = true;
                    if (uploadAnsB) uploadAnsB.required = true;
                    if (uploadAnsC) uploadAnsC.required = true;
                    if (uploadAnsD) uploadAnsD.required = true;

                    // Update form action & submit button text
                    if (uploadAssignForm) {
                        uploadAssignForm.action = "{{ route('admin.classes.store-question') }}";
                    }
                    if (uploadSubmitBtn) {
                        uploadSubmitBtn.textContent = "Save MCQ Question";
                    }
                } else {
                    // Hide MCQ Fields, Show Resource Fields
                    resourceFieldsContainer.classList.remove('d-none');
                    mcqFieldsContainer.classList.add('d-none');

                    // Toggle required flags
                    if (uploadTitle) uploadTitle.required = true;
                    if (uploadFile) uploadFile.required = true;
                    if (uploadSubjectId) uploadSubjectId.required = false;
                    if (uploadQuestion) uploadQuestion.required = false;
                    if (uploadAnsA) uploadAnsA.required = false;
                    if (uploadAnsB) uploadAnsB.required = false;
                    if (uploadAnsC) uploadAnsC.required = false;
                    if (uploadAnsD) uploadAnsD.required = false;

                    // Update form action & submit button text
                    if (uploadAssignForm) {
                        uploadAssignForm.action = "{{ route('dashboard.resources.upload_assign') }}";
                    }
                    if (uploadSubmitBtn) {
                        uploadSubmitBtn.textContent = "Upload & Assign";
                    }

                    // Resource Type specifics
                    if (val === 'video') {
                        if (thumbnailGroup) thumbnailGroup.classList.remove('d-none');
                        if (fileTypeHelp) fileTypeHelp.textContent = 'Supported file: Video (Max 500MB - MP4, AVI, WebM)';
                    } else if (val === 'pdf') {
                        if (thumbnailGroup) thumbnailGroup.classList.add('d-none');
                        if (fileTypeHelp) fileTypeHelp.textContent = 'Supported file: PDF (Max 50MB)';
                    } else {
                        if (thumbnailGroup) thumbnailGroup.classList.add('d-none');
                        if (fileTypeHelp) fileTypeHelp.textContent = 'Supported file: Image (Max 10MB - JPEG, PNG, WebP)';
                    }
                }
            });
        }

        // Edit Class modal fill values
        document.querySelectorAll('.edit-class-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const classId = this.dataset.id;
                const name = this.dataset.name;
                const description = this.dataset.description;
                const sort = this.dataset.sort;

                const form = document.getElementById('editClassForm');
                form.action = `/dashboard/classes/${classId}`;

                document.getElementById('edit_class_name').value = name;
                document.getElementById('edit_class_description').value = description || '';
                document.getElementById('edit_class_sort').value = sort || 0;
            });
        });

        // Handle serialization order updates via AJAX
        document.querySelectorAll('.save-sorting-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const classId = this.dataset.classId;
                const cardBody = this.closest('.card-body');
                const resourceInputs = cardBody.querySelectorAll('.res-sort-input');
                const questionInputs = cardBody.querySelectorAll('.q-sort-input');

                const resourceOrders = {};
                const questionOrders = {};

                resourceInputs.forEach(function (input) {
                    resourceOrders[input.dataset.id] = input.value;
                });

                questionInputs.forEach(function (input) {
                    questionOrders[input.dataset.id] = input.value;
                });

                btn.disabled = true;
                btn.innerHTML = '<i class="icon-refresh spin"></i> Updating...';

                fetch('{{ route("admin.classes.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        resource_orders: resourceOrders,
                        question_orders: questionOrders
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Something went wrong.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="icon-refresh"></i> Save Serialization Order';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving order.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="icon-refresh"></i> Save Serialization Order';
                });
            });
        });

        // Video Player Modal triggers and controls
        const videoPlayerModal = document.getElementById('videoPlayerModal');
        const videoPlayer = document.getElementById('dashboardVideoPlayer');
        const videoSource = document.getElementById('videoPlayerSource');
        const modalTitle = document.getElementById('videoPlayerModalLabel');
        const codecWarning = document.getElementById('videoCodecWarning');

        document.querySelectorAll('.play-video-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const url = this.dataset.url;
                const title = this.dataset.title;

                modalTitle.textContent = title;
                videoSource.src = url;

                const extension = url.split('.').pop().toLowerCase();
                if (extension === 'mkv') {
                    videoSource.type = 'video/x-matroska';
                    codecWarning.classList.remove('d-none');
                } else if (extension === 'webm') {
                    videoSource.type = 'video/webm';
                    codecWarning.classList.add('d-none');
                } else {
                    videoSource.type = 'video/mp4';
                    codecWarning.classList.add('d-none');
                }

                videoPlayer.load();
                videoPlayer.play().catch(e => console.log("Autoplay blocked or format unsupported:", e));
            });
        });

        if (videoPlayerModal) {
            videoPlayerModal.addEventListener('hidden.bs.modal', function () {
                videoPlayer.pause();
                videoSource.src = '';
            });

            if (window.$) {
                $(videoPlayerModal).on('hidden.bs.modal', function () {
                    videoPlayer.pause();
                    videoSource.src = '';
                });
            }
        }

        // Handle File upload with progress bar using Chunked uploads
        if (uploadAssignForm) {
            uploadAssignForm.addEventListener('submit', function (e) {
                const uploadTypeVal = uploadType ? uploadType.value : 'video';
                if (uploadTypeVal === 'mcq') {
                    // Submit normally for MCQ
                    return;
                }

                // Prevent standard submit for file resource upload
                e.preventDefault();

                // Check if file is selected
                if (!uploadFile || !uploadFile.files || uploadFile.files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const file = uploadFile.files[0];

                // Disable submit button and close button to prevent double submit/cancelling unexpectedly
                if (uploadSubmitBtn) {
                    uploadSubmitBtn.disabled = true;
                    uploadSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
                }
                const closeBtn = uploadAssignForm.querySelector('[data-dismiss="modal"], [data-bs-dismiss="modal"]');
                if (closeBtn) closeBtn.disabled = true;

                // Show progress container
                const progressContainer = document.getElementById('upload-progress-container');
                const progressBar = document.getElementById('upload-progress-bar');
                const progressStatus = document.getElementById('upload-progress-status');
                const progressStats = document.getElementById('upload-progress-stats');

                if (progressContainer) progressContainer.classList.remove('d-none');
                if (progressBar) {
                    progressBar.style.width = '0%';
                    progressBar.textContent = '0%';
                    progressBar.setAttribute('aria-valuenow', '0');
                }
                if (progressStatus) progressStatus.textContent = 'Preparing upload...';
                if (progressStats) progressStats.textContent = '';

                // Chunk parameters: 900KB per chunk (to stay safely under server's 1MB/2MB default limits)
                const chunkSize = 900 * 1024;
                const totalChunks = Math.ceil(file.size / chunkSize);
                const fileUuid = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                
                let currentChunk = 0;

                function uploadNextChunk() {
                    const start = currentChunk * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const chunkData = new FormData();
                    chunkData.append('file_uuid', fileUuid);
                    chunkData.append('chunk_index', currentChunk);
                    chunkData.append('total_chunks', totalChunks);
                    chunkData.append('file_name', file.name);
                    chunkData.append('file_chunk', chunk);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route("dashboard.resources.upload_chunk") }}', true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            const uploadedBytesPrior = currentChunk * chunkSize;
                            const currentUploadedBytes = event.loaded;
                            const totalUploadedBytes = Math.min(uploadedBytesPrior + currentUploadedBytes, file.size);
                            const percentComplete = Math.round((totalUploadedBytes / file.size) * 100);
                            const loadedMB = (totalUploadedBytes / (1024 * 1024)).toFixed(2);
                            const totalMB = (file.size / (1024 * 1024)).toFixed(2);

                            if (progressBar) {
                                progressBar.style.width = percentComplete + '%';
                                progressBar.textContent = percentComplete + '%';
                                progressBar.setAttribute('aria-valuenow', percentComplete);
                            }
                            if (progressStatus) progressStatus.textContent = 'Uploading chunk ' + (currentChunk + 1) + ' of ' + totalChunks + '...';
                            if (progressStats) progressStats.textContent = loadedMB + ' MB / ' + totalMB + ' MB';
                        }
                    });

                    xhr.addEventListener('load', function () {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            let responseObj;
                            try {
                                responseObj = JSON.parse(xhr.responseText);
                            } catch (e) {
                                handleUploadError('Invalid response from chunk server.');
                                return;
                            }

                            currentChunk++;

                            if (currentChunk < totalChunks) {
                                uploadNextChunk();
                            } else {
                                // All chunks uploaded! Send final assignment request
                                if (progressStatus) progressStatus.textContent = 'Upload complete! Processing and saving...';
                                submitFinalForm(responseObj.temp_file_path);
                            }
                        } else {
                            handleUploadError('Chunk upload failed with status code ' + xhr.status);
                        }
                    });

                    xhr.addEventListener('error', function () {
                        handleUploadError('Network error occurred during chunk upload. Please check your connection.');
                    });

                    xhr.send(chunkData);
                }

                function submitFinalForm(tempFilePath) {
                    const finalData = new FormData(uploadAssignForm);
                    
                    // Remove the raw file input from the final request (we upload it via chunks)
                    finalData.delete('file');
                    
                    // Append the temp file reference info
                    finalData.append('temp_file_path', tempFilePath);
                    finalData.append('temp_file_name', file.name);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadAssignForm.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.addEventListener('load', function () {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            if (progressStatus) progressStatus.textContent = 'Done! Redirecting...';
                            if (progressBar) {
                                progressBar.classList.remove('bg-success');
                                progressBar.classList.add('bg-info');
                            }
                            window.location.reload();
                        } else {
                            let errorMsg = 'Final validation/save failed (status code ' + xhr.status + ')';
                            try {
                                const responseObj = JSON.parse(xhr.responseText);
                                if (responseObj.message) {
                                    errorMsg = responseObj.message;
                                } else if (responseObj.errors) {
                                    const errorList = [];
                                    for (const key in responseObj.errors) {
                                        if (responseObj.errors.hasOwnProperty(key)) {
                                            errorList.push(responseObj.errors[key].join(', '));
                                        }
                                    }
                                    if (errorList.length > 0) {
                                        errorMsg = errorList.join('\n');
                                    }
                                }
                            } catch (e) {}
                            handleUploadError(errorMsg);
                        }
                    });

                    xhr.addEventListener('error', function () {
                        handleUploadError('Network error during final save submission.');
                    });

                    xhr.send(finalData);
                }

                function handleUploadError(message) {
                    alert('Error:\n' + message);
                    if (progressStatus) progressStatus.textContent = 'Upload failed.';
                    if (uploadSubmitBtn) {
                        uploadSubmitBtn.disabled = false;
                        uploadSubmitBtn.innerHTML = 'Upload & Assign';
                    }
                    if (closeBtn) closeBtn.disabled = false;
                }

                // Start the upload flow
                uploadNextChunk();
            });
        }
    })();
    </script>
</body>
</html>
