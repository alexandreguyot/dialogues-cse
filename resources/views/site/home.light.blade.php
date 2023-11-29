@extends('layouts.app')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Une identité visuelle qui vous ressemble</h1>
            <p>Offrez à votre CSE une identité visuelle distinctive, permettant une identification rapide et aisée par vos collègues. Cette identité reflète vos valeurs et engagements uniques. Elle peut être originale et mémorable, laissant une empreinte marquante. Notre équipe est là pour vous accompagner dans cette démarche en vous proposant :</p>
        </div>
        <div class="carousel support">
            <div class="slot-container">
                <div class="slot">
                    <img src="img/illustrations/catalogue_&_rs.png">
                    <h4>Une identité visuelle clé en main</h4>
                    <p>Notre équipe d’experts vous propose de réaliser pour vous une charte graphique propre à votre CSE</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/catalogue_&_rs.png">
                    <h4>Une communication à votre image</h4>
                    <p>Dialogues CSE vous permet de mettre en œuvre les outils de communication adaptés à votre stratégie et à votre public</p>
                </div>
                <div class="slot">
                    <img src="img/illustrations/catalogue_&_rs.png">
                    <h4>Des goodies qui vous rendront fiers de votre CSE</h4>
                    <p>Dialogues CSE vous propose de créer pour vous des goodies à votre image et éco-responsables</p>
                </div>
            </div>
        </div>
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
