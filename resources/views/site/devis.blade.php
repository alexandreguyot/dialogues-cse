@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Besoin d'un renseignement ?</h1>
        </div>
        <form action="{{ route('sendEmail')}}" method="POST">
            @csrf
            <div class="form">
                <div class="form-container">
                        <div class="element">
                            <label for="lastname">Nom et Prénom</label>
                            <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" required>
                            @error('lastname')<span class="help-block"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="element">
                            <label for="email">Adresse e-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                            @error('email')<span class="help-block"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="element">
                            <label for="tel">Téléphone</label>
                            <input type="tel" name="tel" id="tel" value="{{ old('tel') }}">
                        </div>
                        <div class="element">
                            <label for="societe">Entreprise</label>
                            <input type="text" name="societe" id="societe" value="{{ old('societe') }}">
                        </div>
                        <div class="element">
                            <label for="date">À quelle date pouvons nous vous joindre ?</label>
                            <input type="date" name="date" id="date" value="{{ old('date') }}">
                        </div>
                        <div class="element">
                            <label for="message">Indiquez-nous en quelques lignes votre projet</label>
                            <textarea name="message" id="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')<span class="help-block"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        {!! NoCaptcha::display() !!}
                        @if ($errors->has('g-recaptcha-response'))
                            <span class="help-block">
                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </span>
                        @endif
                        <div class="submit">
                            <input type="submit" value="Envoyer la demande" class="btn green">
                        </div>
                </div>
            </div>
        </form>
        <div class="contact">
            <h2>On garde contact ? 🤙</h2>
            <ul>
                <li><a href="{{ asset('pdf/catalogue_dialogues.pdf')}}" class="btn purple" download>Télécharger notre brochure</a></li>
                <li><a href="#" class="btn purple">S'inscrire à notre newsletter</a></li>
                <li><a href="{{ route('devis')}}" class="btn green">Demander un devis</a></li>
            </ul>
        </div>
    </div>
@endsection
@push('scripts')
    {!! NoCaptcha::renderJs() !!}
@endpush
