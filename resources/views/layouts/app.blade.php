<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ asset('css/site.css') }}" />
    <title>{{ __('panel.site_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Pacifico&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <body class="light">
        <main>
            <header>
                <div class="logo">
                    <a href="{{ route('home')}}">
                        <img src="img/logo.svg">
                    </a>
                    <span class="barre"> | </span><span class="text"> CSE</span>
                </div>
                <div class="nav"><a href="#" class="btn green">Demander un devis</a></div>
            </header>
            @yield('content')
        </main>
        <script type="text/javascript" src="{{ asset('js/site.js') }}"></script>
    </body>

</html>
