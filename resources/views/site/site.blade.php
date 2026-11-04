@extends('layouts.app')
@section('title', 'Création de site internet pour CSE | Dialogues CSE')
@section('description', 'Dialogues CSE crée un site internet adapté à vos besoins et à votre public, puis vous aide à l\'animer et à produire son contenu.')
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
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png" alt="Illustration de la création d'un site internet">
                    <p>Création d’un site internet adapté à vos besoins et à votre public</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/flash_info.png" alt="Illustration de l'animation d'un site internet">
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
