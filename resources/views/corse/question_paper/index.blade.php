@extends('layouts.admin')
@section('title', 'Question Papers')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                
                {{-- Error & Success Alert Banner --}}
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

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-dark"><i class="icon-doc me-2 text-primary"></i>Question Papers Management</h4>
                        <p class="text-muted small mb-0">Create & publish Subject-Wise Mock Tests and Combined Multi-Subject Question Papers.</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-primary btn-sm fw-semibold" data-toggle="modal" data-target="#createPaperModal" data-bs-toggle="modal" data-bs-target="#createPaperModal">
                            <i class="icon-plus me-1"></i> Create Question Paper
                        </button>
                        <span class="badge badge-primary p-2">{{ $papers->total() }} Total Papers</span>
                    </div>
                </div>

                                                {{-- Two Main Sections: Mock Test vs Test Paper --}}
                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa;">
                    <div class="card-body p-2 d-flex gap-2">
                        <a href="{{ route('admin.question-papers.index', ['paper_type' => 'mocktest']) }}" 
                           class="btn flex-fill py-2 fw-bold rounded-3 {{ request('paper_type') === 'mocktest' || !request('paper_type') ? 'btn-info text-white shadow-sm' : 'btn-light text-muted' }}">
                           <i class="icon-layers me-2"></i> 1. Mock Test (Subject-Wise)
                        </a>
                        <a href="{{ route('admin.question-papers.index', ['paper_type' => 'combined']) }}" 
                           class="btn flex-fill py-2 fw-bold rounded-3 {{ request('paper_type') === 'combined' ? 'btn-purple text-white shadow-sm' : 'btn-light text-muted' }}" style="{{ request('paper_type') === 'combined' ? 'background-color: #6C63FF; border-color: #6C63FF; color: white;' : '' }}">
                           <i class="icon-doc me-2"></i> 2. Test Paper (Combined Papers)
                        </a>
                    </div>
                </div>

@if($papers->isEmpty())
                    <div class="text-center py-5 text-muted border rounded bg-light">
                        <i class="icon-doc d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5>No Question Papers Found</h5>
                        <p class="mb-3">Click the "Create Question Paper" button to generate a Subject-Wise Mock Test or Combined Question Paper.</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createPaperModal" data-bs-toggle="modal" data-bs-target="#createPaperModal">
                            <i class="icon-plus me-1"></i> Create Paper Now
                        </button>
                    </div>
                @else
                <div class="row g-4">
                    @foreach($papers as $paper)
                    <div class="col-md-4 mb-4">
                        <div class="card border shadow-sm h-100" style="border-radius:12px; overflow:hidden;">
                            {{-- Coloured header strip based on type --}}
                            <div style="background: {{ $paper->paper_type === 'mocktest' ? 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)' : 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)' }}; padding:20px 24px 16px;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge {{ $paper->paper_type === 'mocktest' ? 'bg-dark text-white' : 'bg-warning text-dark' }} fw-bold" style="font-size: 11px;">
                                        @if($paper->paper_type === 'mocktest')
                                            <i class="icon-layers me-1"></i> MOCK TEST ({{ optional($paper->subject)->name ?? 'Subject-Wise' }})
                                        @else
                                            <i class="icon-grid me-1"></i> COMBINED PAPER
                                        @endif
                                    </span>
                                    <small class="text-white opacity-75">{{ $paper->exam_year ?? date('Y') }}</small>
                                </div>
                                <h5 class="text-white fw-bold mb-0 mt-2">{{ $paper->title }}</h5>
                                @if($paper->course)
                                    <small class="badge bg-light text-dark mt-1"><i class="icon-book-open me-1"></i> {{ $paper->course->name }}</small>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <p class="text-muted small mb-3">{{ Str::limit($paper->description ?? 'No description provided.', 100) }}</p>

                                    {{-- Stats row --}}
                                    <div class="row text-center mb-3 g-0 border rounded py-2 bg-light">
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5 text-primary">{{ $paper->questions_count }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Questions</small>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="fw-bold fs-5 text-success">{{ $paper->total_marks }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Marks</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-5 text-warning">{{ $paper->duration_minutes }}</div>
                                            <small class="text-muted" style="font-size: 10px;">Minutes</small>
                                        </div>
                                    </div>

                                    {{-- Subject breakdown quotas badges --}}
                                    <div class="d-flex flex-wrap gap-1 mb-4">
                                        @if($paper->subject_quotas && is_array($paper->subject_quotas))
                                            @foreach($paper->subject_quotas as $subName => $qCount)
                                                <span class="badge bg-secondary text-dark px-2 py-1" style="font-size: 10px;">
                                                    {{ $subName }}: {{ $qCount }}
                                                </span>
                                            @endforeach
                                        @elseif($paper->subject)
                                            <span class="badge bg-info text-white px-2 py-1" style="font-size: 10px;">
                                                {{ $paper->subject->name }}: {{ $paper->questions_count }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.question-papers.show', $paper->id) }}"
                                       class="btn btn-primary btn-sm w-100 py-2 fw-semibold">
                                        <i class="icon-eye me-1"></i> View
                                    </a>
                                    <form action="{{ route('admin.question-papers.destroy', $paper->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Question Paper?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-2 px-3" title="Delete Paper">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $papers->links() }}</div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: CREATE QUESTION PAPER (2 TABS: MOCK TEST & COMBINED)
