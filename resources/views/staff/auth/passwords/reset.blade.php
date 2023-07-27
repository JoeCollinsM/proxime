@extends('staff.layouts.auth')

@section('content')
<div class="card-title text-uppercase pb-2">{{ __('Reset Password') }}</div>
        <p>Fill bellow form to reset you password</p>
        <img src="{{ asset('staff/images/user.svg') }}" alt="">
    <form action="{{ route('staff.password.update') }}" method="post">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label for="email">{{ __('E-Mail Address') }}</label>
            <div class="position-relative has-icon-right">
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="your Email here.." autocomplete="email" autofocus required>
                <div class="form-control-position">
					  <i class="icon-envelope-open"></i>
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
                <div class="form-control-position eye">
                    <i class="fa fa-eye-slash"></i>
				</div>
                @error('password')
                <span class="invalid-feedback text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <div class="position-relative has-icon-right">
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control form-control-password @error('password_confirmation') is-invalid @enderror"
                    placeholder="Retype Password here.." autocomplete="new-password" required>
                <div class="form-control-position eye">
                    <i class="fa fa-eye-slash"></i>
				</div>
                @error('password_confirmation')
                <span class="invalid-feedback text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
    </form>
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
