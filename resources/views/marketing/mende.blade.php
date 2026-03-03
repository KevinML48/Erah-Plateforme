@extends('marketing.layouts.template')


@section('title', 'Mende | ERAH Esport')

@section('meta_description', 'ERAH Esport, club de gaming et d’esport basé à Mende en Lozère. Découvrez nos compétitions, événements et actions locales pour promouvoir le sport électronique.')

@section('meta_keywords', 'ERAH Esport Mende, esport Lozère, esport Occitanie, club gaming Mende, compétitions esport Mende, association esport Lozère, événements gaming Mende')

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
				class="ph-full ph-full-m ph-center ph-cap-xxxxlg ph-image-parallax ph-caption-parallax">

				<div class="ph-video ph-video-cover-1">
					<div class="ph-video-inner" style="position: relative; width: 100%; height: 100%;">
						<img src="/template/assets/img/mende/mende-3.jpg" alt="Image de couverture"
							style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
					</div>
				</div>





				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h1 class="ph-caption-title">Mende</h1>
							<div class="ph-caption-description max-width-700">
								Nous sommes fiers de représenter Mende
							</div>
						</div>
					</div>

				</div>

				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">

								<h1 class="ph-caption-title">Mende</h1>
								<div class="ph-caption-description max-width-700">
									Nous sommes fiers de représenter Mende
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

				<div class="tt-section padding-bottom-xlg-120">
					<div class="tt-section-inner tt-wrap max-width-1000">

						<div class="tt-heading tt-heading-lg">
							<h3 class="tt-heading-subtitle tt-text-reveal">Département de la Lozère</h3>
							<h2 class="tt-heading-title tt-text-reveal">Mende, du sport à l’esport</h2>
						</div>


						<p>La Lozère, département le moins peuplé de France, est un <strong>véritable écrin de
								nature.</strong></p>
						<p><strong>Entre les <a href="https://www.cevennes-gorges-du-tarn.com/" class="tt-link" target="_blank"
									rel="noopener noreferrer">monts de la Margeride, les gorges du Tarn et les plateaux
									de l’Aubrac et des Causses</a></strong>, elle offre des paysages grandioses et
							préservés.</p>
						<p>C’est un territoire authentique, idéal pour les amoureux d’activités de plein air
							<mark>(randonnée, VTT, sports d’eau vive)</mark> et ceux qui recherchent calme et qualité de
							vie.</p>

						<div class="tt-section no-padding-top">
							<div class="tt-section-inner tt-wrap max-width-1600">

								<div class="tt-row">
									<div class="tt-col-xl-6">

										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/mende/gorges-du-tarn.png" class="tt-image-link"
													data-cursor="View" data-fancybox data-caption="Paysage de Lozère">
													<img src="/template/assets/img/mende/gorges-du-tarn.png" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
												</a>
												<figcaption>
													Gorges du Tarn
												</figcaption>
											</figure>
										</div>


									</div>

									<div class="tt-col-xl-6">

										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/mende/aubrac.png" class="tt-image-link" data-cursor="View"
													data-fancybox data-caption="Aubrac">
													<img src="/template/assets/img/mende/aubrac.png" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
												</a>
												<figcaption>
													Aubrac
												</figcaption>
											</figure>
										</div>



									</div>

								</div>


								<div class="tt-section padding-top-xlg-180">
									<div class="tt-section-inner">

										<div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-60">

											<h2 class="tt-heading-title tt-text-reveal">Découvrir la Lozère</h2>
										</div>

<div class="tt-clipper" style="position: relative; width: 100%; height: 100%; overflow: hidden;">
  <a href="https://youtu.be/XQ_WDT2Y5xI?si=ue3TDiSGVvPoP7VV"
     class="tt-clipper-inner"
     data-fancybox
     data-caption="Découvrez la Lozère en vidéo"
     style="position: relative; display: block; width: 100%; height: 100%;">

    <!-- 🎥 Vidéo de fond -->
    <video autoplay muted loop playsinline
           class="tt-anim-zoomin tt-clipper-bg"
           style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
      <source src="/template/assets/img/mende/mende.mp4" type="video/mp4">
      Votre navigateur ne supporte pas la lecture de vidéos.
    </video>

    <!-- ▶️ Bouton Play centré -->
    <div class="tt-clipper-content"
         style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center;">
      <div class="tt-clipper-btn"
           style="background: rgba(0, 0, 0, 0.6); color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; transition: background 0.3s;">
        <i class="fa-solid fa-play"></i>
      </div>
    </div>

  </a>
