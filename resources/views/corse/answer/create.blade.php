@extends('layouts.admin')
@section('title', 'Add Answer')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Add New Answer</h4>
                    <a href="{{ route('admin.answers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.answers.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="question_id">Question <span class="text-danger">*</span></label>
                        <select name="question_id" id="question_id" class="form-select form-select-lg @error('question_id') is-invalid @enderror" required>
                            <option value="">-- Select Question --</option>
                            @foreach($questions as $question)
                                <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>
                                    {{ Str::limit($question->question, 80) }}
                                </option>
                            @endforeach
                        </select>
                        @error('question_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="answer">Answer <span class="text-danger">*</span></label>
                        <textarea name="answer" id="answer" rows="3"
                                  class="form-control @error('answer') is-invalid @enderror"
                                  placeholder="Enter the answer text" required>{{ old('answer') }}</textarea>
                        @error('answer')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="is_correct">Is Correct? <span class="text-danger">*</span></label>
                        <select name="is_correct" id="is_correct" class="form-select form-select-lg @error('is_correct') is-invalid @enderror" required>
                            <option value="0" {{ old('is_correct') == '0' ? 'selected' : '' }}>No — Wrong Answer</option>
                            <option value="1" {{ old('is_correct') == '1' ? 'selected' : '' }}>Yes — Correct Answer</option>
                        </select>
                        @error('is_correct')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">Save Answer</button>
                        <a href="{{ route('admin.answers.index') }}" class="btn btn-light btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
