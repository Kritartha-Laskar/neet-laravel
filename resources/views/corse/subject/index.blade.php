@extends('layouts.admin')
@section('title', 'Subjects')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">All Subjects</h4>
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                        <i class="icon-plus me-1"></i> Add Subject
                    </a>
                </div>

                <div class="table-responsive border rounded p-1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Subject Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge badge-info p-2">{{ $subject->course->name ?? '-' }}</span></td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this subject?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="icon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No subjects found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $subjects->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
