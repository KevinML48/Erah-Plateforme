@extends('marketing.layouts.template')


@section('title', 'Nos événements | ERAH Esport')

@section('meta_description', 'Découvrez les événements ERAH Esport : tournois, LAN, compétitions et animations gaming pour joueurs et passionnés en Lozère et au-delà.')

@section('meta_keywords', 'Événements ERAH Esport, tournois gaming, LAN party, compétitions esport, animations gaming, activités club esport, événements Lozère, gaming Occitanie')

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
  font-family: 'Arial', sans-serif;
  font-size: 14px;
  text-align: center;
  opacity: 0;
  animation: fadeIn 0.6s forwards;
}

/* Animation */
@keyframes fadeIn {
  to { opacity: 1; }
}

/* Boutons stylés */
#cookie-banner button {
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s, background 0.2s;
}

#cookie-banner button#accept-cookies {
  background: #4CAF50;
  color: #fff;
}

#cookie-banner button#accept-cookies:hover {
  transform: scale(1.05);
  background: #45a049;
}

#cookie-banner button#reject-cookies {
  background: #f44336;
  color: #fff;
}

#cookie-banner button#reject-cookies:hover {
  transform: scale(1.05);
  background: #d7372a;
}

@media (max-width: 500px) {
  #cookie-banner div {
    display: flex;
    flex-direction: column;
    gap: 8px; /* espace vertical entre les boutons */
    width: 100%;
  }
  #cookie-banner div button {
    width: 100%; /* boutons plein largeur sur mobile */
  }
}
</style>

@endverbatim
@endsection


@section('content')
@verbatim

<div id="page-header"
				class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">



				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h2 class="ph-caption-subtitle">Nos Moments</h2>
							<h1 class="ph-caption-title">Événements</h1>
							<div class="ph-caption-description max-width-700">
								Retrouvez ici tous les événements qui rythment notre aventure.
							</div>
						</div>
					</div>

				</div>

				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h2 class="ph-caption-subtitle">Highlights</h2>
								<h1 class="ph-caption-title">Nos Événements</h1>
								<div class="ph-caption-description max-width-700">
									Un aperçu des temps forts qui marquent notre parcours et reflètent notre passion.
								</div>
							</div>
						</div>

					</div>
				</div>




				<div class="ph-social">
					<ul>
						<li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank"
								rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
						<li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank"
								rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
						<li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank"
								rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
						<li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank"
								rel="noopener"><i class="fa-brands fa-discord"></i></a></li>

					</ul>
				</div>


				<div class="tt-scroll-down">

					<a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
						<div class="tt-scrd-icon"></div>
						<svg viewBox="0 0 500 500">
							<defs>
								<path
									d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250"
									id="textcircle"></path>
							</defs>
							<text dy="30">

								<textPath xlink:href="#textcircle">Explorez Défiez Brillez Soutenez ERAH -</textPath>
							</text>
						</svg>
					</a>
				</div>


			</div>


			<div id="tt-page-content">


				<div class="tt-section">
					<div class="tt-section-inner">

						<div class="tt-sticky-portfolio">

							<a href="#" class="tt-stp-item" data-cursor="ActeurSport 2025">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/acteursport-2025-2.jpg" class="tt-anim-zoomin"
											loading="lazy" alt="Événement ActeurSport 2025">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">ActeurSport 2025</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Événement</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Lozère Nouvelle">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/lozere-nouvelle-1.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Événement Lozère Nouvelle">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Lozère Nouvelle</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Événement</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Lozère Nouvelle">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/lozere-nouvelle-2.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Événement Lozère Nouvelle">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Lozère Nouvelle</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Événement</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Intervention">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/intervention-2.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Intervention éducative">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Intervention</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Animation</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Atelier & Partage">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/mission-locale-1.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Intervention éducative">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Atelier & Partage</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Éducation</div>
										</div>
									</div>
								</div>
							</a>

							<a href="#" class="tt-stp-item" data-cursor="Conférence">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/chirac-conference-3.jpg" class="tt-anim-zoomin"
											loading="lazy" alt="Mission Locale">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Chirac</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Conférence</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Interview Hoplan 2025">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/interview-GA-2025.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Interview Hoplan 2025">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Interview Hoplan 2025</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Médias</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Mission Locale">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/mission-locale-3.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Mission Locale">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Mission Locale</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Engagement</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Partenariat">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/partenariat-1.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Partenariat">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Partenariat</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Collaboration</div>
										</div>
									</div>
								</div>
							</a>


							<a href="#" class="tt-stp-item" data-cursor="Mission Locale">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/mission-locale-4.webp" class="tt-anim-zoomin"
											loading="lazy" alt="Mission Locale">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Mission Locale</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Engagement</div>
										</div>
									</div>
								</div>
							</a>

							<a href="#" class="tt-stp-item" data-cursor="Conférence">
								<div class="tt-stp-item-inner">
									<div class="tt-stp-item-image cover-opacity-2">
										<img src="/template/assets/img/galerie/chirac-conference-2.jpg" class="tt-anim-zoomin"
											loading="lazy" alt="Mission Locale">
									</div>
									<div class="tt-stp-item-caption">
										<h2 class="tt-stp-item-title">Chirac</h2>
										<div class="tt-stp-item-categories">
											<div class="tt-stp-item-category">Conférence</div>
										</div>
									</div>
								</div>
							</a>




						</div>


					</div>
				</div>



				<div class="tt-section padding-bottom-xlg-120">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row margin-bottom-40">
							<div class="tt-col-xl-8">


								<div class="tt-heading tt-heading-xxxlg no-margin">
									<h3 class="tt-heading-subtitle tt-text-reveal">Contact</h3>
									<h2 class="tt-heading-title tt-text-reveal">Travaillons<br> Ensemble</h2>
								</div>


							</div>

							<div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

								<div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
									Envie de collaborer sur un nouveau projet&nbsp;? Partagez-nous vos idées<br> et
									discutons-en&nbsp;!
								</div>


								<div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
									<a href="/contact" class="tt-big-round-ptn-holder tt-magnetic-item">
										<div class="tt-big-round-ptn-inner">Entrons<br> en Contact</div>
									</a>
								</div>


							</div>
						</div>

					</div>
				</div>


@endverbatim
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
