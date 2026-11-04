<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ asset('css/site.css') }}" />
    <title>@yield('title', __('panel.site_title'))</title>
    @if(filled(trim($__env->yieldContent('description'))))
        <meta name="description" content="@yield('description')" />
        <meta property="og:description" content="@yield('description')" />
    @endif
    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="fr_FR" />
    <meta property="og:site_name" content="Dialogues CSE" />
    <meta property="og:title" content="@yield('title', __('panel.site_title'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ url('img/illustrations/outils_de_com.png') }}" />
    <meta name="twitter:card" content="summary" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Pacifico&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="{{ $theme ?? 'light' }}">
    <main>
        <header>
            <div class="logo">
                <a href="{{ route('home')}}">
                    <img src="{{ asset('img/logo.svg') }}" alt="Dialogues">
                </a>
                <span class="barre"> | </span><span class="text"> CSE</span>
            </div>
            <div class="nav">
                @foreach($menuPages as $menuPage)
                    <a href="{{ route('pages.show', $menuPage->slug) }}" class="nav-link">{{ $menuPage->title }}</a>
                @endforeach
                <a href="{{ route('devis')}}" class="btn green">Demander un devis</a>
            </div>
        </header>
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <script type="text/javascript" src="{{ asset('js/site.js') }}"></script>
    @stack('scripts')
</body>

</html>
