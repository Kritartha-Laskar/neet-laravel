@extends('layouts.admin')
@section('title', $questionPaper->title)

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body p-0">

                {{-- ── Paper Header ─────────────────────────────────── --}}
                <div style="background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
                            border-radius: 8px 8px 0 0; padding: 28px 32px;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h3 class="text-white fw-bold mb-1">{{ $questionPaper->title }}</h3>
                            <p class="text-white opacity-75 mb-0 small">{{ $questionPaper->description }}</p>
                        </div>
                        <a href="{{ route('admin.question-papers.index') }}"
                           class="btn btn-light btn-sm align-self-start">
                            <i class="icon-arrow-left me-1"></i> Back
                        </a>
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
                                <div class="fw-bold fs-4 text-white">{{ $questionPaper->exam_year }}</div>
                                <small class="text-white opacity-75">Exam Year</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Marking Scheme ───────────────────────────────── --}}
                <div class="px-4 py-3 border-bottom bg-light d-flex flex-wrap gap-3 align-items-center">
                    <span class="fw-semibold text-muted">Marking Scheme:</span>
                    <span class="badge badge-success p-2">+4 for Correct</span>
                    <span class="badge badge-danger p-2">−1 for Incorrect</span>
                    <span class="badge badge-secondary p-2">0 for Unattempted</span>
                </div>

                {{-- ── Questions grouped by Subject ─────────────────── --}}
                <div class="p-4">

                    @php $globalNo = 1; @endphp

                    @foreach($grouped as $subjectName => $questions)

                    {{-- Subject section header --}}
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
                                    <th>Question</th>
                                    <th style="width:120px;">Type</th>
                                    <th style="width:80px;">Marks</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $globalNo++ }}</td>
                                    <td>{{ $question->question }}</td>
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
                                            <span class="text-muted small">No options yet</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                </div>{{-- /p-4 --}}

            </div>
        </div>
    </div>
</div>
@endsection
