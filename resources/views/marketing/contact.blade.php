@extends('marketing.layouts.template')

@section('title', 'Contact | ERAH Esport')
@section('meta_description', 'Contactez ERAH Esport pour toute question, partenariat, inscription ou informations sur nos compétitions et evenements gaming.')
@section('meta_keywords', 'Contact ERAH Esport, email ERAH, telephone ERAH, partenariat esport, informations club gaming, inscription tournois, evenements gaming')
@section('meta_author', 'ERAH Esport')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
@verbatim
<style>
/* Bandeau cookies stylé */
#cookie-banner {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  max-width: 480px;
  background: rgba(0, 0, 0, 0.9);
  color: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  z-index: 10000;
  font-family: Arial, sans-serif;
  font-size: 14px;
  text-align: center;
  opacity: 0;
  animation: fadeIn 0.6s forwards;
}
@keyframes fadeIn { to { opacity: 1; } }
#cookie-banner button {
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s, background 0.2s;
}
#cookie-banner button#accept-cookies { background: #4CAF50; color: #fff; }
#cookie-banner button#accept-cookies:hover { transform: scale(1.05); background: #45a049; }
#cookie-banner button#reject-cookies { background: #f44336; color: #fff; }
#cookie-banner button#reject-cookies:hover { transform: scale(1.05); background: #d7372a; }

.tt-contact-note {
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 14px;
  padding: 16px 18px;
  background: rgba(255,255,255,.03);
}

.tt-contact-note h6 {
  margin-bottom: 8px;
}

.tt-contact-note ul {
  margin: 0;
  padding-left: 18px;
  display: grid;
  gap: 6px;
}

.tt-field-error {
  margin: 8px 0 0;
  color: #ffbcbc;
  font-size: 13px;
  line-height: 1.4;
}
@media (max-width: 1024px) {
  .tt-contact-info-inner {
    padding-left: 0;
  }

  .tt-contact-note {
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
  }
}
@media (max-width: 500px) {
  .tt-contact-note {
    max-width: none;
  }

  #cookie-banner div { display: flex; flex-direction: column; gap: 8px; width: 100%; }
  #cookie-banner div button { width: 100%; }
}
</style>
@endverbatim
@endsection

@section('content')
@php
    $contactCategories = $contactCategories ?? \App\Models\ContactMessage::categoryLabels();
    $contactSubmissionToken = $contactSubmissionToken ?? '';
@endphp
<div id="page-header"
    class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
    <div class="page-header-inner tt-wrap">
        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">Nous sommes la pour vous&nbsp;!</h2>
                <h1 class="ph-caption-title">Contact</h1>
                <div class="ph-caption-description max-width-700">
                    Vous etes au bon endroit&nbsp;!
                </div>
            </div>
        </div>
    </div>

    <div class="page-header-inner ph-mask">
        <div class="ph-mask-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Tentez votre chance</h2>
                    <h1 class="ph-caption-title">Postuler</h1>
                    <div class="ph-caption-description max-width-700">
                        Vous souhaitez rejoindre notre equipe ? Contactez-nous des maintenant.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ph-social">
        <ul>
            <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
            <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
            <li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
        </ul>
    </div>

    <div class="tt-scroll-down">
        <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
            <div class="tt-scrd-icon"></div>
            <svg viewBox="0 0 500 500">
                <defs>
                    <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                </defs>
                <text dy="30">
                    <textPath xlink:href="#textcircle">Explorez Defiez Brillez Soutenez ERAH -</textPath>
                </text>
            </svg>
        </a>
    </div>
</div>

