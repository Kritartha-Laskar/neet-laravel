@extends('layouts.admin')
@section('title', $questionPaper->title)

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">

                {{-- Alert Banners --}}
                <div class="p-3 pb-0">
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
                </div>

                {{-- ── Paper Header ─────────────────────────────────── --}}
                <div style="background: {{ $questionPaper->paper_type === 'mocktest' ? 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)' : 'linear-gradient(135deg,#667eea 0%,#764ba2 100%)' }};
                            border-radius: 8px 8px 0 0; padding: 28px 32px;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-dark text-white fw-bold" style="font-size: 11px;">
                                    @if($questionPaper->paper_type === 'mocktest')
                                        <i class="icon-layers me-1"></i> MOCK TEST PAPER ({{ optional($questionPaper->subject)->name ?? 'Subject-Wise' }})
                                    @else
                                        <i class="icon-grid me-1"></i> COMBINED QUESTION PAPER
                                    @endif
                                </span>
                                @if($questionPaper->course)
                                    <span class="badge bg-light text-dark fw-bold" style="font-size: 11px;">
                                        <i class="icon-book-open me-1"></i> {{ $questionPaper->course->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-white fw-bold mb-1">{{ $questionPaper->title }}</h3>
                            <p class="text-white opacity-75 mb-0 small">{{ $questionPaper->description }}</p>
                        </div>
                        <div class="d-flex gap-2 align-self-start flex-wrap">
                            @if(false)
                            <button type="button" class="btn btn-warning btn-sm fw-semibold" data-toggle="modal" data-target="#addQuestionsModal" data-bs-toggle="modal" data-bs-target="#addQuestionsModal">
                                <i class="icon-plus me-1"></i> Select From Question Bank
                            </button>
                            @endif
                            <a href="{{ route('admin.questions.create', ['course_id' => $questionPaper->course_id, 'subject_id' => $questionPaper->subject_id, 'question_paper_id' => $questionPaper->id]) }}" class="btn btn-success btn-sm fw-semibold">
                                <i class="icon-plus me-1"></i> Create &amp; Attach Question
                            </a>
                            <a href="{{ route('admin.question-papers.index') }}" class="btn btn-light btn-sm">
                                <i class="icon-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div class="row text-center mt-4 g-2">
                        <div class="col-6 col-md-3">
                            <div class="bg-white bg-opacity-25 rounded p-3" style="background:rgba(255,255,255,.15)!important">
                                <div class="fw-bold fs-4 text-white">{{ $questionPaper->total_questions }}</div>
                                <small class="text-white opacity-75">Total Questions</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white bg-opacity-25 rounded p-3" style="background:rgba(255,255,255,.15)!important">
                                <div class="fw-bold fs-4 text-white">{{ $questionPaper->total_marks }}</div>
                                <small class="text-white opacity-75">Total Marks</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white bg-opacity-25 rounded p-3" style="background:rgba(255,255,255,.15)!important">
                                <div class="fw-bold fs-4 text-white">{{ $questionPaper->duration_minutes }} min</div>
                                <small class="text-white opacity-75">Duration</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-white bg-opacity-25 rounded p-3" style="background:rgba(255,255,255,.15)!important">
                                <div class="fw-bold fs-4 text-white">{{ $questionPaper->exam_year ?? date('Y') }}</div>
                                <small class="text-white opacity-75">Exam Year</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Marking Scheme & Quick Action Bar ─────────────── --}}
                <div class="px-4 py-3 border-bottom bg-light d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="fw-semibold text-muted small">Marking Scheme:</span>
                        <span class="badge badge-success p-2">+4 for Correct</span>
                        <span class="badge badge-danger p-2">−1 for Incorrect</span>
                        <span class="badge badge-secondary p-2">0 for Unattempted</span>
                    </div>
                    <div class="d-flex gap-2">
                        @if(false)
                        <button type="button" class="btn btn-primary btn-sm fw-semibold" data-toggle="modal" data-target="#addQuestionsModal" data-bs-toggle="modal" data-bs-target="#addQuestionsModal">
                            <i class="icon-plus me-1"></i> Select From Bank
                        </button>
                        @endif
                        <a href="{{ route('admin.questions.create', ['course_id' => $questionPaper->course_id, 'subject_id' => $questionPaper->subject_id, 'question_paper_id' => $questionPaper->id]) }}" class="btn btn-success btn-sm fw-semibold">
                            <i class="icon-plus me-1"></i> Create New Question
                        </a>
                    </div>
                </div>

                {{-- ── Questions Grouped by Subject ─────────────────── --}}
                <div class="p-4">

                    @if($questionPaper->questions->isEmpty())
                        <div class="text-center py-5 text-muted border rounded bg-light">
                            <i class="icon-question d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h5>No Questions Added to This Paper Yet</h5>
                            <p class="mb-3">Click below to create and add new questions into this question paper.</p>
                            <a href="{{ route('admin.questions.create', ['course_id' => $questionPaper->course_id, 'subject_id' => $questionPaper->subject_id, 'question_paper_id' => $questionPaper->id]) }}" class="btn btn-success btn-sm fw-semibold">
                                <i class="icon-plus me-1"></i> Create &amp; Attach Question
                            </a>
                        </div>
                    @else

                    @php $globalNo = 1; @endphp

                    @foreach($grouped as $subjectName => $questions)

                    {{-- Subject Section Header --}}
                    <div class="d-flex align-items-center mb-3 mt-{{ $loop->first ? '0' : '5' }}">
                        <div style="width:4px; height:32px; border-radius:4px;
                                    background: {{ match($subjectName) {
                                        'Biology'   => '#28a745',
                                        'Physics'   => '#007bff',
                                        'Chemistry' => '#ffc107',
                                        default     => '#6c757d'
                                    } }};" class="me-3"></div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $subjectName }}</h5>
                            <small class="text-muted">{{ $questions->count() }} Questions
                                · {{ $questions->count() * 4 }} Marks</small>
                        </div>
                        <span class="badge ms-auto p-2
                            {{ match($subjectName) {
                                'Biology'   => 'badge-success',
                                'Physics'   => 'badge-primary',
                                'Chemistry' => 'badge-warning',
                                default     => 'badge-secondary'
                            } }}">
                            Section
                        </span>
                    </div>

                    <div class="table-responsive border rounded mb-2">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Question Text / Image</th>
                                    <th style="width:100px;">Type</th>
                                    <th style="width:80px;">Marks</th>
                                    <th>Answer Options</th>
                                    <th style="width:100px;" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $globalNo++ }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $question->question }}</div>
                                        @if($question->image_url)
                                            <a href="{{ $question->image_url }}" target="_blank" class="badge badge-info mt-1 text-decoration-none">
                                                <i class="icon-picture me-1"></i> View Attached Image
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $question->question_type === 'mcq' ? 'primary' : ($question->question_type === 'msq' ? 'warning' : 'info') }} p-1">
                                            {{ strtoupper($question->question_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">+{{ $question->pivot->marks }}</span>
                                        <span class="text-danger small"> / −1</span>
                                    </td>
                                    <td>
                                        @if($question->answers->isNotEmpty())
                                            <div class="row g-1">
                                                @foreach($question->answers as $idx => $answer)
                                                <div class="col-6">
                                                    <div class="d-flex align-items-start gap-1 small
                                                        {{ $answer->is_correct ? 'text-success fw-semibold' : 'text-muted' }}">
                                                        <span class="fw-bold">{{ chr(65 + $idx) }}.</span>
                                                        <span>{{ $answer->answer }}
                                                            @if($answer->is_correct)
                                                                <i class="icon-check text-success" title="Correct Answer"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">No options configured</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.questions.edit', $question->id) }}" 
                                               class="btn btn-outline-primary btn-xs" 
                                               title="Edit Question">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.question-papers.remove-question', [$questionPaper->id, $question->id]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Remove this question from this paper?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Remove question from paper">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                    @endif

                </div>{{-- /p-4 --}}

            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: ADD QUESTIONS TO QUESTION PAPER
══════════════════════════════════════════════════════ -->
<div class="modal fade" id="addQuestionsModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.question-papers.add-questions', $questionPaper->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addQuestionsModalLabel">
                        <i class="icon-plus text-primary me-2"></i>Add Questions to Paper
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    @if($availableQuestions->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="icon-check d-block mb-2" style="font-size: 2.5rem; color: #28a745;"></i>
                            <h6>All available questions are already added or no questions exist!</h6>
                            <p class="small text-muted mb-3">You can create new questions in the Question Bank and then assign them here.</p>
                            <a href="{{ route('admin.questions.create') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="icon-plus me-1"></i> Create New Question
                            </a>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Select questions below to add to <strong>{{ $questionPaper->title }}</strong>:</span>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAllQuestions" onclick="toggleSelectAll(this)">
                                <label class="form-check-label small fw-bold" for="selectAllQuestions">Select All</label>
                            </div>
                        </div>

                        <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 40px;">Select</th>
                                        <th>Question</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableQuestions as $availQ)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="question_ids[]" value="{{ $availQ->id }}" class="form-check-input q-checkbox">
                                        </td>
                                        <td>
                                            <div class="fw-semibold small text-dark">{{ $availQ->question }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-dark">{{ optional($availQ->subject)->name ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white small">{{ strtoupper($availQ->question_type) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    @if($availableQuestions->isNotEmpty())
                        <button type="submit" class="btn btn-primary fw-semibold"><i class="icon-plus me-1"></i> Add Selected Questions</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.q-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>
@endsection
