<!DOCTYPE html>
<html lang="fr">
<head>
  <title>@yield('title', 'ERAH Esport')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="@yield('meta_description', 'ERAH Esport est une association basée à Mende (Lozère), spécialisée dans la compétition et la promotion du gaming local et national.')">
  <meta name="keywords" content="@yield('meta_keywords', 'ERAH Esport, esport Lozère, esport Mende, club esport, gaming, compétitions esport, événements esport, association esport, tournois gaming, sport électronique')">
  <meta name="author" content="@yield('meta_author', 'ERAH Esport')">
  <meta name="theme-color" content="#d80707">
  <link rel="manifest" href="/manifest.json">

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
  <link rel="stylesheet" href="/template/assets/css/platform-responsive.css">
  <link rel="stylesheet" href="/template/assets/css/platform-motion.css">

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
    .tt-toast-stack { position: fixed; top: 92px; right: 16px; width: min(420px, calc(100vw - 24px)); display: grid; gap: 10px; z-index: 2000; pointer-events: none; }
    .tt-toast { margin: 0; box-shadow: 0 10px 28px rgba(0, 0, 0, .35); backdrop-filter: blur(4px); pointer-events: auto; transition: opacity .2s ease, transform .2s ease; }
    .tt-toast.toast-leaving { opacity: 0; transform: translateY(-8px); }
    .tt-toast-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .tt-toast-close { background: transparent; border: 0; color: inherit; font-size: 18px; line-height: 1; cursor: pointer; opacity: .8; }
    .tt-toast-close:hover { opacity: 1; }
    .tt-toast ul { margin: 8px 0 0; padding-left: 18px; }
    .tt-hidden-honeypot { position: absolute !important; left: -9999px !important; opacity: 0 !important; pointer-events: none !important; }
    @media (max-width: 991.98px) { .tt-toast-stack { top: 82px; right: 10px; width: calc(100vw - 20px); } }
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
      @php($hideGlobalAlerts = trim($__env->yieldContent('hide_global_alerts', '0')) === '1')
      @if (!$hideGlobalAlerts && (session('success') || session('error') || $errors->any()))
        <div class="tt-toast-stack" id="tt-toast-stack" aria-live="polite">
          @if (session('success'))
            <div class="tt-alert tt-alert-success tt-toast" role="status">
              <div class="tt-toast-head">
                <strong>Succes</strong>
                <button type="button" class="tt-toast-close" data-toast-close aria-label="Fermer">&times;</button>
              </div>
              <div>{{ session('success') }}</div>
            </div>
          @endif
          @if (session('error'))
            <div class="tt-alert tt-alert-danger tt-toast" role="alert">
              <div class="tt-toast-head">
                <strong>Erreur</strong>
                <button type="button" class="tt-toast-close" data-toast-close aria-label="Fermer">&times;</button>
              </div>
              <div>{{ session('error') }}</div>
            </div>
          @endif
          @if ($errors->any())
            <div class="tt-alert tt-alert-danger tt-toast" role="alert">
              <div class="tt-toast-head">
                <strong>Verification</strong>
                <button type="button" class="tt-toast-close" data-toast-close aria-label="Fermer">&times;</button>
              </div>
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      @endif

      @yield('content')
    </div>

    @include('marketing.partials.footer')
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var stack = document.getElementById('tt-toast-stack');
      if (!stack) {
      } else {
        var removeToast = function (toast) {
          if (!toast) {
            return;
          }

          toast.classList.add('toast-leaving');
          window.setTimeout(function () {
            toast.remove();
            if (!stack.querySelector('.tt-toast')) {
              stack.remove();
            }
          }, 180);
        };

        stack.querySelectorAll('[data-toast-close]').forEach(function (button) {
          button.addEventListener('click', function () {
            removeToast(button.closest('.tt-toast'));
          });
        });

        window.setTimeout(function () {
          stack.querySelectorAll('.tt-toast').forEach(function (toast) {
            removeToast(toast);
          });
        }, 5200);
      }

      var isMobile = window.matchMedia('(max-width: 1199.98px)').matches;
      if (!isMobile) {
        return;
      }

      var mobileToggle = document.querySelector('.tt-m-menu-toggle-btn');
      var mobileMenuHolder = document.querySelector('.tt-main-menu-holder');
      if (!mobileToggle || !mobileMenuHolder) {
        return;
      }

      mobileMenuHolder.querySelectorAll('a[href]').forEach(function (link) {
        var href = (link.getAttribute('href') || '').trim();
        if (href === '' || href === '#') {
          return;
        }

        link.addEventListener('click', function () {
          mobileToggle.click();
        });
      });
    });

    window.addEventListener('load', function () {
      if (!('serviceWorker' in navigator)) {
        return;
      }

      navigator.serviceWorker.getRegistrations().then(function (registrations) {
        registrations.forEach(function (registration) {
          registration.unregister();
        });
      });

      if ('caches' in window) {
        caches.keys().then(function (keys) {
          keys.forEach(function (key) {
            caches.delete(key);
          });
        });
      }
    });
  </script>

  @yield('page_scripts')
  <script src="/template/assets/js/platform-motion.js" defer></script>
</body>
</html>
