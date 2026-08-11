@extends('layouts.admin')
@section('title', 'Courses')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">All Courses</h4>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
                        <i class="icon-plus me-1"></i> Add Course
                    </a>
                </div>

                <div class="table-responsive border rounded p-1">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $course->status === 'active' ? 'success' : 'danger' }} p-2">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td>{{ $course->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this course?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="icon-trash"></i> Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">No courses found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $courses->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
