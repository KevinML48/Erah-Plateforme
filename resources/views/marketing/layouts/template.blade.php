<!DOCTYPE html>
<html lang="fr">
<head>
  <title>@yield('title', 'ERAH Esport')</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

  <link rel="preload" href="/template/assets/vendor/fontawesome/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/fancybox/css/fancybox.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="/template/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/template/assets/vendor/fancybox/css/fancybox.css">
    <link rel="stylesheet" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css">
  </noscript>

  <x-google-analytics />

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
    body.tt-lightmode-on,
    body.tt-lightmode-on #body-inner,
    body.tt-lightmode-on #tt-content-wrap {
      color: #0f172a;
      background:
        radial-gradient(circle at top right, rgba(216, 7, 7, .08), transparent 26%),
        linear-gradient(180deg, #fafaf9 0%, #f5f7fb 46%, #eef2f7 100%);
    }
    body.tt-lightmode-on .tt-alert {
      border-color: rgba(15, 23, 42, .12);
      box-shadow: 0 18px 36px rgba(15, 23, 42, .10);
      background: rgba(255, 255, 255, .94);
      color: #0f172a;
    }
    body.tt-lightmode-on .tt-alert-success {
      background: linear-gradient(180deg, rgba(222, 247, 233, .98), rgba(205, 236, 219, .96));
      color: #14532d;
    }
    body.tt-lightmode-on .tt-alert-danger {
      background: linear-gradient(180deg, rgba(255, 235, 235, .98), rgba(255, 221, 221, .96));
      color: #991b1b;
    }
    body.tt-lightmode-on .tt-form-control,
    body.tt-lightmode-on input.tt-form-control,
    body.tt-lightmode-on select.tt-form-control,
    body.tt-lightmode-on textarea.tt-form-control {
      border-color: rgba(148, 163, 184, .34);
      background: rgba(255, 255, 255, .92);
      color: #0f172a;
      box-shadow: 0 10px 26px rgba(148, 163, 184, .12);
    }
    body.tt-lightmode-on .tt-form-control::placeholder {
      color: rgba(71, 85, 105, .66);
    }
    body.tt-lightmode-on .tt-form-control:focus,
    body.tt-lightmode-on input.tt-form-control:focus,
    body.tt-lightmode-on select.tt-form-control:focus,
    body.tt-lightmode-on textarea.tt-form-control:focus {
      border-color: rgba(216, 7, 7, .34);
      box-shadow: 0 0 0 4px rgba(216, 7, 7, .12), 0 14px 32px rgba(148, 163, 184, .16);
    }
    body.tt-lightmode-on .tt-btn-primary,
    body.tt-lightmode-on .tt-btn-secondary,
    body.tt-lightmode-on .tt-btn-outline {
      border: 1px solid rgba(148, 163, 184, .26);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background-color .18s ease;
    }
    body.tt-lightmode-on .tt-btn-primary {
      background: linear-gradient(135deg, #d80707, #8f0b0b);
      border-color: rgba(216, 7, 7, .78);
      box-shadow: 0 18px 34px rgba(143, 11, 11, .22);
    }
    body.tt-lightmode-on .tt-btn-primary > *,
    body.tt-lightmode-on .tt-btn-primary > *::after {
      color: #fff;
    }
    body.tt-lightmode-on .tt-btn-primary:hover,
    body.tt-lightmode-on .tt-btn-primary:focus-visible {
      border-color: rgba(216, 7, 7, .98);
      box-shadow: 0 22px 40px rgba(143, 11, 11, .28);
      transform: translateY(-1px);
    }
    body.tt-lightmode-on .tt-btn-outline {
      border-color: rgba(148, 163, 184, .34);
      background: rgba(255, 255, 255, .88);
      box-shadow: 0 12px 28px rgba(148, 163, 184, .12);
    }
    body.tt-lightmode-on .tt-btn-outline > *,
    body.tt-lightmode-on .tt-btn-outline > *::after {
      color: #0f172a;
    }
    body.tt-lightmode-on .tt-btn-secondary {
      background: rgba(255, 255, 255, .96);
      border-color: rgba(148, 163, 184, .3);
      box-shadow: 0 14px 30px rgba(15, 23, 42, .12);
    }
    body.tt-lightmode-on .tt-btn-secondary > *,
    body.tt-lightmode-on .tt-btn-secondary > *::after {
      color: #0f172a;
    }
    body.tt-lightmode-on .tt-btn-secondary:hover,
    body.tt-lightmode-on .tt-btn-secondary:focus-visible,
    body.tt-lightmode-on .tt-btn-outline:hover,
    body.tt-lightmode-on .tt-btn-outline:focus-visible {
      border-color: rgba(15, 23, 42, .42);
      box-shadow: 0 18px 34px rgba(148, 163, 184, .18);
      transform: translateY(-1px);
    }
    body.tt-lightmode-on .tt-btn-outline:hover,
    body.tt-lightmode-on .tt-btn-outline:focus-visible {
      background: #0f172a;
    }
    body.tt-lightmode-on .tt-btn-outline:hover > *,
    body.tt-lightmode-on .tt-btn-outline:hover > *::after,
    body.tt-lightmode-on .tt-btn-outline:focus-visible > *,
    body.tt-lightmode-on .tt-btn-outline:focus-visible > *::after {
      color: #fff;
    }
    body.tt-lightmode-on .tt-heading-title,
    body.tt-lightmode-on .tt-heading-title a,
    body.tt-lightmode-on .tt-heading-subtitle,
    body.tt-lightmode-on .tt-heading-description,
    body.tt-lightmode-on .text-gray,
    body.tt-lightmode-on .text-muted {
      color: #0f172a;
    }
    body.tt-lightmode-on .tt-heading-subtitle {
      opacity: .72;
    }
    body.tt-lightmode-on #page-header {
      position: relative;
    }
    body.tt-lightmode-on #page-header .page-header-inner.ph-mask {
      display: none;
    }
    body.tt-lightmode-on #page-header .ph-caption-inner {
      padding: 24px 28px;
      border: 1px solid rgba(148, 163, 184, .22);
      border-radius: 28px;
      background: linear-gradient(180deg, rgba(255, 255, 255, .82), rgba(248, 250, 252, .68));
      box-shadow: 0 20px 44px rgba(148, 163, 184, .18);
      backdrop-filter: blur(14px);
    }
    body.tt-lightmode-on #page-header .ph-video::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(247, 250, 252, .64), rgba(236, 242, 248, .74));
      pointer-events: none;
    }
    body.tt-lightmode-on #page-header .ph-caption-title,
    body.tt-lightmode-on #page-header .ph-caption-title a {
      color: #0f172a;
      text-shadow: none;
    }
    body.tt-lightmode-on #page-header .ph-caption-subtitle {
      color: rgba(51, 65, 85, .82);
    }
    body.tt-lightmode-on #page-header .ph-caption-description {
      color: rgba(51, 65, 85, .88);
    }
    body.tt-lightmode-on #tt-header .tt-header-inner {
      background: rgba(255, 255, 255, .88);
      border-bottom: 1px solid rgba(148, 163, 184, .18);
      box-shadow: 0 16px 38px rgba(148, 163, 184, .16);
      backdrop-filter: blur(14px);
    }
    body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a,
    body.tt-lightmode-on #tt-header .tt-submenu-trigger > a,
    body.tt-lightmode-on #tt-header .tt-m-menu-toggle-btn-text span,
    body.tt-lightmode-on #tt-header .tt-logo a {
      color: #0f172a;
    }
    body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a:hover,
    body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a:focus-visible,
    body.tt-lightmode-on #tt-header .tt-submenu-trigger > a:hover,
    body.tt-lightmode-on #tt-header .tt-submenu-trigger > a:focus-visible {
      color: #d80707;
    }
    body.tt-lightmode-on #tt-header .tt-submenu {
      border: 1px solid rgba(148, 163, 184, .18);
      background: rgba(255, 255, 255, .96);
      box-shadow: 0 24px 48px rgba(148, 163, 184, .18);
      backdrop-filter: blur(14px);
    }
    body.tt-lightmode-on #tt-header .tt-submenu-list {
      background: transparent;
    }
    body.tt-lightmode-on #tt-header .tt-submenu-list > li > a {
      color: rgba(15, 23, 42, .92);
      -webkit-text-fill-color: rgba(15, 23, 42, .92);
      border-radius: 10px;
    }
    body.tt-lightmode-on #tt-header .tt-submenu-list > li > a:hover,
    body.tt-lightmode-on #tt-header .tt-submenu-list > li > a:focus-visible,
    body.tt-lightmode-on #tt-header .tt-submenu-list > li.active > a,
    body.tt-lightmode-on #tt-header .tt-submenu-list > li > .tt-submenu-trigger:hover a,
    body.tt-lightmode-on #tt-header .tt-submenu-list > li.active > .tt-submenu-trigger a {
      color: #d80707;
      -webkit-text-fill-color: #d80707;
      background: rgba(216, 7, 7, .08);
    }
    body.tt-lightmode-on #tt-header .tt-style-switch-inner {
      border: 1px solid rgba(148, 163, 184, .24);
      background: rgba(255, 255, 255, .94);
      box-shadow: 0 12px 28px rgba(148, 163, 184, .16);
    }
    body.tt-lightmode-on #tt-header .tt-stsw-light,
    body.tt-lightmode-on #tt-header .tt-stsw-dark {
      color: #0f172a;
    }
    body.tt-lightmode-on #tt-header .tt-main-menu-holder {
      background: rgba(255, 255, 255, .98);
    }
    #tt-header .tt-main-menu-holder {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    #tt-header .tt-main-menu-holder::-webkit-scrollbar {
      width: 0;
      height: 0;
      display: none;
    }
    @media (min-width: 1025px) {
      #tt-header .tt-header-inner {
        width: min(1320px, calc(100% - 48px));
        margin: 22px auto 0;
        padding: 14px 18px 14px 14px;
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 18px;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(8, 12, 18, .78), rgba(9, 13, 19, .7));
        box-shadow: 0 22px 48px rgba(0, 0, 0, .24);
        backdrop-filter: blur(10px);
      }
      #tt-header .tt-header-col-left,
      #tt-header .tt-header-col-center,
      #tt-header .tt-header-col-right {
        display: flex;
        align-items: center;
        min-width: 0;
      }
      #tt-header .tt-header-col-center {
        justify-content: center;
      }
      #tt-header .tt-header-col-right {
        justify-content: flex-end;
        gap: 12px;
      }
      #tt-header .tt-logo a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 58px;
        padding: 8px 14px;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .04);
      }
      #tt-header .tt-logo img {
        max-height: 34px;
      }
      #tt-header .tt-main-menu {
        display: block;
        width: 100%;
      }
      #tt-header .tt-main-menu-holder {
        overflow: visible;
        margin-right: 0;
        background: transparent;
      }
      #tt-header .tt-main-menu-inner {
        display: block;
        height: auto;
        padding: 0;
      }
      #tt-header .tt-main-menu-content {
        display: block;
      }
      #tt-header .tt-main-menu-list {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 0;
        padding: 8px 10px;
        list-style: none;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .04);
      }
      #tt-header .tt-main-menu-list > li {
        position: relative;
        margin: 0;
      }
      #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more {
        margin-left: 8px;
        padding-left: 12px;
      }
      #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more::before {
        content: "";
        position: absolute;
        left: 0;
        top: 11px;
        bottom: 11px;
        width: 1px;
        background: rgba(255, 255, 255, .1);
      }
      #tt-header .tt-main-menu-list > li > a,
      #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a {
        min-height: 40px;
        padding: 0 14px;
        border: 0;
        border-radius: 12px;
        background: transparent;
        box-shadow: none;
        color: rgba(248, 250, 252, .92) !important;
        -webkit-text-fill-color: rgba(248, 250, 252, .92);
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        letter-spacing: .11em;
        text-transform: uppercase;
        white-space: nowrap;
      }
      #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more > a,
      #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more > .tt-submenu-trigger > a {
        color: rgba(226, 232, 240, .72) !important;
        -webkit-text-fill-color: rgba(226, 232, 240, .72);
        font-size: 12px;
        font-weight: 500;
      }
      #tt-header .tt-main-menu-list > li > a:hover,
      #tt-header .tt-main-menu-list > li > a:focus-visible,
      #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a:hover,
      #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a:focus-visible,
      #tt-header .tt-main-menu-list > li.tt-submenu-open > .tt-submenu-trigger > a {
        background: rgba(255, 255, 255, .08);
        color: #fff !important;
        -webkit-text-fill-color: #fff;
      }
      #tt-header .tt-main-menu-list > li > a::before,
      #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a::before {
        display: none;
      }
      #tt-header .tt-submenu {
        padding-top: 14px;
      }
      #tt-header .tt-submenu-list {
        min-width: 220px;
        padding: 14px;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, .1);
        background: rgba(8, 12, 18, .94);
        box-shadow: 0 22px 46px rgba(0, 0, 0, .24);
        backdrop-filter: blur(10px);
      }
      #tt-header .tt-submenu-list > li > a {
        min-height: 40px;
        padding: 10px 12px;
        border-radius: 12px;
        color: rgba(241, 245, 249, .9);
        -webkit-text-fill-color: rgba(241, 245, 249, .9);
        font-size: 13px;
        letter-spacing: .04em;
      }
      #tt-header .tt-submenu-list > li > a:hover,
      #tt-header .tt-submenu-list > li > a:focus-visible,
      #tt-header .tt-submenu-list > li.active > a {
        color: #fff;
        -webkit-text-fill-color: #fff;
        background: rgba(255, 255, 255, .08);
      }
      #tt-header .tt-header-account-cluster {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-left: 16px;
        border-left: 1px solid rgba(255, 255, 255, .08);
      }
      #tt-header .tt-header-account-btn {
        min-height: 40px;
        padding: 0 14px;
      }
      #tt-header .tt-header-account-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        min-height: 40px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .1);
        background: rgba(255, 255, 255, .04);
        color: rgba(248, 250, 252, .9);
      }
      #tt-header #tt-m-menu-toggle-btn-wrap {
        display: none;
      }
      body.tt-lightmode-on #tt-header .tt-header-inner {
        border: 1px solid rgba(148, 163, 184, .22);
        background: linear-gradient(180deg, rgba(255, 255, 255, .95), rgba(248, 250, 252, .9));
        box-shadow: 0 20px 44px rgba(148, 163, 184, .18);
      }
      body.tt-lightmode-on #tt-header .tt-logo a,
      body.tt-lightmode-on #tt-header .tt-main-menu-list,
      body.tt-lightmode-on #tt-header .tt-header-account-icon {
        border-color: rgba(148, 163, 184, .2);
        background: rgba(255, 255, 255, .82);
        box-shadow: 0 10px 22px rgba(148, 163, 184, .1);
      }
      body.tt-lightmode-on #tt-header .tt-header-account-cluster {
        border-left-color: rgba(148, 163, 184, .2);
      }
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more::before {
        background: rgba(148, 163, 184, .2);
      }
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a,
      body.tt-lightmode-on #tt-header .tt-header-account-icon {
        color: #0f172a !important;
        -webkit-text-fill-color: #0f172a;
      }
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more > a,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li.tt-main-menu-secondary-more > .tt-submenu-trigger > a {
        color: rgba(51, 65, 85, .62) !important;
        -webkit-text-fill-color: rgba(51, 65, 85, .62);
      }
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a:hover,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > a:focus-visible,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a:hover,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li > .tt-submenu-trigger > a:focus-visible,
      body.tt-lightmode-on #tt-header .tt-main-menu-list > li.tt-submenu-open > .tt-submenu-trigger > a,
      body.tt-lightmode-on #tt-header .tt-header-account-icon:hover,
      body.tt-lightmode-on #tt-header .tt-header-account-icon:focus-visible {
        color: #0f172a !important;
        -webkit-text-fill-color: #0f172a;
        border-color: rgba(148, 163, 184, .28);
        background: rgba(241, 245, 249, .94);
      }
      body.tt-lightmode-on #tt-header .tt-submenu-list {
        border-color: rgba(148, 163, 184, .2);
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 22px 42px rgba(148, 163, 184, .16);
      }
      body.tt-lightmode-on #tt-header .tt-submenu-list > li > a {
        color: #0f172a;
        -webkit-text-fill-color: #0f172a;
      }
      body.tt-lightmode-on #tt-header .tt-submenu-list > li > a:hover,
      body.tt-lightmode-on #tt-header .tt-submenu-list > li > a:focus-visible,
      body.tt-lightmode-on #tt-header .tt-submenu-list > li.active > a {
        background: rgba(241, 245, 249, .94);
        color: #0f172a;
        -webkit-text-fill-color: #0f172a;
      }
    }
    body.tt-lightmode-on .tt-scroll-down-inner,
    body.tt-lightmode-on .tt-scroll-down text {
      color: #0f172a;
      fill: #0f172a;
    }
    body.tt-lightmode-on #tt-page-content,
    body.tt-lightmode-on #tt-page-content p,
    body.tt-lightmode-on #tt-page-content li,
    body.tt-lightmode-on #tt-page-content label,
    body.tt-lightmode-on #tt-page-content small,
    body.tt-lightmode-on #tt-page-content .tt-form-text,
    body.tt-lightmode-on #tt-page-content .text-muted,
    body.tt-lightmode-on #tt-page-content .text-gray {
      color: rgba(51, 65, 85, .88);
    }
    body.tt-lightmode-on #tt-page-content h1,
    body.tt-lightmode-on #tt-page-content h2,
    body.tt-lightmode-on #tt-page-content h3,
    body.tt-lightmode-on #tt-page-content h4,
    body.tt-lightmode-on #tt-page-content h5,
    body.tt-lightmode-on #tt-page-content h6,
    body.tt-lightmode-on #tt-page-content strong,
    body.tt-lightmode-on #tt-page-content .tt-list li strong,
    body.tt-lightmode-on #tt-page-content .pgi-title,
    body.tt-lightmode-on #tt-page-content .pgi-title a,
    body.tt-lightmode-on #tt-page-content .ttgr-cat-classic-item a,
    body.tt-lightmode-on #tt-page-content table td strong {
      color: #0f172a;
      text-shadow: none;
    }
    body.tt-lightmode-on #tt-page-content .border-top,
    body.tt-lightmode-on #tt-page-content .tt-section.border-top {
      border-color: rgba(148, 163, 184, .18) !important;
    }
    body.tt-lightmode-on .portfolio-grid-item {
      padding: 18px;
      border: 1px solid rgba(148, 163, 184, .24);
      border-radius: 28px;
      background: linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(248, 250, 252, .88));
      box-shadow: 0 24px 54px rgba(148, 163, 184, .18);
      backdrop-filter: blur(14px);
    }
    body.tt-lightmode-on .portfolio-grid-item .pgi-image-holder,
    body.tt-lightmode-on .portfolio-grid-item .pgi-image-inner,
    body.tt-lightmode-on .portfolio-grid-item .pgi-image,
    body.tt-lightmode-on .portfolio-grid-item .pgi-image img {
      border-radius: 22px;
    }
    body.tt-lightmode-on .portfolio-grid-item .pgi-image-holder {
      overflow: hidden;
      box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
    }
    body.tt-lightmode-on .portfolio-grid-item .pgi-caption {
      margin-top: 18px;
    }
    body.tt-lightmode-on .portfolio-grid-item .pgi-category,
    body.tt-lightmode-on .portfolio-grid-item .pgi-category a {
      color: rgba(71, 85, 105, .88);
      opacity: 1;
    }
    body.tt-lightmode-on .ttgr-cat-classic-item a {
      border: 1px solid rgba(148, 163, 184, .28);
      border-radius: 999px;
      background: rgba(255, 255, 255, .88);
      box-shadow: 0 12px 26px rgba(148, 163, 184, .12);
    }
    body.tt-lightmode-on .ttgr-cat-classic-item a.active,
    body.tt-lightmode-on .ttgr-cat-classic-item a:hover,
    body.tt-lightmode-on .ttgr-cat-classic-item a:focus-visible {
      border-color: #0f172a;
      background: #0f172a;
      color: #fff;
    }
    body.tt-lightmode-on table {
      border-color: rgba(148, 163, 184, .22);
    }
    body.tt-lightmode-on table th {
      color: rgba(71, 85, 105, .82);
    }
    body.tt-lightmode-on table td {
      color: #0f172a;
    }
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

      if (window.matchMedia('(max-width: 1024px)').matches) {
        return;
      }

      var pageHeader = document.getElementById('page-header');
      if (!pageHeader) {
        return;
      }

      var caption = pageHeader.querySelector('.page-header-inner:not(.ph-mask) .ph-caption');
      var mask = pageHeader.querySelector('.ph-mask');
      if (!caption || !mask) {
        return;
      }

      document.body.classList.add('ph-mask-on');

      var latestX = null;
      var latestY = null;
      var ticking = false;

      var paintMask = function () {
        ticking = false;

        if (latestX === null || latestY === null) {
          return;
        }

        var rect = mask.getBoundingClientRect();
        if (!rect.width || !rect.height) {
          return;
        }

        var xPercent = ((latestX - rect.left) / rect.width) * 100;
        var yPercent = ((latestY - rect.top) / rect.height) * 100;

        mask.style.setProperty('--x', xPercent.toFixed(2) + '%');
        mask.style.setProperty('--y', yPercent.toFixed(2) + '%');
      };

      var scheduleMask = function (clientX, clientY) {
        latestX = clientX;
        latestY = clientY;

        if (ticking) {
          return;
        }

        ticking = true;
        window.requestAnimationFrame(paintMask);
      };

      pageHeader.addEventListener('mousemove', function (event) {
        scheduleMask(event.clientX, event.clientY);
      });

      caption.addEventListener('mouseenter', function () {
        document.body.classList.add('ph-mask-active');
      });

      caption.addEventListener('mouseleave', function () {
        document.body.classList.remove('ph-mask-active');
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
  @include('partials.mission-live-toasts')
  @include('marketing.partials.guided-tour')
</body>
</html>
