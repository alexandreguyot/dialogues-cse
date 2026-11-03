@extends('layouts.auth')

@section('content')
<form class="auth-card" method="POST" action="{{ route('two-factor.verify') }}">
    @csrf
    <h1>Code de vérification</h1>
    <p class="auth-text">Saisissez le code à 6 chiffres de votre application, ou un code de secours.</p>

    <div class="form-group {{ $errors->has('code') ? 'invalid' : '' }}">
        <label class="form-label" for="code">Code</label>
        <input class="form-control" id="code" name="code" type="text" autocomplete="one-time-code" required autofocus />
        <div class="validation-message">{{ $errors->first('code') }}</div>
    </div>

    <button class="btn btn-indigo auth-submit">Valider</button>
    @include('auth.two-factor.logout')
</form>
@endsection
