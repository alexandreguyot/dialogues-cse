@extends('layouts.app-dark')
@section('title', 'Communication sur mesure pour votre CSE | Dialogues CSE')
@section('description', 'Identité visuelle, communication au quotidien, aide aux réunions et site web : Dialogues CSE propose des solutions sur mesure pour votre CSE.')
@section('content')
    <div class="content">
        <div class="hero">
            <h1>Une solution unique pour votre CSE</h1>
            <p>Des solutions sur mesure pour une communication impactante</p>
            <a href="{{ route('devis')}}" class="btn green">Demander un devis</a>
        </div>
        <div class="cards-container">
            <div class="card">
                <h3 class="accordion">Une identité visuelle qui vous ressemble<img src="img/chevron.svg" alt=""></h3>
                <a href="{{ route('identite')}}" style="text-decoration:none">
                    <div>
                        <p>Offrez à votre CSE une identité visuelle distinctive, permettant une identification rapide et aisée par vos collègues. Cette identité …</p>
                        <a href="{{ route('identite')}}">En savoir plus</a>
                        <img src="img/illustrations/identite_visuelle.png" alt="Illustration d'une identité visuelle de CSE" />
                    </div>
                </a>
            </div>
            <div class="card">
                <h3 class="accordion">Communiquer au quotidien<img src="img/chevron.svg" alt=""></h3>
                <a href="{{ route('communiquer')}}" style="text-decoration:none">
                    <div>
                        <p>Chez Dialogues, nous croyons en l'importance de cultiver un lien quotidien et fort avec vos salariés. Nous proposons…</p>
                        <a href="{{ route('communiquer')}}">En savoir plus</a>
                        <img src="img/illustrations/catalogue_&_rs.png" alt="Illustration d'un livret de présentation et des réseaux sociaux du CSE" />
                    </div>
                </a>
            </div>
            <div class="card">
                <h3 class="accordion">Vous aider lors des réunions CSE<img src="img/chevron.svg" alt=""></h3>
                <a href="{{ route('reunions')}}" style="text-decoration:none">
                    <div>
                        <p>Les réunions CSE sont des moments cruciaux nécessitant une préparation minutieuse et un suivi rigoureux…</p>
                        <a href="{{ route('reunions')}}">En savoir plus</a>
                        <img src="img/illustrations/outils_de_com.png" alt="Illustration de la préparation d'une réunion" />
                    </div>
                </a>
            </div>
            <div class="card">
                <h3 class="accordion">Un site web qui parle pour vous<img src="img/chevron.svg" alt=""></h3>
                <a href="{{ route('site')}}" style="text-decoration:none">
                <div>
                    <p>Un site web bien conçu est essentiel pour une communication efficace. Laissez notre équipe vous guider…</p>
                    <a href="{{ route('site')}}">En savoir plus</a>
                    <img src="img/illustrations/bilan_de_fin_de_mandat.png" alt="Illustration de la création d'un site internet" />
                </div>
                </a>
            </div>
        </div>
        <div class="carousel testimonials">
            <h2>Ils ont dialogué avec nous</h2>
            <ul class="nav_arrow"><li><img id="arrow_left" src="img/arrow_left.png" alt="Précédent"></li><li><img id="arrow_right" src="img/arrow_right.png" alt="Suivant"></li></ul>
            <div class="pin-container">
                <div class="pin" data-position="1">
                    <img src="img/illustrations/katell.png" alt="Portrait de Katell" class="face">
                    <p>“J’ai présenté le bilan à mon équipe et comme moi ils ont été emballés, ravis… Cela nous représente tellement bien ! Les images, les accroches… Alors un grand merci pour ce joli travail.”</p>
                    <span class="name">Katell<br>Secrétaire de CSE</span>
                </div>
                <div class="pin" data-position="2">
                    <img src="img/illustrations/olivier.png" alt="Portrait d'Olivier" class="face">
                    <p>“Dialogues CSE nous accompagne dans la préparation des réunions avec la direction et dans la réalisation des PV.  L'expertise juridique et l'expérience des CSE ont été déterminants dans notre choix de travailler avec vous.“</p>
                    <span class="name">Olivier<br>Secrétaire de CSE</span>
                </div>
                <div class="pin" data-position="3">
                    <img src="img/illustrations/rosa.png" alt="Portrait de Rosa" class="face">
                    <p>“Merci pour votre travail ! Après les élections, nous avions envie de faire connaître le CSE avec des idées originales et c’est mission réussie. Nous avons proposé une cocotte pour présenter le CSE, retour en enfance garanti !”</p>
                    <span class="name">Rosa<br>Membre de CSE</span>
                </div>
                <div class="pin" data-position="4">
                    <img src="img/illustrations/anissa.png" alt="Portrait d'Anissa" class="face">
                    <p>“Merci encore de nous avoir proposé des solutions en accord avec nos valeurs. Nous avons pu mettre en place une campagne de communication et d'affichages avec les illustrations originales de Léonie, les salariés ont beaucoup apprécié.”</p>
                    <span class="name">Anissa<br>Responsable communication de CSE</span>
                </div>
            </div>
        </div>
        <div class="carousel support">
            <h2>Nos engagements</h2>
            <ul class="nav_arrow"><li><img id="arrow_left" src="img/arrow_left.png" alt="Précédent"></li><li><img id="arrow_right" src="img/arrow_right.png" alt="Suivant"></li></ul>
            <div class="slot-container">
                <div class="slot" data-position="1">
                    <img src="img/illustrations/flash_info.png" alt="Illustration d'un flash info">
                    <h4>Une communication qui vous ressemble</h4>
                    <p>Nous mettons en œuvre une stratégie de communication adaptée à vos envies et à besoins, en vous associant à chaque étape de notre travail. Nous répondons rapidement à toutes vos demandes pour coller à l’actualité de votre entreprise.</p>
                </div>
                <div class="slot" data-position="2">
                    <img src="img/illustrations/catalogue_&_rs.png" alt="Illustration d'un livret de présentation et des réseaux sociaux du CSE">
                    <h4>Une équipe à l’écoute, experte dans le domaine des CSE</h4>
                    <p>Vous disposez désormais d’un interlocuteur unique pour l’ensemble de vos besoins dans le domaine de la communication. Nos experts vous proposent des solutions adaptées à vos besoins et vos contraintes. Ils veillent pour vous au respect des règles légales pour communiquer sans inquiétude.</p>
                </div>
                <div class="slot" data-position="3">
                    <img src="img/illustrations/eco_respo.png" alt="Illustration d'un engagement éco-responsable">
                    <h4>Des solutions éco-responsables</h4>
                    <p>Nous n’avons qu’une planète. Nous nous engageons à proposer des solutions respectueuses de l'environnement. Nous rejoindre, c'est faire le choix d'une communication plus verte.</p>
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
@push('scripts')
    <script type="application/ld+json">
        {"@@context":"https://schema.org","@@type":"Organization","name":"Dialogues CSE","url":"{{ url('/') }}","logo":"{{ url('img/logo.svg') }}"}
    </script>
@endpush
