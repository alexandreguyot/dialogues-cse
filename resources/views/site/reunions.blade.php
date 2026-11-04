@extends('layouts.app')
@section('title', 'Préparation des réunions CSE | Dialogues CSE')
@section('description', 'Dialogues CSE vous aide à préparer vos réunions, à suivre les points à l\'ordre du jour et à communiquer autour de chaque réunion.')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Vous aider lors des réunions CSE</h1>
            <p>Offrez à votre CSE une identité visuelle distinctive, permettant une identification rapide et aisée par vos collègues. Cette identité reflète vos valeurs et engagements uniques. Elle peut être originale et mémorable, laissant une empreinte marquante. Notre équipe est là pour vous accompagner dans cette démarche en vous proposant :</p>
        </div>
        <div class="support">
            <div class="slot-container">
                <div class="slot">
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png" alt="Illustration de la préparation d'une réunion">
                    <p>Préparer les réunions et assurer le suivi des points à l’ordre du jour</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/crayon_girl.png" alt="Illustration de la préparation d'une réunion">
                    <p>Préparer les réunions et assurer le suivi des points à l’ordre du jour</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/flash_info.png" alt="Illustration d'une action de communication">
                    <p>Préparer les actions de communication autour de la réunion</p>
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
