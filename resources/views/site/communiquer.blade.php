@extends('layouts.app')
@section('title', 'Communication du CSE au quotidien | Dialogues CSE')
@section('description', 'Livret de présentation, réseaux sociaux, flash info, newsletter, enquêtes, événements et bilan de fin de mandat pour votre CSE.')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Communiquer au quotidien</h1>
            <p>Chez Dialogues, nous croyons en l'importance de cultiver un lien quotidien fort avec vos salariés. Nous proposons les solutions suivantes :</p>
        </div>
        <div class="support">
            <div class="slot-container">
                <div class="slot">
                    <img src="img/illustrations/catalogue_&_rs.png" alt="Illustration d'un livret de présentation et des réseaux sociaux du CSE">
                    <p>Créer pour vous un livret de présentation du CSE, de ses actions et ses activités et d’animer vos réseaux sociaux</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/flash_info.png" alt="Illustration d'un flash info">
                    <p>Réaliser un flash info ou une newsletter régulière</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/enquete.png" alt="Illustration d'une enquête auprès des salariés">
                    <p>Réaliser des enquêtes ou sondages en fonction de vos besoins</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/evenement.png" alt="Illustration d'un événement interne">
                    <p>Nous occuper de la création d’événements en interne</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png" alt="Illustration d'un bilan de fin de mandat">
                    <p>Réaliser vos bilans de fin de mandats</p>
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
