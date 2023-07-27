@extends('shop.layouts.auth')

@section('content')

<div class="card card-authentication1 mx-auto my-5">
    <div class="card-body">
        <div class="card-content p-2">

            <div class="text-center">
                <img src="{{ config('ui.logo.large') }}" alt="logo icon">
            </div>

            <div class="card-title text-uppercase text-center py-3">Vendor Sign In</div>

            <form action="{{ route('shop.login') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="position-relative has-icon-right">
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="your Email here.." autocomplete="email" autofocus required>

                            <div class="form-control-position">
                            <i class="icon-user"></i>
                            </div>
                        @error('email')
                        <span class="invalid-feedback text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror 
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="position-relative has-icon-right">
                        <input type="password" id="password" name="password"
                            class="form-control form-control-password @error('password') is-invalid @enderror"
                            placeholder="Your Password here.." autocomplete="current-password" required>
                        <div class="form-control-position">
                            <i class="icon-lock"></i>
                        </div>
                        <span class="eye"><i class="fa fa-eye-slash"></i></span>
                        @error('password')
                        <span class="invalid-feedback text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror 
                    </div>
                </div>
                <div class="d-flex justify-content-between rememberme align-items-center mb-4">
                    <div>
                        <div class="form-group form-check mb-0">
                            <input class="styled-checkbox form-check-input" type="checkbox"
                                name="remember"
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="text-right"><a href="{{ route('shop.password.request') }}">Forgot Password?</a></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
<div>

@endsection

@section('js')
    <script>
        $(document).on('click', '.eye', function (e) {
            if (document.getElementById('password').type === 'password') {
                document.getElementById('password').type = 'text'
                $(this).children('i').removeClass('fa-eye-slash')
                $(this).children('i').addClass('fa-eye')
            } else {
                document.getElementById('password').type = 'password'
                $(this).children('i').removeClass('fa-eye')
                $(this).children('i').addClass('fa-eye-slash')
            }
        })
    </script>
@endsection
