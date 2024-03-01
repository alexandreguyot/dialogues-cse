@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Un site web qui parle pour vous</h1>
            <p>
                Un site web bien conçu est essentiel pour une communication efficace.
                </br>
                </br>
                Laissez notre équipe vous guider dans la création et l'animation de votre site internet.
            </p>
        </div>
        <div class="support">
            <div class="slot-container">
                <div class="slot">
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png">
                    <p>Création d’un site internet adapté à vos besoins et à votre public</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/flash_info.png">
                    <p>Animation du site et création de contenu</p>
                </div>
            </div>
        </div>
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
