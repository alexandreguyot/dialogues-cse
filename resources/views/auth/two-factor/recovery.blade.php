@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <h1>Codes de secours</h1>
    <p class="auth-text">Ils remplacent l'application si vous perdez votre téléphone. Chaque code ne sert qu'une fois. Conservez-les dans un gestionnaire de mots de passe : ils ne seront plus affichés.</p>
    <ul class="auth-codes">
        @foreach($codes as $code)
            <li><code>{{ $code }}</code></li>
        @endforeach
    </ul>
    <a class="btn btn-indigo auth-submit" href="{{ route('admin.home') }}">J'ai conservé mes codes</a>
</div>
@endsection
