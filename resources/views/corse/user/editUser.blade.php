@extends('layouts.admin')
@section('title', 'Edit User / Admin')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Edit User / Admin</h4>
                        <p class="text-muted small mb-0">Editing details for: <strong>{{ $user->name }}</strong> (&#64;{{ $user->user_name }})</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="user_name">Username <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" id="user_name"
                               class="form-control form-control-lg @error('user_name') is-invalid @enderror"
                               value="{{ old('user_name', $user->user_name) }}" placeholder="Enter unique username" required>
                        @error('user_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="gmail">Gmail Address <span class="text-danger">*</span></label>
                        <input type="email" name="gmail" id="gmail"
                               class="form-control form-control-lg @error('gmail') is-invalid @enderror"
                               value="{{ old('gmail', $user->gmail) }}" placeholder="Enter unique gmail address" required>
                        @error('gmail')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone_no">Phone Number</label>
                        <input type="text" name="phone_no" id="phone_no"
                               class="form-control form-control-lg @error('phone_no') is-invalid @enderror"
                               value="{{ old('phone_no', $user->phone_no) }}" placeholder="Enter phone number">
                        @error('phone_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select form-select-lg @error('role') is-invalid @enderror" required>
                                    <option value="7" {{ old('role', $user->role) == 7 ? 'selected' : '' }}>Super Admin (Role 7)</option>
                                    <option value="5" {{ old('role', $user->role) == 5 ? 'selected' : '' }}>Admin (Role 5)</option>
                                    <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>User (Role 3)</option>
                                </select>
                                @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border p-3 my-3">
                        <h6 class="fw-bold mb-2">Change Password <small class="text-muted fw-normal">(leave blank to keep current password)</small></h6>
                        <div class="form-group mb-3">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="Enter new password (min. 6 characters)">
                            @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                   class="form-control form-control-lg"
                                   placeholder="Re-enter new password">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">Save Changes</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
