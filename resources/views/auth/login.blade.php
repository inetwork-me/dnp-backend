@extends('layouts.auth')

@section('title')
    {{ translate('Login to your account') }}
@endsection

@section('body')
<form class="form-default" role="form" action="{{ route('login') }}" method="POST">
    @csrf
    
    <div class="form-group mb-3">
        <label for="email" class="text-white">{{  translate('Email') }}</label>
        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0" value="{{ old('email') }}"  name="email" id="email" autocomplete="off">
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
        
    <!-- password -->
    <div class="form-group mb-3">
        <label for="password" class="text-white" >{{  translate('Password') }}</label>
        <div class="position-relative">
            <input type="password" class="form-control rounded-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password">
            <i class="password-toggle las la-2x la-eye"></i>
        </div>
    </div>
    

    <div class="row mb-2">
        <!-- Remember Me -->
        <div class="col-6">
            <label class="aiz-checkbox">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="has-transition fs-12 fw-400 text-white">{{  translate('Remember Me') }}</span>
                <span class="aiz-square-check"></span>
            </label>
        </div>
        
        <!-- Forgot password -->
        <div class="col-6 text-right">
            <a href="{{ route('password.request') }}" class="fs-12 fw-400 text-white"><u>{{ translate('Forgot password?')}}</u></a>
        </div>
    </div>

    <div class="d-flex justify-content-between flex-sm-row flex-column mt-4">
        <button type="submit" class="btn btn-custom">{{  translate('Login') }}</button>
    </div>
</form>
@endsection
