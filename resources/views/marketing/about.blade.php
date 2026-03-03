@extends('marketing.layouts.template')


@section('title', 'Qui sommes-nous ? | ERAH Esport')

@section('meta_description', 'Découvrez ERAH Esport, un club de gaming et d’esport basé à Mende en Lozère, engagé dans la compétition, les événements et le développement de la communauté gaming.')

@section('meta_keywords', 'ERAH Esport, club esport Lozère, gaming Mende, équipe esport, association gaming, compétitions esport, événements gaming, communauté esport')

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
							<h2 class="ph-caption-subtitle">ERAH ESPORT</h2>
							<h1 class="ph-caption-title">Notre Histoire</h1>
							<div class="ph-caption-description max-width-700">
								Plus qu'un club,<br> nous sommes une famille
							</div>
						</div>
					</div>

				</div>

				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h2 class="ph-caption-subtitle">ERAH ESPORT</h2>
								<h1 class="ph-caption-title">Notre Histoire</h1>
								<div class="ph-caption-description max-width-700">
									Plus qu'un club,<br> nous sommes une famille
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


				<div class="tt-section no-padding-bottom padding-bottom-xlg-40">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row tt-lg-row-reverse">
							<div class="tt-col-lg-6 margin-bottom-20">


<div class="tt-video ttv-portrait ttv-grayscale">
  <video playsinline muted autoplay loop preload="metadata"
         poster="/template/assets/img/galerie/Hoplan-2025-1.webp"
         class="tt-anim-zoomin">
    <source src="/template/assets/vids/Trailer site.mp4"
            type="video/mp4">
    <source src="/template/assets/vids/Trailer site.webm"
            type="video/webm">
  </video>
