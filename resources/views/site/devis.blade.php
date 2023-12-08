@extends('layouts.app')
@section('content')
    {!! NoCaptcha::renderJs('fr', true, 'recaptchaCallback') !!}
    <div class="content">
        <div class="hero">
            <h1>Besoin d'un devis ou d'un renseignement(s)</h1>
            <p>
                Un site web bien conçu est essentiel pour une communication efficace.
                Laissez notre équipe vous guider dans la création et l'animation de votre site internet.
            </p>
        </div>
        <form action="{{ route('sendEmail')}}" method="POST">
            <div class="form">
                <div class="form-container">
                        <div class="element">
                            <label for="lastname">Nom</label>
                            <input type="text" name="lastname" id="lastname">
                        </div>
                        <div class="element">
                            <label for="firsname">Prénom</label>
                            <input type="text" name="firsname" id="firsname">
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
                            <label for="message">Message</label>
                            <textarea type="textarea" name="message" id="message"></textarea>
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
                <li><a href="#" class="btn purple">Télécharger notre brochure</a></li>
                <li><a href="#" class="btn purple">S'inscrire à notre newsletter</a></li>
                <li><a href="{{ route('devis')}}" class="btn green">Demander un devis</a></li>
            </ul>
        </div>
    </div>
@endsection
