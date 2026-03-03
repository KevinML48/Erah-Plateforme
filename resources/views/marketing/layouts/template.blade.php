<!DOCTYPE html>
<html lang="fr">
<head>
  <title>@yield('title', 'ERAH Esport')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="@yield('meta_description', 'ERAH Esport est une association basée à Mende (Lozère), spécialisée dans la compétition et la promotion du gaming local et national.')">
  <meta name="keywords" content="@yield('meta_keywords', 'ERAH Esport, esport Lozère, esport Mende, club esport, gaming, compétitions esport, événements esport, association esport, tournois gaming, sport électronique')">
  <meta name="author" content="@yield('meta_author', 'ERAH Esport')">

  <link rel="icon" href="/template/assets/img/logo.png" type="image/png" sizes="512x512">
  <link rel="apple-touch-icon" href="/template/assets/img/logo.png">

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "ERAH Esport",
    "url": "https://erah-esport.fr",
    "logo": "/template/assets/img/logo.png",
    "sameAs": [
      "https://www.twitch.tv/erah_association",
      "https://www.instagram.com/erahesport/",
      "https://x.com/ErahEsport",
      "https://discord.gg/9G89kkSjRx"
    ]
  }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Big+Shoulders+Display:wght@100..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/template/assets/css/helper.css">
  <link rel="stylesheet" href="/template/assets/css/theme.css">
  <link rel="stylesheet" href="/template/assets/css/theme-light.css">

  <link rel="preload" href="/template/assets/vendor/fontawesome/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/fancybox/css/fancybox.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="/template/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/template/assets/vendor/fancybox/css/fancybox.css">
    <link rel="stylesheet" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css">
  </noscript>

  <script async src="https://www.googletagmanager.com/gtag/js?id=G-H9C6F8VG4D"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-H9C6F8VG4D', { 'anonymize_ip': true });
  </script>

  <style>
    .tt-alert { margin-bottom: 10px; padding: 14px 18px; border-radius: 10px; border: 1px solid rgba(255,255,255,.12); }
    .tt-alert-success { background: rgba(24, 110, 59, .22); color: #d9ffe8; }
    .tt-alert-danger { background: rgba(173, 41, 41, .25); color: #ffe1e1; }
    .tt-hidden-honeypot { position: absolute !important; left: -9999px !important; opacity: 0 !important; pointer-events: none !important; }
  </style>

  @yield('head_extra')
</head>

@yield('page_styles')

<body id="body" class="@yield('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')">
  <main id="body-inner">
    <div id="tt-page-transition">
      <div class="tt-ptr-overlay-top tt-noise"></div>
      <div class="tt-ptr-overlay-bottom tt-noise"></div>
      <div class="tt-ptr-preloader">
        <div class="tt-ptr-prel-content">
          <img src="/template/assets/img/logo.png" class="tt-ptr-prel-image" alt="Logo">
        </div>
      </div>
    </div>

    <div id="magic-cursor">
      <div id="ball"></div>
    </div>

    @include('marketing.partials.header')

    <div id="tt-content-wrap">
      @if (session('success') || session('error') || $errors->any())
        <div class="tt-section padding-top-20 padding-bottom-20">
          <div class="tt-section-inner tt-wrap">
            @if (session('success'))
              <div class="tt-alert tt-alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
              <div class="tt-alert tt-alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
              <div class="tt-alert tt-alert-danger">
                <ul style="margin: 0; padding-left: 18px;">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        </div>
      @endif

      @yield('content')
    </div>

    @include('marketing.partials.footer')
  </main>

  @yield('page_scripts')
</body>
</html>
