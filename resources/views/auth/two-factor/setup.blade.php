@extends('layouts.auth')

@section('content')
<form class="auth-card" method="POST" action="{{ route('two-factor.confirm') }}">
    @csrf
    <h1>Activer l'authenticator</h1>
    <p class="auth-text">Scannez ce QR code avec Google Authenticator, Microsoft Authenticator ou 1Password, puis saisissez le code à 6 chiffres.</p>

    <div class="auth-qr">{!! $qrCode !!}</div>
    <p class="auth-secret">Sans appareil photo, saisissez cette clé :<br><code>{{ $secret }}</code></p>

    <div class="form-group {{ $errors->has('code') ? 'invalid' : '' }}">
        <label class="form-label" for="code">Code à 6 chiffres</label>
        <input class="form-control" id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus />
        <div class="validation-message">{{ $errors->first('code') }}</div>
    </div>

    <button class="btn btn-indigo auth-submit">Activer</button>
    @include('auth.two-factor.logout')
</form>
@endsection
