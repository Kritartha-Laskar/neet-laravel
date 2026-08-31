@extends('layouts.admin')
@section('title', 'Add Question')

@section('content')
<div class="row">
    <div class="col-md-9 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Add New Question</h4>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(request('question_paper_id'))
                        <input type="hidden" name="question_paper_id" value="{{ request('question_paper_id') }}">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="icon-info me-1"></i> Creating question to attach directly to Question Paper <strong>{{ $paper ? $paper->title : ('#' . request('question_paper_id')) }}</strong>.
                        </div>
                    @endif

                    {{-- Course Field (Fixed if set, otherwise Dropdown) --}}
                    <div class="form-group mb-3">
                        <label for="course_id" class="fw-semibold">Course <span class="text-danger">*</span></label>
                        @if($selectedCourse)
                            <input type="text" class="form-control form-control-lg bg-light text-dark fw-bold" value="{{ $selectedCourse->name }}" readonly disabled>
                            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                            <small class="text-muted"><i class="icon-lock me-1"></i> Course is fixed based on selected question paper.</small>
                        @else
                            <select name="course_id" id="course_id" class="form-select form-select-lg" required>
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (old('course_id', request('course_id')) == $course->id) ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Subject Field (Fixed if set, otherwise Dropdown) --}}
                    <div class="form-group mb-3">
                        <label for="subject_id" class="fw-semibold">Subject <span class="text-danger">*</span></label>
                        @if($selectedSubject)
                            <input type="text" class="form-control form-control-lg bg-light text-success fw-bold" value="{{ $selectedSubject->name }}" readonly disabled>
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                            <small class="text-muted"><i class="icon-lock me-1"></i> Subject is fixed for this Mock Test question paper.</small>
                        @else
                            <select name="subject_id" id="subject_id"
                                    class="form-select form-select-lg @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    @if(!$selectedCourse || $subject->course_id == $selectedCourse->id)
                                        <option value="{{ $subject->id }}" data-course-id="{{ $subject->course_id }}" {{ (old('subject_id', request('subject_id')) == $subject->id) ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('subject_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        @endif
                    </div>

                    {{-- Chapter Field (Fixed if set, otherwise Dropdown) --}}
                    <div class="form-group mb-3">
                        <label for="chapter_id" class="fw-semibold">Chapter <small class="text-muted">(Optional)</small></label>
                        @if($selectedChapter)
                            <input type="text" class="form-control form-control-lg bg-light text-primary fw-bold" value="{{ $selectedChapter->full_title }}" readonly disabled>
                            <input type="hidden" name="chapter_id" value="{{ $selectedChapter->id }}">
                            <small class="text-muted"><i class="icon-lock me-1"></i> Chapter is fixed for this Mock Test paper.</small>
                        @else
                            <select name="chapter_id" id="chapter_id" class="form-select form-select-lg @error('chapter_id') is-invalid @enderror">
                                <option value="">-- Select Chapter --</option>
                                @foreach($chapters as $ch)
                                    <option value="{{ $ch->id }}" data-subject-id="{{ $ch->subject_id }}" {{ (old('chapter_id', request('chapter_id')) == $ch->id) ? 'selected' : '' }}>
                                        {{ $ch->full_title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chapter_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        @endif
                    </div>

                    {{-- Question --}}
                    <div class="form-group">
                        <label for="question">Question <span class="text-danger">*</span></label>
                        <textarea name="question" id="question" rows="4"
                                  class="form-control @error('question') is-invalid @enderror"
                                  placeholder="Enter the question text" required>{{ old('question') }}</textarea>
                        @error('question')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Image --}}
                    <div class="form-group">
                        <label for="image">Question Image <small class="text-muted">(Optional)</small></label>
                        <input type="file" name="image" id="image"
                               class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Question Type --}}
                    <div class="form-group">
                        <label for="question_type">Question Type <span class="text-danger">*</span></label>
                        <select name="question_type" id="question_type"
                                class="form-select form-select-lg @error('question_type') is-invalid @enderror" required>
                            <option value="">-- Select Type --</option>
                            <option value="mcq"        {{ old('question_type') == 'mcq'        ? 'selected' : '' }}>MCQ — Multiple Choice (1 correct)</option>
                            <option value="msq"        {{ old('question_type') == 'msq'        ? 'selected' : '' }}>MSQ — Multiple Select (multiple correct)</option>
                            <option value="descripted" {{ old('question_type') == 'descripted' ? 'selected' : '' }}>Descriptive — Written Answer</option>
                        </select>
                        @error('question_type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- ══════════════════════════════════════════════════════
                         ANSWERS SECTION
                    ══════════════════════════════════════════════════════ --}}
                    <div class="form-group mt-4">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0 fw-semibold">
                                Answers
                                <small class="text-muted fw-normal">(add options below)</small>
                            </label>
                            <button type="button" id="add-answer-btn"
                                    class="btn btn-success btn-sm px-3">
                                <i class="icon-plus me-1"></i> Add Answer
                            </button>
                        </div>

                        {{-- Answer rows container --}}
                        <div id="answers-container">
                            {{-- Restore old values on validation failure --}}
                            @if(old('answers'))
                                @foreach(old('answers') as $i => $oldAnswer)
                                <div class="answer-row d-flex align-items-center gap-2 mb-2">
                                    <span class="answer-label fw-bold text-muted" style="min-width:22px;">{{ chr(65+$i) }}.</span>
                                    <input type="text" name="answers[]"
                                           class="form-control"
                                           placeholder="Enter answer option"
                                           value="{{ $oldAnswer }}" required>
                                    <select name="is_correct[]" class="form-select" style="max-width:140px;">
                                        <option value="0" {{ (old('is_correct.'.$i, 0) == 0) ? 'selected' : '' }}>❌ Wrong</option>
                                        <option value="1" {{ (old('is_correct.'.$i, 0) == 1) ? 'selected' : '' }}>✅ Correct</option>
                                    </select>
                                    <button type="button" class="btn btn-danger btn-sm remove-answer" title="Remove">
                                        <i class="icon-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            @else
                                {{-- Default: 4 blank rows --}}
                                @foreach(['A','B','C','D'] as $letter)
                                <div class="answer-row d-flex align-items-center gap-2 mb-2">
                                    <span class="answer-label fw-bold text-muted" style="min-width:22px;">{{ $letter }}.</span>
                                    <input type="text" name="answers[]"
                                           class="form-control"
                                           placeholder="Enter answer option {{ $letter }}" required>
                                    <select name="is_correct[]" class="form-select" style="max-width:140px;">
                                        <option value="0">❌ Wrong</option>
                                        <option value="1">✅ Correct</option>
                                    </select>
                                    <button type="button" class="btn btn-danger btn-sm remove-answer" title="Remove">
                                        <i class="icon-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        @error('answers')<span class="text-danger small">{{ $message }}</span>@enderror
                        @error('answers.*')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    {{-- ══════════════════════════════════════════════════════ --}}

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">Save Question</button>
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-light btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const container = document.getElementById('answers-container');
    const addBtn    = document.getElementById('add-answer-btn');

    /** Re-label every row with A, B, C … */
    function reLabel() {
        container.querySelectorAll('.answer-row').forEach(function (row, idx) {
            const lbl = row.querySelector('.answer-label');
            if (lbl) lbl.textContent = String.fromCharCode(65 + idx) + '.';
        });
    }

    /** Build a new blank answer row */
    function newRow() {
        const idx  = container.querySelectorAll('.answer-row').length;
        const letter = String.fromCharCode(65 + idx);
        const div  = document.createElement('div');
        div.className = 'answer-row d-flex align-items-center gap-2 mb-2';
        div.innerHTML =
            '<span class="answer-label fw-bold text-muted" style="min-width:22px;">' + letter + '.</span>' +
            '<input type="text" name="answers[]" class="form-control" placeholder="Enter answer option ' + letter + '" required>' +
            '<select name="is_correct[]" class="form-select" style="max-width:140px;">' +
                '<option value="0">❌ Wrong</option>' +
                '<option value="1">✅ Correct</option>' +
            '</select>' +
            '<button type="button" class="btn btn-danger btn-sm remove-answer" title="Remove">' +
                '<i class="icon-trash"></i>' +
            '</button>';
        return div;
    }

    // Add answer button
    addBtn.addEventListener('click', function () {
        container.appendChild(newRow());
    });

    // Remove answer button (event delegation)
    container.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-answer');
        if (!btn) return;
        const rows = container.querySelectorAll('.answer-row');
        if (rows.length <= 2) {
            alert('A question must have at least 2 answer options.');
            return;
        }
        btn.closest('.answer-row').remove();
        reLabel();
    });

    // Course -> Subject filtering logic
    const courseSelect = document.getElementById('course_id');
    const subjectSelect = document.getElementById('subject_id');

    if (courseSelect && subjectSelect) {
        const allSubjectOptions = Array.from(subjectSelect.options);
        
        courseSelect.addEventListener('change', function () {
            const selectedCourseId = this.value;
            
            // Clear current options
            subjectSelect.innerHTML = '';
            
            // Always add the default option
            subjectSelect.appendChild(allSubjectOptions[0]);
            
            // Filter and append options
            allSubjectOptions.slice(1).forEach(opt => {
                const optCourseId = opt.getAttribute('data-course-id');
                if (!selectedCourseId || optCourseId === selectedCourseId) {
                    subjectSelect.appendChild(opt.cloneNode(true));
                }
            });
            
            subjectSelect.value = '';
            if (chapterSelect) {
                chapterSelect.innerHTML = '<option value="">-- Select Chapter --</option>';
            }
        });

        // Trigger change initially to filter if a course was pre-selected (old values)
        if (courseSelect.value) {
            const tempVal = subjectSelect.value;
            courseSelect.dispatchEvent(new Event('change'));
            subjectSelect.value = tempVal;
        }
    }

    // Subject -> Chapter cascading logic
    const chapterSelect = document.getElementById('chapter_id');
    if (subjectSelect && chapterSelect) {
        function loadChaptersForSubject(subId) {
            if (!subId) {
                chapterSelect.innerHTML = '<option value="">-- Select Chapter --</option>';
                return;
            }
            fetch('{{ url("admin/chapters/by-subject") }}/' + subId)
                .then(res => res.json())
                .then(data => {
                    const selectedChId = '{{ old("chapter_id", request("chapter_id")) }}';
                    chapterSelect.innerHTML = '<option value="">-- Select Chapter --</option>';
                    data.forEach(ch => {
                        const opt = document.createElement('option');
                        opt.value = ch.id;
                        opt.textContent = ch.title;
                        if (ch.id == selectedChId) opt.selected = true;
                        chapterSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching chapters:', err));
        }

        subjectSelect.addEventListener('change', function () {
            loadChaptersForSubject(this.value);
        });

        if (subjectSelect.value) {
            loadChaptersForSubject(subjectSelect.value);
        }
    }
})();
</script>
@endpush
