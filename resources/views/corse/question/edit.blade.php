@extends('layouts.admin')
@section('title', 'Edit Question')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-md-9 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Edit Question</h4>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.questions.update', $question->id) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="form-group mb-3">
                        <label for="course_id">Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-select form-select-lg" required>
                            <option value="">-- Select Course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                    {{ old('course_id', optional($question->subject)->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subject --}}
                    <div class="form-group mb-3">
                        <label for="subject_id">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subject_id"
                                class="form-select form-select-lg @error('subject_id') is-invalid @enderror" required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" data-course-id="{{ $subject->course_id }}"
                                    {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Chapter --}}
                    <div class="form-group mb-3">
                        <label for="chapter_id" class="fw-semibold">Chapter <small class="text-muted">(Optional)</small></label>
                        <select name="chapter_id" id="chapter_id" class="form-select form-select-lg @error('chapter_id') is-invalid @enderror">
                            <option value="">-- Select Chapter --</option>
                            @foreach($chapters as $ch)
                                <option value="{{ $ch->id }}" data-subject-id="{{ $ch->subject_id }}"
                                    {{ old('chapter_id', $question->chapter_id) == $ch->id ? 'selected' : '' }}>
                                    {{ $ch->full_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('chapter_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Question --}}
                    <div class="form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <label for="question" class="fw-semibold mb-0">Question <span class="text-danger">*</span></label>
                            
                            {{-- Math Helper Toolbar --}}
                            <div class="btn-group btn-group-sm" role="group" aria-label="Math Toolbar">
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( F = m \\cdot a \\)')" title="Multiplication (Dot)">· Dot</button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( W = F \\times d \\)')" title="Multiplication (Cross)">× Cross</button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( v = \\frac{d}{t} \\)')" title="Fraction">Fraction \(\frac{d}{t}\)</button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( E = mc^2 \\)')" title="Exponent">Power \(x^2\)</button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( v_f = v_i + at \\)')" title="Subscript">Subscript \(v_f\)</button>
                                <button type="button" class="btn btn-outline-primary" onclick="insertMath('question', '\\( T = 2\\pi \\sqrt{\\frac{L}{g}} \\)')" title="Square Root">√ Root</button>
                            </div>
                        </div>
                        
                        <textarea name="question" id="question" rows="4"
                                  class="form-control @error('question') is-invalid @enderror"
                                  placeholder="Enter the question text. Use \( formula \) for math expressions" required>{{ old('question', $question->question) }}</textarea>
                        
                        {{-- Live Math Preview Container --}}
                        <div id="question-preview-box" class="p-3 mt-2 bg-light border border-info rounded" style="min-height: 50px; display: none;">
                            <span class="badge bg-info text-white mb-2"><i class="icon-eye me-1"></i> Live Formula Preview</span>
                            <div id="question-preview-content" class="fs-5 text-dark"></div>
                        </div>
                        @error('question')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Image --}}
                    <div class="form-group">
                        <label for="image">Question Image <small class="text-muted">(Optional)</small></label>
                        @if($question->image)
                            <div class="mb-2">
                                <img src="{{ $question->image_url }}" alt="Question Image"
                                     style="max-width: 150px; max-height: 150px;" class="img-thumbnail d-block">
                                <small class="text-muted">Current image. Upload a new one to replace it.</small>
                            </div>
                        @endif
                        <input type="file" name="image" id="image"
                               class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Question Type --}}
                    <div class="form-group mb-3">
                        <label for="question_type">Question Type <span class="text-danger">*</span></label>
                        <select name="question_type" id="question_type" class="form-select form-select-lg" required>
                            <option value="mcq"        {{ old('question_type', $question->question_type) == 'mcq'        ? 'selected' : '' }}>MCQ — Multiple Choice (1 correct)</option>
                            <option value="msq"        {{ old('question_type', $question->question_type) == 'msq'        ? 'selected' : '' }}>MSQ — Multiple Select (multiple correct)</option>
                            <option value="descripted" {{ old('question_type', $question->question_type) == 'descripted' ? 'selected' : '' }}>Descriptive — Written Answer</option>
                        </select>
                    </div>

                    {{-- Reason / Explanation --}}
                    <div class="form-group mb-3">
                        <label for="reason" class="fw-semibold">Reason / Explanation <small class="text-muted">(Optional - Reason for the correct answer)</small></label>
                        <textarea name="reason" id="reason" rows="3"
                                  class="form-control @error('reason') is-invalid @enderror"
                                  placeholder="Enter explanation or reason for the correct answer">{{ old('reason', $question->reason) }}</textarea>
                        @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- ══════════════════════════════════════════════════════
                         ANSWERS SECTION
                    ══════════════════════════════════════════════════════ --}}
                    <div class="form-group mt-4">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0 fw-semibold">
                                Answers
                                <small class="text-muted fw-normal">(edit or add options below)</small>
                            </label>
                            <button type="button" id="add-answer-btn"
                                    class="btn btn-success btn-sm px-3">
                                <i class="icon-plus me-1"></i> Add Answer
                            </button>
                        </div>

                        <div id="answers-container">
                            {{-- If validation failed: restore old submitted values --}}
                            @if(old('answers'))
                                @foreach(old('answers') as $i => $oldAnswer)
                                <div class="answer-row card p-2 mb-2 bg-light border">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="answer-label fw-bold text-muted" style="min-width:22px;">{{ chr(65+$i) }}.</span>
                                        {{-- Hidden ID to update existing answer; blank for new ones --}}
                                        <input type="hidden" name="answer_ids[]" value="{{ old('answer_ids.'.$i, '') }}">
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
                                    <div class="ms-4">
                                        <input type="text" name="answer_reasons[]"
                                               class="form-control form-control-sm"
                                               placeholder="Reason / Explanation for Option {{ chr(65+$i) }} (Optional)"
                                               value="{{ old('answer_reasons.'.$i) }}">
                                    </div>
                                </div>
                                @endforeach
                            @else
                                {{-- Load existing answers from DB --}}
                                @forelse($question->answers as $i => $answer)
                                <div class="answer-row card p-2 mb-2 bg-light border">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="answer-label fw-bold text-muted" style="min-width:22px;">{{ chr(65+$i) }}.</span>
                                        <input type="hidden" name="answer_ids[]" value="{{ $answer->id }}">
                                        <input type="text" name="answers[]"
                                               class="form-control"
                                               value="{{ $answer->answer }}" required>
                                        <select name="is_correct[]" class="form-select" style="max-width:140px;">
                                            <option value="0" {{ !$answer->is_correct ? 'selected' : '' }}>❌ Wrong</option>
                                            <option value="1" {{  $answer->is_correct ? 'selected' : '' }}>✅ Correct</option>
                                        </select>
                                        <button type="button" class="btn btn-danger btn-sm remove-answer" title="Remove">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </div>
                                    <div class="ms-4">
                                        <input type="text" name="answer_reasons[]"
                                               class="form-control form-control-sm"
                                               placeholder="Reason / Explanation for Option {{ chr(65+$i) }} (Optional)"
                                               value="{{ $answer->reason }}">
                                    </div>
                                </div>
                                @empty
                                    {{-- No answers yet: show 4 blank rows --}}
                                    @foreach(['A','B','C','D'] as $letter)
                                    <div class="answer-row card p-2 mb-2 bg-light border">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="answer-label fw-bold text-muted" style="min-width:22px;">{{ $letter }}.</span>
                                            <input type="hidden" name="answer_ids[]" value="">
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
                                        <div class="ms-4">
                                            <input type="text" name="answer_reasons[]"
                                                   class="form-control form-control-sm"
                                                   placeholder="Reason / Explanation for Option {{ $letter }} (Optional)">
                                        </div>
                                    </div>
                                    @endforeach
                                @endforelse
                            @endif
                        </div>

                        @error('answers')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>
                    {{-- ══════════════════════════════════════════════════════ --}}

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">Update Question</button>
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

    function reLabel() {
        container.querySelectorAll('.answer-row').forEach(function (row, idx) {
            const letter = String.fromCharCode(65 + idx);
            const lbl = row.querySelector('.answer-label');
            if (lbl) lbl.textContent = letter + '.';
            const reasonInput = row.querySelector('input[name="answer_reasons[]"]');
            if (reasonInput) reasonInput.placeholder = 'Reason / Explanation for Option ' + letter + ' (Optional)';
        });
    }

    function newRow() {
        const idx    = container.querySelectorAll('.answer-row').length;
        const letter = String.fromCharCode(65 + idx);
        const div    = document.createElement('div');
        div.className = 'answer-row card p-2 mb-2 bg-light border';
        div.innerHTML =
            '<div class="d-flex align-items-center gap-2 mb-1">' +
                '<span class="answer-label fw-bold text-muted" style="min-width:22px;">' + letter + '.</span>' +
                '<input type="hidden" name="answer_ids[]" value="">' +
                '<input type="text" name="answers[]" class="form-control" placeholder="Enter answer option ' + letter + '" required>' +
                '<select name="is_correct[]" class="form-select" style="max-width:140px;">' +
                    '<option value="0">❌ Wrong</option>' +
                    '<option value="1">✅ Correct</option>' +
                '</select>' +
                '<button type="button" class="btn btn-danger btn-sm remove-answer" title="Remove">' +
                    '<i class="icon-trash"></i>' +
                '</button>' +
            '</div>' +
            '<div class="ms-4">' +
                '<input type="text" name="answer_reasons[]" class="form-control form-control-sm" placeholder="Reason / Explanation for Option ' + letter + ' (Optional)">' +
            '</div>';
        return div;
    }

    addBtn.addEventListener('click', function () {
        container.appendChild(newRow());
    });

    container.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-answer');
        if (!btn) return;
        if (container.querySelectorAll('.answer-row').length <= 2) {
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
                    const selectedChId = '{{ old("chapter_id", $question->chapter_id) }}';
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

        if (subjectSelect.value && !chapterSelect.value) {
            loadChaptersForSubject(subjectSelect.value);
        }
    }
})();
</script>