</div>



							</div>

							<div class="tt-col-lg-1">

							</div>

							<div class="tt-col-lg-5">

								<h2 class="tt-font-alter tt-anim-fadeinup">Yop !</h2>
								<div class="text-lg tt-anim-fadeinup">
									<p>ERAH Esport est fier de représenter la Lozère dans le milieu professionnel de la Ligue VCL de Valorant. Basé à Mende, notre club incarne la passion et l'ambition, en combinant compétition de haut niveau et développement de talents locaux.</p>
									<p>Nous participons à des compétitions officielles, tout en cultivant une équipe professionnelle pour former la prochaine génération de joueurs d'esport, avec une approche éducative de la pratique de l'esport et du jeu vidéo, aidée par une équipe médicale et des coaches & managers pour accompagner les joueurs.</p>
									<p>Notre objectif&nbsp;: promouvoir la Lozère sur la scène nationale et internationale, en bâtissant une structure stable et humaine.</p>

									<div class="tt-big-round-ptn margin-top-20 margin-left-xlg-10-p">
										<a href="https://x.com/ErahEsport"
											class="tt-big-round-ptn-holder tt-magnetic-item" target="_blank"
											rel="noopener">
											<div class="tt-big-round-ptn-inner">Nous<br> suivre</div>
										</a>
									</div>


								</div>

							</div>
						</div>

					</div>
				</div>


				<br>
				<br>

				<div class="tt-section full-height-vh padding-top-120 padding-bottom-120">

					<div class="tt-section-background tt-sbg-image tt-sbg-cover-1">
						<img src="/template/assets/img/mende/mende-3.jpg" class="tt-image-parallax tt-anim-zoomin" loading="lazy"
							alt="Image">
					</div>



					<div class="tt-section-inner tt-wrap text-center">

						<div class="tt-heading tt-heading-xlg tt-heading-center">
							<h3 class="tt-heading-subtitle tt-text-reveal">Notre vision</h3>
							<h2 class="tt-heading-title tt-text-reveal">Une aventure esportive<br> ancrée en Lozère</h2>
						</div>

						<div class="margin-top-30">
							<a href="https://discord.gg/gytq5QEyRa"
								class="tt-btn tt-btn-outline tt-anim-fadeinup tt-magnetic-item" target="_blank"
								rel="noopener">
								<span data-hover="Rejoindre le Discord">Rejoindre le Discord</span>
							</a>

							<a href="/contact" class="tt-btn tt-btn-secondary tt-anim-fadeinup tt-magnetic-item">
								<span data-hover="Postuler chez ERAH">Postuler chez ERAH</span>
							</a>
						</div>

					</div>

				</div>

				<div class="tt-section no-padding-bottom padding-bottom-xlg-80">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row">
							<div class="tt-col-xl-8">

								<div class="tt-heading tt-heading-xxxlg">
									<h3 class="tt-heading-subtitle tt-text-reveal">ERAH Esport</h3>
									<h2 class="tt-heading-title tt-text-reveal">Nos valeurs</h2>
								</div>


								<div
									class="tt-text-uppercase max-width-400 margin-left-xlg-10-p text-pretty tt-text-reveal">
									Ce sont ces valeurs qui nous définissent et nous guident dans notre évolution&nbsp;!
								</div>

							</div>

							<div class="tt-col-xl-4 tt-align-self-end margin-top-40">

								<div class="tt-big-arrow tt-ba-angle-bottom-left tt-anim-fadeinup">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path
											d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
										</path>
									</svg>
								</div>


							</div>
						</div>

					</div>
				</div>


				<div class="tt-section">
					<div class="tt-section-inner">

						<div class="tt-horizontal-accordion tt-hac-alter-hover tt-anim-fadeinup">

							<div class="tt-hac-item cursor-alter">
								<div class="tt-hac-item-count"></div>
								<div class="tt-hac-item-inner">
									<div class="tt-hac-item-content">
										<div class="tt-haci-content-top">
											<h2 class="tt-haci-title">Ambition</h2>
											<div class="tt-haci-description">
												ERAH vise l’excellence en compétition, en formation et dans ses projets.
												Toujours plus haut.
											</div>
										</div>


									</div>
								</div>
							</div>

							<div class="tt-hac-item cursor-alter">
								<div class="tt-hac-item-count"></div>
								<div class="tt-hac-item-inner">
									<div class="tt-hac-item-content">
										<div class="tt-haci-content-top">
											<h2 class="tt-haci-title">Humanité</h2>
											<div class="tt-haci-description">
												Chez ERAH, l’humain passe avant la performance. Bienveillance, écoute et
												respect guident notre club.
											</div>
										</div>

									</div>
								</div>
							</div>

							<div class="tt-hac-item cursor-alter">
								<div class="tt-hac-item-count"></div>
								<div class="tt-hac-item-inner">
									<div class="tt-hac-item-content">
										<div class="tt-haci-content-top">
											<h2 class="tt-haci-title">Innovation</h2>
											<div class="tt-haci-description">
												ERAH Esport se distingue par des projets uniques, des formats inédits et
												une approche différente de l’esport.
											</div>
										</div>

									</div>
								</div>
							</div>

							<div class="tt-hac-item cursor-alter">
								<div class="tt-hac-item-count"></div>
								<div class="tt-hac-item-inner">
									<div class="tt-hac-item-content">
										<div class="tt-haci-content-top">
											<h2 class="tt-haci-title">Engagement</h2>
											<div class="tt-haci-description">
												ERAH s’investit pleinement dans la préparation des compétitions,
												l’organisation d’événements et la gestion du club.
											</div>
										</div>

									</div>
								</div>
							</div>

							<div class="tt-hac-item cursor-alter">
								<div class="tt-hac-item-count"></div>
								<div class="tt-hac-item-inner">
									<div class="tt-hac-item-content">
										<div class="tt-haci-content-top">
											<h2 class="tt-haci-title">Fierté</h2>
											<div class="tt-haci-description">
												Représenter Mende et la Lozère est dans notre ADN. Fiers d’être locaux,
												nous rayonnons aussi en France et à l’international.
											</div>
										</div>

										<div class="tt-haci-content-bottom">

										</div>
									</div>
								</div>
							</div>


						</div>


					</div>
				</div>

				<div class="tt-section padding-top-xlg-120">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row margin-bottom-40">
							<div class="tt-col-xl-8">


								<div class="tt-heading tt-heading-xxxlg no-margin">
									<h3 class="tt-heading-subtitle tt-text-reveal">Recrutement</h3>
									<h2 class="tt-heading-title tt-text-reveal">Rejoins<br> l'aventure</h2>
								</div>


							</div>

							<div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

								<div
									class="max-width-600 margin-bottom-10 tt-text-uppercase text-pretty tt-text-reveal">
									Tu veux représenter ERAH en compétition, t’investir dans l’esport ou rejoindre notre
									staff&nbsp;?<br> Nous attendons ta candidature&nbsp;!
								</div>


								<div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
									<a href="/contact" class="tt-big-round-ptn-holder tt-magnetic-item">
										<div class="tt-big-round-ptn-inner">Postuler<br> maintenant</div>
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
