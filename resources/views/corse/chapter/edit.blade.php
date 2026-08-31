@extends('layouts.admin')
@section('title', 'Edit Chapter')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0 fw-bold text-dark"><i class="icon-note me-2 text-primary"></i>Edit Chapter</h4>
                    <a href="{{ route('admin.chapters.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="icon-arrow-left me-1"></i> Back to Chapters
                    </a>
                </div>

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

                <form method="POST" action="{{ route('admin.chapters.update', $chapter->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label fw-semibold">Select Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select form-control" required>
                                <option value="">-- Choose Subject --</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ old('subject_id', $chapter->subject_id) == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="course_id" class="form-label fw-semibold">Select Course <small class="text-muted">(Optional)</small></label>
                            <select name="course_id" id="course_id" class="form-select form-control">
                                <option value="">-- Auto from Subject / All --</option>
                                @foreach($courses as $crs)
                                    <option value="{{ $crs->id }}" {{ old('course_id', $chapter->course_id) == $crs->id ? 'selected' : '' }}>
                                        {{ $crs->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="chapter_number" class="form-label fw-semibold">Chapter Number / Tag</label>
                            <input type="text" name="chapter_number" id="chapter_number" class="form-control" 
                                   placeholder="e.g. 1, 2, or Chapter 1" value="{{ old('chapter_number', $chapter->chapter_number) }}">
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Chapter Name / Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   placeholder="e.g. Cell: The Unit of Life" value="{{ old('name', $chapter->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description <small class="text-muted">(Optional)</small></label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Brief details about topics covered in this chapter...">{{ old('description', $chapter->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $chapter->sort_order) }}" min="0" style="max-width: 150px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.chapters.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-semibold"><i class="icon-check me-1"></i> Update Chapter</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
