@extends('layouts.auth')

@section('title')
    {{ translate('Login to your account') }}
@endsection

@section('content')
    <div class="d-flex justify-content-center align-items-center login-container">
        <div class="login-box">
            <div class="text-center mb-3">
                <div class="login-title">{{ translate('Login') }}</div>
                <div class="login-subtitle">{{ translate('Login to dashboard to access to your work') }}</div>
            </div>
            <form role="form" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="floating-label">
                    <input type="email" name="email"
                        class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                        placeholder="{{ translate('Email') }}" required>
                    <label>{{ translate('Email') }}</label>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="floating-label">
                    <input type="password" name="password" class="form-control"
                        placeholder="{{ translate('Password') }} {{ $errors->has('password') ? ' is-invalid' : '' }}"
                        required>
                    <label>{{ translate('Password') }}</label>
                </div>

                <div class="form-group form-check custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remember" name="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember">{{ translate('Remember Me') }}</label>
                </div>

                <button type="submit" class="btn btn-login btn-block">{{ translate('Login') }}</button>
                <a href="{{ route('password.request') }}" class="forgot-password">{{ translate('Forgot password?') }}</a>
            </form>
        </div>
    </div>
@endsection