</div>



										<a href="https://www.luxe-admiral.com/blog/que-faire-en-lozere-guide"
											class="tt-btn tt-btn-link" target="_blank" rel="nofollow noopener">
											<span class="tt-btn-icon"><i class="tt-btn-line"></i></span>
											<span data-hover="Plus d’informations">Plus d’informations</span>
										</a>

									</div>
								</div>

								<p>À <strong>Mende</strong>, la vie est douce et rythmée par les saisons. La nature est
									partout : <mark>sentiers de randonnée, rivières, forêts, montagnes…</mark> <br><br>
									Les Mendois aiment se retrouver sur le marché, encourager leurs clubs sportifs ou
									partager un aligot en bonne compagnie.</p>
								<p><strong><a href="https://x.com/ErahEsport" class="tt-link" target="_blank"
											rel="noopener noreferrer">Cette convivialité se retrouve aussi chez nous, à
											ERAH Esport :</a></strong> un esprit d’équipe fort, le goût du défi et
									l’envie de faire découvrir de nouvelles passions.</p>

								<p> </p>

								<p>Si Mende est réputée pour ses paysages et son patrimoine, elle est aussi une ville
									qui se tourne vers <mark>l’avenir</mark>. <strong><a
											href="https://mende.fr/" class="tt-link" target="_blank"
											rel="noopener noreferrer">Les initiatives locales autour du numérique ne
											cessent de se développer,</a></strong> et nous sommes fiers d’y contribuer.
								</p>

							</div>
						</div>

						<h3 class="tt-font-alter tt-text-uppercase tt-text-reveal">VILLE</h3>

						<p>Au cœur de ce territoire, <strong>Mende</strong>, petite capitale lozérienne, combine
							<strong>le charme d’une ville à taille humaine</strong> et la proximité immédiate de la
							nature. Avec ses rues pittoresques, sa <strong>cathédrale emblématique</strong>, ses
							services de proximité et sa vie associative dynamique, Mende séduit ceux qui veulent
							<strong>vivre dans un cadre apaisant</strong>, loin du stress des grandes villes, tout en
							restant connectés et actifs.</p>

						<div class="tt-image tti-border-radius margin-bottom-40">
							<figure>
								<img src="/template/assets/img/mende/mende-8.jpg" class="tt-anim-zoomin" loading="lazy" alt="Image">
								<figcaption>
									Ville Mende 48
								</figcaption>
							</figure>
						</div>


						<p>La ville de Mende se construit depuis des années comme étant une “ville sportive”. Mende
							s’inscrit dans une dynamique avec plus d’un Mendois sur deux licencié dans une association
							sportive.
							De nombreux événements sportifs sont aussi organisés sur le territoire.
						</p>

						<blockquote class="open-quote">
							<cite>Pourquoi ne pas créer un lien entre “ville sportive” et “ville e-sportive” ?</cite>
							<footer><cite title="Source Title">Kévin MOLINES</cite></footer>
						</blockquote>


						<p>Nous avons choisi Mende pour ses valeurs humaines, sa proximité et son dynamisme associatif.
							<a href="https://mende.fr/" class="tt-link" target="_blank"
								rel="noopener noreferrer">Comme notre ville, nous croyons en la force des liens, en
								l’importance de partager, et en la capacité à briller même depuis un petit coin de
								paradis.</a>
						</p>

					</div>


				</div>


				<div class="tt-section">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-100">

							<h2 class="tt-heading-title tt-text-reveal">Découvrez nos paysages</h2>
						</div>

						<div class="tt-content-slider tt-anim-fadeinup" data-loop="true" data-speed="800"
							data-pagination-type="bullets">

							<div class="swiper">

								<div class="swiper-wrapper">

									<div class="swiper-slide">
										<div class="tt-content-slider-item">
											<div class="tt-cs-image-wrap" data-swiper-parallax="50%">
												<img src="/template/assets/img/mende/mende-7.jpg" class="tt-cs-image" loading="lazy"
													alt="Image">
												<div class="swiper-lazy-preloader"></div>
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="tt-content-slider-item">
											<div class="tt-cs-image-wrap" data-swiper-parallax="50%">
												<img src="/template/assets/img/mende/mende-6.jpg" class="tt-cs-image" loading="lazy"
													alt="Image">
												<div class="swiper-lazy-preloader"></div>
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="tt-content-slider-item">
											<div class="tt-cs-image-wrap" data-swiper-parallax="50%">
												<img src="/template/assets/img/mende/mende-4.jpg" class="tt-cs-image" loading="lazy"
													alt="Image">
												<div class="swiper-lazy-preloader"></div>
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="tt-content-slider-item">
											<div class="tt-cs-image-wrap" data-swiper-parallax="50%">
												<img src="/template/assets/img/mende/mende-2.jpg" class="tt-cs-image" loading="lazy"
													alt="Image">
												<div class="swiper-lazy-preloader"></div>
											</div>
										</div>
									</div>

									<div class="swiper-slide">
										<div class="tt-content-slider-item">
											<div class="tt-cs-image-wrap" data-swiper-parallax="50%">
												<img src="/template/assets/img/mende/mende-1.jpg" class="tt-cs-image" loading="lazy"
													alt="Image">
												<div class="swiper-lazy-preloader"></div>
											</div>
										</div>
									</div>


								</div>


							</div>

							<div class="tt-cs-nav-prev cursor-arrow-left">
								<div class="tt-cs-nav-arrow tt-magnetic-item"></div>
							</div>
							<div class="tt-cs-nav-next cursor-arrow-right">
								<div class="tt-cs-nav-arrow tt-magnetic-item"></div>
							</div>

							<div class="tt-cs-pagination hide-cursor"></div>

						</div>

						<a href="https://mende.fr/" class="tt-btn tt-btn-link" target="_blank" rel="nofollow noopener">
							<span class="tt-btn-icon"><i class="tt-btn-line"></i></span>
							<span data-hover="En savoir plus">En savoir plus</span>
						</a>


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
