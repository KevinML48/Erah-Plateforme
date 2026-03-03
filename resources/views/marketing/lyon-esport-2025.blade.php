@extends('marketing.layouts.template')


@section('title', 'Lyon Esport 2025 | ERAH Esport')

@section('meta_description', 'Revivez la participation d’ERAH Esport à la Lyon Esport 2025 : tournois, performances et moments forts au plus grand festival gaming de France.')

@section('meta_keywords', 'Lyon Esport 2025, ERAH Esport Lyon Esport, tournoi esport Lyon, LAN France 2025, compétition esport, festival gaming France, esport national')

@section('meta_author', 'ERAH Esport')

@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')


@section('page_styles')
@verbatim

<style>
	.tt-anim-zoomin {
		animation: none !important;
		transform: scale(1) !important;
	}

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

<div id="page-header" class="ph-full ph-full-m ph-cap-xlg ph-center ph-image-parallax ph-caption-parallax">

				<div class="ph-image ph-image-cover-2">
					<div class="ph-image-inner">
						<img src="/template/assets/img/galerie/lyon_esport.jpg" alt="Image">
					</div>
				</div>




				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h1 class="ph-caption-title">Lyon Esport<br> 2025</h1>
							<div class="ph-caption-categories">
								<div class="ph-caption-category">Valorant</div>

							</div>
						</div>
					</div>

				</div>

				<div class="ph-share">
					<div class="ph-share-inner">
						<div class="ph-share-trigger">
							<div class="ph-share-text">Share</div>
							<div class="ph-share-icon"><i class="fas fa-share"></i></div>
						</div>
						<div class="ph-share-buttons">
							<ul>
								<li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item"
										target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
								<li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item"
										target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
								<li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank"
										rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
								<li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank"
										rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
							</ul>
						</div>
					</div>
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

								<textPath xlink:href="#textcircle">Scroll to Explore - Scroll to Explore -</textPath>
							</text>
						</svg>
					</a>
				</div>


			</div>

			<div id="tt-page-content">


                            <div class="tt-section padding-top-xlg-130 padding-bottom-xlg-100">
                                <div class="tt-section-inner tt-wrap">

                                    <div class="tt-row">
                                        <div class="tt-col-lg-9">

                                            <div class="tt-anim-fadeinup">
                                                <p>La Lyon Esport 2025 a été une étape majeure pour ERAH Esport. Notre équipe Valorant a brillé lors de ce prestigieux événement, se hissant jusqu'à la Top 7 face à une concurrence nationale de haut niveau. Cette performance illustre le dévouement de nos joueurs, la qualité de notre préparation et notre ambition de nous affirmer parmi les meilleures équipes de France.</p>
                                            </div>

                                        </div>

                                        <div class="tt-col-lg-3">
                                        </div>
                                    </div>

                                </div>
                            </div>



                            <div class="tt-section no-padding">
                                <div class="tt-section-inner tt-wrap">

                                    <div class="tt-sticker">

                                        <div class="tt-row tt-lg-row-reverse">
                                            <div class="tt-col-lg-3 margin-bottom-60">

                                                <div class="tt-sticker-sticky tt-sticky-element">

										<div class="tt-heading tt-heading-lg margin-bottom-40">

											<h2 class="tt-heading-title tt-text-reveal">Info</h2>
										</div>


										<div class="tt-project-info-list tt-anim-fadeinup">
											<ul>
												<li>
													<div class="pi-list-heading">Événements</div>
													<div class="pi-list-cont">Lyon Esport 2025</div>
												</li>
												<li>
													<div class="pi-list-heading">Année</div>
													<div class="pi-list-cont">2025</div>
												</li>
												<li>
													<div class="pi-list-heading">Équipe</div>
													<div class="pi-list-cont">Valorant</div>
												</li>
												<li>
													<div class="pi-list-heading">Plus d’informations</div>
													<div class="pi-list-cont"><a
															href="https://x.com/ErahEsport/status/1987529565648871594?s=20"
															target="_blank" rel="noopener">Twitter<span
																class="pi-list-icon"><i
																	class="fas fa-arrow-right"></i></span></a></div>
												</li>
											</ul>
										</div>


									</div>


								</div>

								<div class="tt-col-lg-9 padding-right-lg-3-p">

                                      <style>
    .video-full-container {
      display: flex;
      justify-content: center;   /* Centre horizontalement */
      align-items: center;       /* Centre verticalement */
      width: 100%;
      height: 100%;
      overflow: hidden;
      position: relative;
    }

    .video-full {
      max-width: 100%;
      max-height: 100%;
      object-fit: cover;         /* Ajuste la vidéo sans déformation */
      border-radius: 10px;       /* Même arrondi que les images */
    }
  </style>

									<div class="tt-sticker-scroller">


										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/galerie/lyon-esport-1.jpg" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="Équipe Valorant">
													<img src="/template/assets/img/galerie/lyon-esport-1.jpg"
														class="tt-anim-zoomin" loading="lazy" alt="Image">
												</a>
												<figcaption>
													Équipe Valorant
												</figcaption>
											</figure>
										</div>


										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/galerie/lyon-esport-3.mp4" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="Victoire">
													<div class="video-full-container">
														<video src="/template/assets/img/galerie/lyon-esport-3.mp4" autoplay loop muted playsinline class="video-full">
