@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Besoin d'un renseignement ?</h1>
        </div>
        <form action="{{ route('sendEmail')}}" method="POST">
            <div class="form">
                <div class="form-container">
                        <div class="element">
                            <label for="lastname">Nom et Prénom</label>
                            <input type="text" name="lastname" id="lastname">
                        </div>
                        <div class="element">
                            <label for="email">Adresse e-mail</label>
                            <input type="mail" name="email" id="email">
                        </div>
                        <div class="element">
                            <label for="tel">Téléphone</label>
                            <input type="tel" name="tel" id="tel">
                        </div>
                        <div class="element">
                            <label for="societe">Entreprise</label>
                            <input type="text" name="societe" id="societe">
                        </div>
                        <div class="element">
                            <label for="date">À quelle date pouvons nous vous joindre ?</label>
                            <input type="date" name="date" id="date">
                        </div>
                        <div class="element">
                            <label for="message">Indiquez-nous en quelques lignes votre projet</label>
                            <textarea type="textarea" name="message" id="message" rows="6"></textarea>
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
