@extends('layouts.admin')
@section('title', 'Answers')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">All Answers</h4>
                    <a href="{{ route('admin.answers.create') }}" class="btn btn-primary btn-sm">
                        <i class="icon-plus me-1"></i> Add Answer
                    </a>
                </div>

                <div class="table-responsive border rounded p-1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Correct?</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($answers as $answer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::limit($answer->question->question ?? '-', 50) }}</td>
                                <td>{{ Str::limit($answer->answer, 60) }}</td>
                                <td>
                                    @if($answer->is_correct)
                                        <span class="badge badge-success p-2">✔ Correct</span>
                                    @else
                                        <span class="badge badge-danger p-2">✘ Wrong</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.answers.edit', $answer->id) }}" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.answers.destroy', $answer->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this answer?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="icon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No answers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $answers->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
