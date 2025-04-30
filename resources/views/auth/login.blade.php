@extends('layouts.auth')

@section('title')
    {{ translate('Login to your account') }}
@endsection

@section('content')
    <div class="row m-0">
        <div class="col-lg-12 col-12 px-4 py-3">
            <img src="{{ uploaded_asset(get_setting('site_icon')) }}" alt="{{ translate('Site Icon') }}" >
            <h4 class="mb-2 card-title">{{ translate('Login to your account') }}</h4>
            <p>{{ translate('Welcome to') }} {{ env('APP_NAME') }}</p>
            <form class="form-default" role="form" action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="fs-12 fw-700 text-soft-dark">{{  translate('Email') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0" value="{{ old('email') }}" placeholder="{{  translate('johndoe@example.com') }}" name="email" id="email" autocomplete="off">
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                    
                <!-- password -->
                <div class="form-group">
                    <label for="password" class="fs-12 fw-700 text-soft-dark">{{  translate('Password') }}</label>
                    <div class="position-relative">
                        <input type="password" class="form-control rounded-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password')}}" name="password" id="password">
                        <i class="password-toggle las la-2x la-eye"></i>
                    </div>
                </div>
                

                <div class="row mb-2">
                    <!-- Remember Me -->
                    <div class="col-6">
                        <label class="aiz-checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="has-transition fs-12 fw-400 text-gray-dark hov-text-primary">{{  translate('Remember Me') }}</span>
                            <span class="aiz-square-check"></span>
                        </label>
                    </div>
                    
                    <!-- Forgot password -->
                    <div class="col-6 text-right">
                        <a href="{{ route('password.request') }}" class="text-reset fs-12 fw-400 text-gray-dark hov-text-primary"><u>{{ translate('Forgot password?')}}</u></a>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-sm-row flex-column">
                    <button type="submit" class="btn btn-primary btn-block fw-700 fs-14 rounded-0">{{  translate('Login') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