<div id="tt-page-content">
    <div class="tt-section padding-top-40 padding-bottom-xlg-120">
        <div class="tt-section-inner tt-wrap">
            <div class="tt-row tt-xl-row-reverse">
                <div class="tt-col-xl-5">
                    <div class="tt-contact-info margin-bottom-80">
                        <div class="tt-big-arrow tt-ba-angle-bottom-left tt-anim-fadeinup">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                            </svg>
                        </div>

                        <div class="tt-contact-info-inner">
                            <div class="margin-bottom-50 tt-anim-fadeinup">
                                <h6>Postulez</h6>
                                <p>Vous voulez postuler ou nous contacter ? Remplissez le formulaire avec un maximum d'informations pour une réponse rapide.</p>
                            </div>

                            <div class="tt-contact-détails margin-bottom-50 tt-anim-fadeinup">
                                <h6>Details</h6>
                                <ul>
                                    <li>
                                        <span class="tt-cd-icon"><i class="fas fa-map-marker-alt"></i></span>
                                        <a href="https://maps.app.goo.gl/MTiizsoAEUrp7NpZ6" class="tt-link" target="_blank" rel="noopener">Mende, 48000</a>
                                    </li>
                                    <li>
                                        <span class="tt-cd-icon"><i class="fas fa-phone"></i></span>
                                        <a href="tel:+33649425578" class="tt-link">+(33) 06 49 42 55 78</a>
                                    </li>
                                    <li>
                                        <span class="tt-cd-icon"><i class="fas fa-envelope"></i></span>
                                        <a href="mailto:erah.association&#64;gmail.com" class="tt-link">erah.association&#64;gmail.com</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tt-social-buttons margin-bottom-50 tt-anim-fadeinup">
                                <h6>Reseaux</h6>
                                <ul>
                                    <li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
                                    <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
                                    <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a></li>
                                    <li><a href="https://www.linkedin.com/company/erah-association/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>

                            <div class="tt-contact-note tt-anim-fadeinup">
                                <h6>Pourquoi nous contacter ?</h6>
                                <ul>
                                    <li>Reponse rapide sur vos questions club, support et activites.</li>
                                    <li>Demandes partenariat, evenement, LAN ou intervention.</li>
                                    <li>Suggestions pour ameliorer la plateforme et la communaute.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tt-col-xl-7">
                    <form id="tt-contact-form" class="tt-form tt-form-creative tt-form-lg" name="contact"
                        method="POST" action="{{ route('marketing.contact.submit') }}">
                        @csrf

                        <div class="tt-hidden-honeypot" aria-hidden="true">
                            <label for="website">Leave this field empty</label>
                            <input id="website" type="text" name="website" tabindex="-1" autocomplète="off" value="{{ old('website') }}">
                        </div>
                        <input type="hidden" name="submission_token" value="{{ $contactSubmissionToken }}">

                        <div id="tt-contact-form-messages" role="alert">
                            <div class="tt-cfm-inner"></div>
                            <div class="tt-cfm-close hide-cursor"><i class="fa-solid fa-xmark"></i></div>
                        </div>

                        <div class="tt-contact-form-inner">
                            <div class="tt-form-group tt-anim-fadeinup">
                                <label>Votre pseudo/prenom <span class="required">*</span></label>
                                <input class="tt-form-control" type="text" name="name"
                                    placeholder="Pseudo ou prenom" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="tt-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tt-form-group tt-anim-fadeinup">
                                <label>Votre email <span class="required">*</span></label>
                                <input class="tt-form-control" type="email" name="email"
                                    placeholder="example@gmail.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <p class="tt-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tt-form-group tt-anim-fadeinup">
                                <label>Motif de contact</label>
                                <select class="tt-form-control" name="category">
                                    <option value="">Selectionner une categorie</option>
                                    @foreach($contactCategories as $categoryKey => $categoryLabel)
                                        <option value="{{ $categoryKey }}" @selected(old('category') === $categoryKey)>{{ $categoryLabel }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="tt-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tt-form-group tt-anim-fadeinup">
                                <label>Sujet <span class="required">*</span></label>
                                <input class="tt-form-control" type="text" name="subject"
                                    placeholder="Objet de votre demande" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <p class="tt-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tt-form-group tt-anim-fadeinup">
                                <label>Votre message <span class="required">*</span></label>
                                <textarea class="tt-form-control" rows="5" name="message"
                                    placeholder="En quoi pouvons-nous vous aider ?" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="tt-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tt-anim-fadeinup">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Envoyer">Envoyer</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
@verbatim
<script src="/template/assets/vendor/jquery/jquery.min.js" defer></script>
<script src="/template/assets/vendor/gsap/gsap.min.js" defer></script>
<script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js" defer></script>
<script src="/template/assets/vendor/gsap/ScrollTrigger.min.js" defer></script>
<script src="/template/assets/vendor/lenis.min.js" defer></script>
<script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/isotope/isotope.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js" defer></script>
<script src="/template/assets/vendor/fancybox/js/fancybox.umd.js" defer></script>
<script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js" defer></script>
<script src="/template/assets/js/theme.js" defer></script>
<script src="/template/assets/js/cookies.js" defer></script>
@endverbatim
@endsection
