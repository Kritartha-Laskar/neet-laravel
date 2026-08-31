@extends('layouts.admin')
@section('title', 'Chapters')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                
                {{-- Alert Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-dark"><i class="icon-notebook me-2 text-primary"></i>Chapters Management</h4>
                        <p class="text-muted small mb-0">Manage chapters per subject for organizing question papers and questions.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.chapters.create') }}" class="btn btn-primary btn-sm fw-semibold">
                            <i class="icon-plus me-1"></i> Add New Chapter
                        </a>
                        <span class="badge badge-primary p-2">{{ $chapters->total() }} Total Chapters</span>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="card mb-4 border bg-light">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('admin.chapters.index') }}" class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="small fw-semibold text-muted">Filter by Subject:</label>
                                <select name="subject_id" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">-- All Subjects --</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-semibold text-muted">Filter by Course:</label>
                                <select name="course_id" class="form-select form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $crs)
                                        <option value="{{ $crs->id }}" {{ request('course_id') == $crs->id ? 'selected' : '' }}>
                                            {{ $crs->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2" style="margin-top: 22px;">
                                <button type="submit" class="btn btn-sm btn-dark px-3"><i class="icon-filter me-1"></i> Filter</button>
                                @if(request('subject_id') || request('course_id'))
                                    <a href="{{ route('admin.chapters.index') }}" class="btn btn-sm btn-outline-secondary px-3">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                @if($chapters->isEmpty())
                    <div class="text-center py-5 text-muted border rounded bg-light">
                        <i class="icon-notebook d-block mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5>No Chapters Found</h5>
                        <p class="mb-3">Click the "Add New Chapter" button to create your first chapter for a subject.</p>
                        <a href="{{ route('admin.chapters.create') }}" class="btn btn-primary btn-sm">
                            <i class="icon-plus me-1"></i> Add Chapter Now
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Order</th>
                                    <th>Chapter Name / Title</th>
                                    <th>Subject</th>
                                    <th>Course</th>
                                    <th class="text-center">Questions</th>
                                    <th class="text-center">Mock Papers</th>
                                    <th class="text-end" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chapters as $chapter)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary text-dark">{{ $chapter->chapter_number ?? ($chapter->sort_order ?: '-') }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $chapter->full_title }}</div>
                                            @if($chapter->description)
                                                <small class="text-muted d-block text-truncate" style="max-width: 300px;">{{ $chapter->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($chapter->subject)
                                                <span class="badge bg-info text-white"><i class="icon-layers me-1"></i> {{ $chapter->subject->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($chapter->course)
                                                <span class="badge bg-light text-dark border"><i class="icon-book-open me-1"></i> {{ $chapter->course->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary text-white">{{ $chapter->questions_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success text-white">{{ $chapter->question_papers_count }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.chapters.edit', $chapter->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Edit Chapter">
                                                    <i class="icon-note"></i>
                                                </a>
                                                <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Chapter?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Chapter">
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

                    <div class="mt-4">{{ $chapters->links() }}</div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
