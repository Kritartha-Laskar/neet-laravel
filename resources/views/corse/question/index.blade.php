@extends('layouts.admin')
@section('title', 'Questions')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-dark"><i class="icon-question me-2 text-primary"></i>All Questions</h4>
                        <p class="text-muted small mb-0">Manage question bank organized by Course, Subject, and Chapter.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm fw-semibold">
                            <i class="icon-plus me-1"></i> Add Question
                        </a>
                        <span class="badge badge-primary p-2">{{ $questions->total() }} Total Questions</span>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="card mb-4 border bg-light">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <label class="small fw-semibold text-muted">Course:</label>
                                <select name="course_id" id="filter_course_id" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $crs)
                                        <option value="{{ $crs->id }}" {{ request('course_id') == $crs->id ? 'selected' : '' }}>
                                            {{ $crs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-semibold text-muted">Subject:</label>
                                <select name="subject_id" id="filter_subject_id" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">-- All Subjects --</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-semibold text-muted">Chapter:</label>
                                <select name="chapter_id" id="filter_chapter_id" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">-- All Chapters --</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-1" style="margin-top: 22px;">
                                <button type="submit" class="btn btn-sm btn-dark w-100"><i class="icon-filter me-1"></i> Filter</button>
                                @if(request('course_id') || request('subject_id') || request('chapter_id'))
                                    <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Subject &amp; Chapter</th>
                                <th>Question</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Options</th>
                                <th>Created At</th>
                                <th class="text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($question->subject)
                                        <span class="badge bg-info text-white me-1"><i class="icon-layers me-1"></i>{{ $question->subject->name }}</span>
                                    @endif
                                    @if($question->chapter)
                                        <span class="badge bg-purple text-white mt-1 d-inline-block" style="background-color: #6c5ce7;">
                                            <i class="icon-notebook me-1"></i>{{ $question->chapter->full_title }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border mt-1 d-inline-block">General</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ Str::limit($question->question, 75) }}</div>
                                    @if($question->image)
                                        <div class="mt-1">
                                            <a href="{{ $question->image_url }}" target="_blank" title="View full image">
                                                <img src="{{ $question->image_url }}" alt="Question Image"
                                                     style="max-width: 60px; max-height: 40px; object-fit: cover;" class="img-thumbnail">
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $question->question_type === 'mcq' ? 'primary' : ($question->question_type === 'msq' ? 'warning text-dark' : 'secondary') }} px-2 py-1">
                                        {{ strtoupper($question->question_type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $question->answers->count() }}</span>
                                </td>
                                <td><small class="text-muted">{{ $question->created_at->format('d M Y') }}</small></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-outline-warning py-1 px-2" title="Edit Question">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this question?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Question"><i class="icon-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No questions found matching the selected filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 d-flex justify-content-center justify-content-md-end">
                    {{ $questions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const subSelect = document.getElementById('filter_subject_id');
    const chSelect = document.getElementById('filter_chapter_id');
    const activeChId = '{{ request("chapter_id") }}';

    function loadChapters(subId) {
        if (!chSelect) return;
        chSelect.innerHTML = '<option value="">-- All Chapters --</option>';
        if (!subId) return;

        fetch('{{ url("admin/chapters/by-subject") }}/' + subId)
            .then(res => res.json())
            .then(data => {
                data.forEach(ch => {
                    const opt = document.createElement('option');
                    opt.value = ch.id;
                    opt.textContent = ch.title;
                    if (ch.id == activeChId) opt.selected = true;
                    chSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Error fetching chapters:', err));
    }

    if (subSelect) {
        subSelect.addEventListener('change', function () {
            loadChapters(this.value);
        });
        if (subSelect.value) {
            loadChapters(subSelect.value);
        }
    }
});
</script>
@endsection
