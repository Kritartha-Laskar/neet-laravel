<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register | NeetCommon</title>
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/flag-icon-css/css/flag-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/css/vertical-light-layout/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('src/assets/images/favicon.png') }}" />
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">

                            <div class="brand-logo">
                                <h3 class="text-primary fw-bold">NeetCommon</h3>
                            </div>

                            <h4>New here?</h4>
                            <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>

                            {{-- Validation errors --}}
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('register.post') }}">
                                @csrf

                                <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        placeholder="Full Name"
                                        value="{{ old('name') }}"
                                        required
                                    >
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control form-control-lg @error('user_name') is-invalid @enderror"
                                        id="user_name"
                                        name="user_name"
                                        placeholder="Username"
                                        value="{{ old('user_name') }}"
                                        required
                                    >
                                    @error('user_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <input
                                        type="email"
                                        class="form-control form-control-lg @error('gmail') is-invalid @enderror"
                                        id="gmail"
                                        name="gmail"
                                        placeholder="Gmail"
                                        value="{{ old('gmail') }}"
                                        required
                                    >
                                    @error('gmail') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control form-control-lg @error('phone_no') is-invalid @enderror"
                                        id="phone_no"
                                        name="phone_no"
                                        placeholder="Phone Number"
                                        value="{{ old('phone_no') }}"
                                    >
                                    @error('phone_no') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <input
                                        type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Password"
                                        required
                                    >
                                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Confirm Password"
                                        required
                                    >
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input" required> I agree to all Terms &amp; Conditions
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn d-grid btn-primary btn-lg font-weight-medium auth-form-btn w-100">
                                        SIGN UP
                                    </button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account? <a href="{{ route('login') }}" class="text-primary">Login</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('src/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('src/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('src/assets/js/misc.js') }}"></script>
</body>
</html>
