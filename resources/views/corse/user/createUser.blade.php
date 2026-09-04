@extends('layouts.admin')
@section('title', 'Create Admin/User')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Create New User / Admin</h4>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="icon-arrow-left me-1"></i> Back
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">
                    @csrf

                    {{-- Hidden fields to prevent browser password managers from auto-filling credentials --}}
                    <input type="text" style="display:none" autocomplete="username">
                    <input type="password" style="display:none" autocomplete="new-password">

                    <div class="form-group mb-3">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" autocomplete="off"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Enter full name" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="user_name">Username <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" id="user_name" autocomplete="off"
                               class="form-control form-control-lg @error('user_name') is-invalid @enderror"
                               value="{{ old('user_name') }}" placeholder="Enter unique username" required>
                        @error('user_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="gmail">Gmail Address <span class="text-danger">*</span></label>
                        <input type="email" name="gmail" id="gmail" autocomplete="off"
                               class="form-control form-control-lg @error('gmail') is-invalid @enderror"
                               value="{{ old('gmail') }}" placeholder="Enter unique gmail address" required>
                        @error('gmail')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone_no">Phone Number <small class="text-muted">(optional)</small></label>
                        <input type="text" name="phone_no" id="phone_no" autocomplete="off"
                               class="form-control form-control-lg @error('phone_no') is-invalid @enderror"
                               value="{{ old('phone_no') }}" placeholder="Enter unique phone number">
                        @error('phone_no')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select form-select-lg @error('role') is-invalid @enderror" required>
                            <option value="3" {{ old('role') == 3 ? 'selected' : '' }}>User</option>
                            <option value="5" {{ old('role') == 5 ? 'selected' : '' }}>Admin</option>
                            <option value="7" {{ old('role') == 7 ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               placeholder="Enter password (min. 6 characters)" required>
                        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                               class="form-control form-control-lg"
                               placeholder="Re-enter password" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-2">Create User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
