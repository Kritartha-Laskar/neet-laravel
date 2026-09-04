@extends('layouts.admin')
@section('title', 'All Users')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1 fw-bold text-dark"><i class="icon-people me-2 text-primary"></i>All Users / Admins</h4>
                        <p class="text-muted mb-0 small">Displaying users with Role 5 (Admin) and Role 7 (Super Admin).</p>
                    </div>
                    @if(Auth::check() && (int)Auth::user()->role === 7)
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="icon-user-follow me-1"></i> Create User
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Search & Filter --}}
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, username, email, phone..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit"><i class="icon-magnifier"></i> Search</button>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Roles (5 &amp; 7)</option>
                            <option value="7" {{ request('role') == '7' ? 'selected' : '' }}>Super Admin (Role 7)</option>
                            <option value="5" {{ request('role') == '5' ? 'selected' : '' }}>Admin (Role 5)</option>
                        </select>
                    </div>
                    @if(request()->hasAny(['search', 'role']))
                        <div class="col-auto">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive border rounded p-1">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Gmail Address</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle me-2 border" style="width: 34px; height: 34px; font-weight: bold;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <small class="text-muted">&#64;{{ $user->user_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->gmail }}" class="text-decoration-none">{{ $user->gmail }}</a>
                                </td>
                                <td>{{ $user->phone_no ?? '—' }}</td>
                                <td>
                                    @if((int)$user->role === 7)
                                        <span class="badge bg-purple text-white p-2">Super Admin (7)</span>
                                    @elseif((int)$user->role === 5)
                                        <span class="badge bg-primary text-white p-2">Admin (5)</span>
                                    @else
                                        <span class="badge bg-secondary text-white p-2">User ({{ $user->role }})</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }} p-2">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm py-1 px-2 me-1" title="Edit User">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete user {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm py-1 px-2" title="Delete User">
                                                <i class="icon-trash"></i> Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-muted border">Current User</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No users found with Role 5 or 7.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
