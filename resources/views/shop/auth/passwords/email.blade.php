@extends('shop.layouts.auth')


@section('content')
    <div class="card-title text-uppercase pb-2">{{ __('Reset Password') }}</div>
        <p>Fill bellow form to reset your password</p>
        <img src="{{ asset('staff/admin/images/favicon.png') }}" alt="">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
  
    <form action="{{ route('shop.password.email') }}" method="post">
        @csrf
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
        <button type="submit"
                class="btn btn-primary  btn-block mt-3">{{ __('Send Password Reset Link') }}</button>
    </form>
@endsection