{{-- KaTeX JS & Auto Render --}}
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>

<script>
// Helper function to insert Math LaTeX expression at cursor position
function insertMath(elementId, mathCode) {
    const textarea = document.getElementById(elementId);
    if (!textarea) return;
    
    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + mathCode + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + mathCode.length;
    textarea.focus();
    
    // Trigger input event to update preview
    textarea.dispatchEvent(new Event('input'));
}

// Live Math Renderer
function updateMathPreview(textareaId, previewBoxId, previewContentId) {
    const textarea = document.getElementById(textareaId);
    const box = document.getElementById(previewBoxId);
    const content = document.getElementById(previewContentId);
    
    if (!textarea || !box || !content) return;
    
    const val = textarea.value.trim();
    if (!val) {
        box.style.display = 'none';
        content.innerHTML = '';
        return;
    }
    
    box.style.display = 'block';
    content.innerHTML = val.replace(/\n/g, '<br>');
    
    if (window.renderMathInElement) {
        try {
            renderMathInElement(content, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '$', right: '$', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ],
                throwOnError: false
            });
        } catch (e) {
            console.warn("KaTeX render issue:", e);
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const qTextarea = document.getElementById('question');
    if (qTextarea) {
        qTextarea.addEventListener('input', function () {
            updateMathPreview('question', 'question-preview-box', 'question-preview-content');
        });
        // Render initial value if editing existing question
        if (qTextarea.value) {
            setTimeout(() => {
                updateMathPreview('question', 'question-preview-box', 'question-preview-content');
            }, 500);
        }
    }
});
</script>
@endpush