</video>

													</div>
												</a>
												<figcaption>
													Victoire
												</figcaption>
											</figure>
										</div>




										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/galerie/lyon-esport-4.jpg"
													class="tt-image-link" data-cursor="View"
													data-fancybox="gallery-746091" data-caption="Qualification Day 2">
													<img src="/template/assets/img/galerie/lyon-esport-4.jpg"
														class="tt-anim-zoomin" loading="lazy" alt="Image">
												</a>
												<figcaption>
                                                    Qualification Day 2
												</figcaption>
											</figure>
										</div>






									</div>
								</div>

							</div>


						</div>
					</div>



					<div class="tt-section padding-bottom-xlg-160">
						<div class="tt-section-inner tt-wrap">

							<div class="text-xlg max-width-1000 tt-text-reveal">
                                La Lyon Esport 2025 a été un moment fort pour ERAH Esport, une expérience mémorable de compétition et de partage de notre passion pour le gaming.
							</div>

							<a href="https://x.com/ErahEsport"
								class="tt-btn tt-btn-outline margin-top-40 tt-anim-fadeinup tt-magnetic-item"
								target="_blank" rel="noopener">
								<span data-hover="Nous soutenir">Nous soutenir</span>
							</a>

						</div>
					</div>


					<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-140 border-top">
						<div class="tt-section-inner tt-wrap">

							<div class="tt-next-project">
								<div class="tt-row">
									<div class="tt-col-md-7">

										<div class="tt-next-project-caption">
											<div class="tt-np-top tt-anim-fadeinup">
												<a href="#erah" class="tt-btn tt-btn-link">
													<span data-hover="Notre équipe">Notre équipe</span>
													<span class="tt-btn-icon"><i class="fas fa-arrow-right"></i></span>
												</a>
											</div>
											<h3 class="tt-np-title tt-text-reveal">Next</h3>
										</div>

									</div>

									<div class="tt-col-md-5 tt-align-self-center">

										<div class="tt-next-project-item">
											<a href="/gamers-assembly-2024" class="tt-npi-image" data-cursor="Gamers Assembly 2024">
												<figure class="tt-npi-image-inner">

													<img src="/template/assets/img/galerie/gamers-assembly-2024-2.webp" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
												</figure>
											</a>
											<div class="tt-npi-caption">
												<div class="tt-npi-title">
													<a href="/gamers-assembly-2024">Gamers Assembly 2024</a>
												</div>
												<div class="tt-npi-categories-wrap">
													<div class="tt-npi-category">Valorant</div>

												</div>
											</div>
										</div>

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
