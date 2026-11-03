@extends('layouts.auth')

@section('content')
<form class="auth-card" method="POST" action="{{ route('login') }}">
    @csrf
    <h1>Espace de gestion</h1>

    <div class="form-group {{ $errors->has('email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ __('global.login_email') }}</label>
        <input class="form-control" id="email" name="email" type="email" required autocomplete="email" autofocus value="{{ old('email') }}" />
        <div class="validation-message">{{ $errors->first('email') }}</div>
    </div>

    <div class="form-group {{ $errors->has('password') ? 'invalid' : '' }}">
        <label class="form-label" for="password">{{ __('global.login_password') }}</label>
        <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password" />
        <div class="validation-message">{{ $errors->first('password') }}</div>
    </div>

    <label class="auth-remember">
        <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} />
        {{ __('global.remember_me') }}
    </label>

    <button class="btn btn-indigo auth-submit">{{ __('global.login') }}</button>

    @if(Route::has('password.request'))
        <a class="auth-link" href="{{ route('password.request') }}">{{ __('global.forgot_password') }}</a>
    @endif
</form>
@endsection
