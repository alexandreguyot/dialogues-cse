@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Communiquer au quotidien</h1>
            <p>Chez Dialogues, nous croyons en l'importance de cultiver un lien quotidien fort avec vos salariés. Nous proposons les solutions suivantes :</p>
        </div>
        <div class="support">
            <div class="slot-container">
                <div class="slot">
                    <img src="img/illustrations/catalogue_&_rs.png">
                    <p>Créer pour vous un livret de présentation du CSE, de ses actions et ses activités et d’animer vos réseaux sociaux</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/flash_infos.png">
                    <p>Réaliser un flash info ou une newsletter régulière</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/enquete.png">
                    <p>Réaliser des enquêtes ou sondages en fonction de vos besoins</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/evenement.png">
                    <p>Nous occuper de la création d’événements en interne</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png">
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
