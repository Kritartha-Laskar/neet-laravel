@extends('layouts.admin')
@section('title', 'Questions')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">All Questions</h4>
                    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                        <i class="icon-plus me-1"></i> Add Question
                    </a>
                </div>

                <div class="table-responsive border rounded p-1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Answers</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($question->subject)
                                        <span class="badge badge-info p-2">{{ $question->subject->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ Str::limit($question->question, 60) }}</div>
                                    @if($question->image)
                                        <div class="mt-1">
                                            <a href="{{ $question->image_url }}" target="_blank" title="View full image">
                                                <img src="{{ $question->image_url }}" alt="Question Image"
                                                     style="max-width: 60px; max-height: 40px; object-fit: cover;" class="img-thumbnail">
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $question->question_type === 'mcq' ? 'primary' : ($question->question_type === 'msq' ? 'warning' : 'info') }} p-2">
                                        {{ strtoupper($question->question_type) }}
                                    </span>
                                </td>
                                <td>{{ $question->answers->count() }}</td>
                                <td>{{ $question->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this question?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="icon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">No questions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $questions->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