══════════════════════════════════════════════════════ -->
<div class="modal fade" id="createPaperModal" tabindex="-1" role="dialog" aria-labelledby="createPaperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="createPaperModalLabel"><i class="icon-doc text-primary me-2"></i>Create New Question Paper</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="px-3 pt-3">
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded" id="paperTabs">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-2 fw-semibold" id="btn-tab-mocktest" onclick="switchPaperTab('mocktest')">
                            <i class="icon-layers me-1"></i> 1. Subject-Wise Mock Test
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 fw-semibold" id="btn-tab-combined" onclick="switchPaperTab('combined')">
                            <i class="icon-grid me-1"></i> 2. Combined Question Paper
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body pt-3">
                <!-- FORM 1: SUBJECT-WISE MOCK TEST -->
                <div id="pane-mocktest">
                    <form method="POST" action="{{ route('admin.question-papers.store') }}">
                        @csrf
                        <input type="hidden" name="paper_type" value="mocktest">

                        <div class="alert alert-info py-2 small mb-3">
                            <i class="icon-info me-1"></i> Generate a subject-specific mock test paper (max limit: 180 questions) from your question bank for a chosen course &amp; subject.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mock_course_id" class="fw-semibold">Select Course <small class="text-muted">(Optional)</small></label>
                                <select name="course_id" id="mock_course_id" class="form-select form-control" onchange="filterMockSubjects(this.value)">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $crs)
                                        <option value="{{ $crs->id }}">{{ $crs->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="mock_subject_id" class="fw-semibold">Select Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="mock_subject_id" class="form-select form-control" required>
                                    <option value="">-- Choose Subject --</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" data-course-id="{{ $sub->course_id }}">
                                            {{ $sub->name }} ({{ $sub->questions_count }} questions available)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mock_title" class="fw-semibold">Paper Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="mock_title" class="form-control" placeholder="e.g. Biology Full Chapter Mock Test 1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mock_limit" class="fw-semibold">Question Limit (Max 180)</label>
                                <input type="number" name="limit" id="mock_limit" class="form-control" value="180" min="1" max="180" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="mock_desc" class="fw-semibold">Description</label>
                            <textarea name="description" id="mock_desc" rows="2" class="form-control" placeholder="Optional brief details..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mock_exam_name" class="fw-semibold">Exam Name</label>
                                    <input type="text" name="exam_name" id="mock_exam_name" class="form-control" value="NEET Subject Mock">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mock_duration" class="fw-semibold">Duration (Minutes)</label>
                                    <input type="number" name="duration_minutes" id="mock_duration" class="form-control" value="180">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="mock_marks" class="fw-semibold">Total Marks</label>
                                    <input type="number" name="total_marks" id="mock_marks" class="form-control" value="720">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success fw-semibold"><i class="icon-magic-wand me-1"></i> Generate Subject Mock Test</button>
                        </div>
                    </form>
                </div>

                <!-- FORM 2: COMBINED QUESTION PAPER -->
                <div id="pane-combined" style="display: none;">
                    <form method="POST" action="{{ route('admin.question-papers.store') }}">
                        @csrf
                        <input type="hidden" name="paper_type" value="combined">

                        <div class="alert alert-purple py-2 small mb-3" style="background-color: #f3ebff; color: #5a189a;">
                            <i class="icon-info me-1"></i> Create a multi-subject combined question paper. You can dynamically specify how many questions to pull per subject (e.g. Physics: 45, Chemistry: 45, Biology: 90). Total limit: 180 questions.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="comb_course_id" class="fw-semibold">Select Course <small class="text-muted">(Optional)</small></label>
                                <select name="course_id" id="comb_course_id" class="form-select form-control">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $crs)
                                        <option value="{{ $crs->id }}">{{ $crs->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="comb_title" class="fw-semibold">Paper Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="comb_title" class="form-control" placeholder="e.g. NEET Grand Combined Test Series #1" required>
                            </div>
                        </div>

                        <div class="card p-3 mb-3 border bg-light">
                            <h6 class="fw-bold text-dark mb-2"><i class="icon-sliders me-1 text-primary"></i> Dynamic Per-Subject Question Distribution (Max 180 Total):</h6>
                            <div class="row">
                                @foreach($subjects as $sub)
                                    <div class="col-md-4 mb-2">
                                        <label for="quota_{{ $sub->id }}" class="form-label small fw-semibold text-truncate d-block mb-1" title="{{ $sub->name }}">
                                            {{ $sub->name }} <span class="text-muted">({{ $sub->questions_count }} avail)</span>
                                        </label>
                                        @php
                                            $defaultVal = 45;
                                            if (str_contains(strtolower($sub->name), 'bio')) $defaultVal = 90;
                                        @endphp
                                        <input type="number" name="quotas[{{ $sub->id }}]" id="quota_{{ $sub->id }}" 
                                               class="form-control form-control-sm quota-input" 
                                               value="{{ min($defaultVal, $sub->questions_count) }}" min="0" max="180">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="comb_desc" class="fw-semibold">Description</label>
                            <textarea name="description" id="comb_desc" rows="2" class="form-control" placeholder="Brief description of combined test..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="comb_exam_name" class="fw-semibold">Exam Name</label>
                                    <input type="text" name="exam_name" id="comb_exam_name" class="form-control" value="NEET Combined Test">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="comb_duration" class="fw-semibold">Duration (Minutes)</label>
                                    <input type="number" name="duration_minutes" id="comb_duration" class="form-control" value="180">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="comb_marks" class="fw-semibold">Total Marks</label>
                                    <input type="number" name="total_marks" id="comb_marks" class="form-control" value="720">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-semibold"><i class="icon-magic-wand me-1"></i> Generate Combined Paper</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchPaperTab(tab) {
    const btnMock = document.getElementById('btn-tab-mocktest');
    const btnComb = document.getElementById('btn-tab-combined');
    const paneMock = document.getElementById('pane-mocktest');
    const paneComb = document.getElementById('pane-combined');

    if (tab === 'mocktest') {
        btnMock.classList.add('active');
        btnComb.classList.remove('active');
        paneMock.style.display = 'block';
        paneComb.style.display = 'none';
    } else {
        btnComb.classList.add('active');
        btnMock.classList.remove('active');
        paneComb.style.display = 'block';
        paneMock.style.display = 'none';
    }
}

function filterMockSubjects(courseId) {
    const select = document.getElementById('mock_subject_id');
    const options = select.querySelectorAll('option');
    options.forEach(opt => {
        if (!opt.value) return;
        const optCourseId = opt.getAttribute('data-course-id');
        if (!courseId || optCourseId == courseId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
}
</script>
@endsection
