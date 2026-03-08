<!DOCTYPE html>
<html lang="fr">

<head>
  <!-- Titre et SEO -->
	<title>ERAH Esport</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="ERAH Esport est une association basée à Mende (Lozère), spécialisée dans la compétition et la promotion du gaming local et national.">
	<meta name="keywords" content="ERAH Esport, esport Lozère, esport Mende, club esport, gaming, compétitions esport, événements esport, association esport, tournois gaming, sport électronique">
	<meta name="author" content="ERAH Esport">

  <!-- Favicon -->
  <link rel="icon" href="/template/assets/img/logo.png" type="image/png" sizes="512x512">
  <link rel="apple-touch-icon" href="/template/assets/img/logo.png">

  <!-- Schema.org pour Google -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "ERAH Esport",
    "url": "https://erah-esport.fr",
    "logo": "/assets/img/logo.png",
    "sameAs": [
      "https://www.twitch.tv/erah_association",
      "https://www.instagram.com/erahesport/",
      "https://x.com/ErahEsport",
      "https://discord.gg/9G89kkSjRx"
    ]
  }
  </script>

  <!-- Préconnexion & polices Google -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Big+Shoulders+Display:wght@100..900&display=swap" rel="stylesheet">

  <!-- CSS critiques (appliqués immédiatement) -->
  <link rel="stylesheet" href="/template/assets/css/helper.css">
  <link rel="stylesheet" href="/template/assets/css/theme.css">
  <link rel="stylesheet" href="/template/assets/css/theme-light.css">

  <!-- Préchargement CSS moins critiques -->
  <link rel="preload" href="/template/assets/vendor/fontawesome/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/fancybox/css/fancybox.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="/template/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/template/assets/vendor/fancybox/css/fancybox.css">
    <link rel="stylesheet" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css">
  </noscript>

  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-H9C6F8VG4D"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-H9C6F8VG4D', { 'anonymize_ip': true });
  </script>
</head>



<style>
	.tt-grid-item {
		width: 20% !important;
		/* 100% / 5 colonnes */
		box-sizing: border-box;
	}

	@media (max-width: 1024px) {
		.tt-grid-item {
			width: 33.333% !important;
			/* 100% / 3 colonnes */
		}
	}

	@media (max-width: 600px) {
		.tt-grid-item {
			width: 50% !important;
		}
	}

	.tt-grid-items-wrap {
		margin-left: -0.75em;
		margin-right: -0.75em;
	}

	.tt-grid-item {
		padding-left: 0.75em;
		padding-right: 0.75em;
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

<body id="body" class="tt-transition tt-noise tt-magic-cursor tt-smooth-scroll">


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


			<div id="page-header"
				class="ph-full ph-full-m ph-center ph-cap-xxxxlg ph-image-parallax ph-caption-parallax">


				<div class="ph-video ph-video-cover-1">
  <div class="ph-video-inner">
    <!-- Vidéo avec poster léger -->
<video id="hero-video"
       loop
       muted
       autoplay
       playsinline
       preload="none"
       poster="/template/assets/img/galerie/interview-HopLan-2025.webp"
       style="width:100%; height:100%; object-fit:cover;">
  <source src="/template/assets/vids/Trailer site.mp4" type="video/mp4">
</video>

  </div>
</div>

				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h1 class="ph-caption-title">ERAH<br> Esport</h1>
							<div class="ph-caption-description max-width-700">
								ERAH, c’est une chance de<br> réussir dans l’esport
							</div>
						</div>
					</div>
				</div>
				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h1 class="ph-caption-title">ERAH<br> Esport</h1>
								<div class="ph-caption-description max-width-700">
									ERAH, c’est une chance de<br> réussir dans l’esport
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="ph-social">
    <ul>
        <li>
            <a href="https://www.twitch.tv/erah_association" 
               class="tt-magnetic-item" 
               target="_blank" 
               rel="noopener"
               aria-label="Twitch ERAH Esport">
               <i class="fa-brands fa-twitch"></i>
            </a>
        </li>
        <li>
            <a href="https://www.instagram.com/erahesport/" 
               class="tt-magnetic-item" 
               target="_blank" 
               rel="noopener"
               aria-label="Instagram ERAH Esport">
               <i class="fa-brands fa-instagram"></i>
            </a>
        </li>
        <li>
            <a href="https://x.com/ErahEsport" 
               class="tt-magnetic-item" 
               target="_blank" 
               rel="noopener"
               aria-label="X ERAH Esport">
               <i class="fa-brands fa-twitter"></i>
            </a>
        </li>
        <li>
            <a href="https://discord.gg/9G89kkSjRx" 
               class="tt-magnetic-item" 
               target="_blank" 
               rel="noopener"
               aria-label="Discord ERAH Esport">
               <i class="fa-brands fa-discord"></i>
            </a>
        </li>
    </ul>
</div>


				<div class="tt-scroll-down">
					<a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0" aria-label="Descendre vers le contenu de la page">
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

                @include('marketing.partials.home-access-rapide', [
                    'homeQuickAccess' => $homeQuickAccess ?? [],
                ])
                @include('marketing.partials.home-en-ce-moment', [
                    'homeEnCeMoment' => $homeEnCeMoment ?? [],
                ])

				<div class="tt-section padding-top-xlg-140 padding-bottom-xlg-120">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row">
							<div class="tt-col-lg-4">

								<div class="tt-heading tt-heading-xlg">
									<h2 class="tt-heading-title tt-text-reveal">Notre Histoire</h2>
								</div>

								<div class="tt-text-uppercase margin-top-30 tt-text-reveal">
									ERAH Esport<br> un club compétitif basé à Mende
								</div>

							</div>
							<div class="tt-col-lg-1 padding-top-30">
							</div>
							<div class="tt-col-lg-7 tt-align-self-center">

								<div class="text-xxlg font-500 tt-text-reveal">
									Nous sommes un club esport en développement sur Valorant, avec une équipe très
									compétitive
								</div>

								<a href="about.html"
									class="tt-btn tt-btn-outline margin-top-40 tt-magnetic-item tt-anim-fadeinup">
									<span data-hover="En savoir plus">En savoir plus</span>
								</a>

							</div>
						</div>
					</div>
				</div>

				<div class="tt-section padding-top-xlg-140 border-top">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-heading tt-heading-xxxlg tt-heading-center">
							<h2 class="tt-heading-title tt-text-reveal">Équipes ERAH</h2>
							<p class="max-width-500 tt-text-uppercase tt-text-reveal">Découvrez nos équipes</p>
						</div>

					</div>
				</div>

				<div class="tt-section">
  <div class="tt-section-inner max-width-2200">

    <div class="tt-portfolio-compact-list pcl-caption-hover pcl-image-hover">
      <div class="pcli-inner">

        <!-- VALORANT -->
        <a href="valorant-VCL.html" class="pcli-item tt-anim-fadeinup" data-cursor="Go !">
          <div class="pcli-item-inner">
            <div class="pcli-col pcli-col-image">
              <div class="pcli-image">
                <img src="/template/assets/img/VCL_LINE_UP.png" loading="lazy" alt="Image">
              </div>
            </div>
            <div class="pcli-col pcli-col-count">
              <div class="pcli-count"></div>
            </div>
            <div class="pcli-col pcli-col-caption">
              <div class="pcli-caption">
                <h2 class="pcli-title">Valorant</h2>
                <div class="pcli-categories">
                  <div class="pcli-category">VCL</div>
                </div>
              </div>
            </div>
          </div>
        </a>

        <!-- STAFF -->
        <a href="staff.html" class="pcli-item tt-anim-fadeinup" data-cursor="Go !">
          <div class="pcli-item-inner">
            <div class="pcli-col pcli-col-image">
              <div class="pcli-video">

                <video autoplay loop muted playsinline preload="metadata" poster="/template/assets/img/galerie/intervention-2.webp">
                  <source src="/template/assets/vids/vue-mende.mp4" type="video/mp4">
                  <source src="/template/assets/vids/vue-mende.webm" type="video/webm">
                </video>

              </div>
            </div>
            <div class="pcli-col pcli-col-count">
              <div class="pcli-count"></div>
            </div>
            <div class="pcli-col pcli-col-caption">
              <div class="pcli-caption">
                <h2 class="pcli-title">Staff</h2>
                <div class="pcli-categories">
                  <div class="pcli-category">ERAH</div>
                </div>
              </div>
            </div>
          </div>
        </a>

        <!-- MÉDICAL -->
        <a href="medical.html" class="pcli-item tt-anim-fadeinup" data-cursor="Go !">
          <div class="pcli-item-inner">
            <div class="pcli-col pcli-col-image">
              <div class="pcli-video">

                <video autoplay loop muted playsinline preload="metadata" poster="/template/assets/img/staffs-erah/infinity-up.jpg">
                  <source src="/template/assets/vids/Video InfinityUP presentation.mp4" type="video/mp4">
                  <source src="/template/assets/vids/Video InfinityUP presentation.webm" type="video/webm">
                </video>

              </div>
            </div>
            <div class="pcli-col pcli-col-count">
              <div class="pcli-count"></div>
            </div>
            <div class="pcli-col pcli-col-caption">
              <div class="pcli-caption">
                <h2 class="pcli-title">Médical</h2>
                <div class="pcli-categories">
                  <div class="pcli-category">ERAH</div>
                </div>
              </div>
            </div>
          </div>
        </a>

      </div>
    </div>
  </div>
</div>


				@include('marketing.partials.home-reviews')


				<div class="tt-section no-padding padding-top-xlg-40 padding-bottom-xlg-40">
					<div class="tt-section-inner">

						<div class="tt-scrolling-text-crossed">
							<div class="tt-scrolling-text-crossed-inner">

								<div class="tt-scrolling-text scrt-dyn-separator scrt-color-reverse"
									data-scroll-speed="7" data-change-direction="true" data-opposite-direction="true">
									<div class="tt-scrt-inner">
										<div class="tt-scrt-content">
											Challengers France VCL 2026
											<span class="tt-scrt-separator">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
													<path
														d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
													</path>
												</svg>
											</span>
										</div>
									</div>
								</div>

								<div class="tt-scrolling-text scrt-dyn-separator" data-scroll-speed="7"
									data-change-direction="true">
									<div class="tt-scrt-inner">
										<div class="tt-scrt-content">
											Challengers France VCL 2026
											<span class="tt-scrt-separator">
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
													<path
														d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
													</path>
												</svg>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<br>
				<br>
				<br>

				<div class="tt-heading tt-heading-xxxlg tt-heading-center">
					<h2 class="tt-heading-title tt-text-reveal">Foire aux questions</h2>
					<p class="max-width-500 tt-text-uppercase tt-text-reveal">
						Vous souhaitez en savoir plus sur ERAH Esport ?
					</p>
				</div>



				<div class="tt-section">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Où est situé le club ERAH Esport ?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Notre club est basé à <a href="/mende.html" class="tt-link">Mende</a>, en Lozère
										(48), au cœur de l’Occitanie.</p>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Quelles sont les équipes actives au sein d'ERAH
												Esport ?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Nous disposons actuellement d'une équipe principale :</p>
									<ul>
										<li>Une équipe VCL (Valorant Roster Compétitif), engagée dans les <a
												href="https://tracker.gg/valorant/premier/teams/f698836f-cfac-4872-bf2f-9bfaaeeefc25/matches"
												class="tt-link" target="_blank" rel="noopener noreferrer">tournois et
												ligues de haut niveau</a>.</li>
									</ul>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Dans quelle ligue ou compétition évolue ERAH
												Esport ?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Notre structure évolue désormais uniquement dans le circuit VCL, une ligue compétitive officielle de Valorant.</p>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Est-ce que le club ERAH Esport est ouvert au
												public ou aux nouveaux joueurs ?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Nous recrutons ponctuellement, selon les besoins de nos équipes ou de nos
										projets. Suivez-nous sur nos réseaux pour ne rien manquer.</p>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Est-ce que vous participez à des événements
												physiques (LAN, salons, etc.) ?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Oui ! <strong>ERAH Esport est présent lors de plusieurs événements IRL comme la
											<a href="hoplan-2025.html" class="tt-link" target="_blank"
												rel="noopener noreferrer">Gamers Assembly, HopLAN, Espot
												Paris</a></strong>, et organise aussi des bootcamps et des conférences, tout en participant à divers événements.</p>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Est-ce que ERAH Esport est une association ?
											</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Oui, <strong><a
												href="https://www.journal-officiel.gouv.fr/pages/associations-detail-annonce/?q.id=id:202300481173"
												class="tt-link" target="_blank" rel="noopener noreferrer">ERAH Esport
												est une association loi 1901</a></strong>, à but non lucratif,
										structurée autour de valeurs humaines et d’un projet de développement
										professionnel.</p>
								</div>
							</div>

							<div class="tt-accordion-item tt-anim-fadeinup">
								<div class="tt-accordion-heading">
									<div class="tt-ac-head cursor-alter">
										<div class="tt-ac-head-inner">
											<h4 class="tt-ac-head-title">Comment suivre vos compétitions ou vos annonces
												?</h4>
										</div>
									</div>
									<div class="tt-accordion-caret">
										<div class="tt-accordion-caret-inner tt-magnetic-item">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
												<path
													d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z">
												</path>
											</svg>
										</div>
									</div>
								</div>
								<div class="tt-accordion-content max-width-1000">
									<p>Nous communiquons activement via nos réseaux sociaux, notre site web, ainsi que
										sur notre serveur <a href="https://discord.gg/pfnptbTb">Discord</a>.</p>
									<p><em><u>N’hésitez pas à nous suivre sur nos réseaux !</u></em></p>
								</div>
							</div>

						</div>

					</div>
				</div>


				<br>
				<br>

				<div class="tt-heading tt-heading-xxxlg tt-heading-center" style="margin-bottom: -10px;">
					<h3 class="tt-heading-subtitle tt-text-reveal">Présentation ERAH</h3>
				</div>





				<div class="tt-section no-padding-top">
					<div class="tt-section-inner">

						<div class="tt-clipper">
							<a data-fancybox href="https://youtu.be/MCh7wI7gMOU"
								data-caption="Erah Esport" class="tt-clipper-inner" data-cursor="Play" aria-label="Vidéo YouTube : Erah Esport">


								<div class="tt-clipper-bg">
									<video loop muted autoplay playsinline preload="metadata" poster="/template/assets/img/galerie/lancement-erah.webp">
  <source src="/template/assets/vids/Presentation ERAH.mp4" data-src="/template/assets/vids/erah-aca.mp4" type="video/mp4">
  <source src="/template/assets/vids/Presentation ERAH.webm" data-src="/template/assets/vids/erah-aca.webm" type="video/webm">
</video>

								</div>

								<div class="tt-clipper-content">

									<div class="tt-clipper-btn">
										<i class="fa-solid fa-play"></i>
									</div>

								</div>
							</a>
						</div>

					</div>
				</div>

				<div class="tt-section">
					<div class="tt-section-inner">

						<div class="tt-heading tt-heading-xxxlg tt-heading-center">
							<h2 class="tt-heading-title tt-text-reveal" style="font-style: normal;">Notre parcours</h2>
							<p class="max-width-500 tt-text-uppercase tt-text-reveal">
								Depuis 2024, nous offrons à n'importe qui de vivre une expérience compétitive à haut
								niveau.
							</p>
						</div>


						<br>
						<br>


						<div class="tt-sticky-horizontal-scroll" data-speed="2000" data-direction="left">
							<div class="tt-shs-pin-wrap">
								<div class="tt-shs-animation-wrap">

									<div class="tt-shs-item">
										<a href="gamers-assembly-2024.html"
											style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/gamers-assembly-2024-2.webp"
														class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Gamers Assembly Valorant 2024
													</figcaption>
												</figure>
											</div>
										</a>
									</div>


									<div class="tt-shs-item">
										<a href="hoplan-2024.html" style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/HopLan-2024-1.webp" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
													<figcaption>
														HopLan Valorant 2024
													</figcaption>
												</figure>
											</div>
										</a>
									</div>

									<div class="tt-shs-item">
										<a href="gamers-assembly-2025.html"
											style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/gamers-assembly-2025-1.webp"
														class="tt-anim-zoomin" loading="lazy" alt="Image">
													<figcaption>
														Gamers Assembly Valorant 2025
													</figcaption>
												</figure>
											</div>
										</a>
									</div>

									<div class="tt-shs-item">
										<a href="hoplan-2025.html" style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/Hoplan-2025-2.webp" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
													<figcaption>
														HopLan Valorant 2025
													</figcaption>
												</figure>
											</div>
										</a>
									</div>

									<div class="tt-shs-item">
										<a href="hoplan-2025.html" style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/challengers_valorant.jpg" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
													<figcaption>
														Qualification Challengers France VCL 2026
													</figcaption>
												</figure>
											</div>
										</a>
									</div>

									<div class="tt-shs-item">
										<a href="lyon-esport-2025.html" style="text-decoration: none; color: inherit;">
											<div class="tt-shs-item-inner">
												<figure class="tt-shs-item-image">
													<img src="/template/assets/img/galerie/lyon_esport.jpg" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
													<figcaption>
														LyonEsport Valorant 2025
													</figcaption>
												</figure>
											</div>
										</a>
									</div>

								</div>

								<div class="tt-shs-keep-scrolling">
									<span>Scroll pour avancer</span>
									<i class="fa-solid fa-arrows-up-down"></i>
								</div>

							</div>
						</div>

					</div>
				</div>


				<div class="tt-section padding-top-xlg-120 no-padding-bottom">
					<div class="tt-section-inner">

						<div class="tt-heading tt-heading-xxxlg tt-heading-center">
							<h3 class="tt-heading-subtitle tt-text-reveal">Valorant</h3>
							<h2 class="tt-heading-title tt-text-reveal" style="font-style: normal;">Performances</h2>
							<p class="max-width-500 tt-text-uppercase tt-text-reveal">
								Découvrez toutes nos<br>
								performances depuis nos débuts
							</p>
						</div>


					</div>
				</div>


				<div class="tt-section">
					<div class="tt-section-inner">

						<div class="tt-avards-list">

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">VCL Invite</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 8 avec place en playoffs</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Contenders League</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Champions – Montée en VCL</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Open Tour France – Division 2</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Double Top 1</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>



							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Last Chance Qualifier</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Quart de finalistes</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Europe – BO6</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 16</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">France – MW3</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 2</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">One Tap Series</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Vainqueurs</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">EnisorailCup 2</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 1 x2</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2024 / 2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Gamers Assembly</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 8/7</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">HopLan – Strasbourg</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 3</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Alsace Arena – Call of Duty</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 4</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Qualification Challengers France VCL 2026</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 1</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>

							<div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
								<div class="tt-avlist-item-inner">
									<div class="tt-avlist-col tt-avlist-col-title">
										<h4 class="tt-avlist-title">Lyon Esport Valorant</h4>
									</div>
									<div class="tt-avlist-col tt-avlist-col-description">
										<div class="tt-avlist-description">Top 7</div>
									</div>
									<div class="tt-avlist-col tt-avlist-col-info">
										<div class="tt-avlist-info">2025</div>
									</div>
								</div>
							</div>


						</div>

					</div>
				</div>


				<div class="tt-section">
					<div class="tt-section-inner tt-wrap max-width-1800">

						<div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-100">
							<h2 class="tt-heading-title tt-text-reveal" style="font-style: normal;">Dernières Vidéos
							</h2>
							<p class="max-width-500 tt-anim-fadeinup text-muted">
								Retrouvez ici nos dernières vidéos publiées sur YouTube et plongez dans nos moments
								forts en images.
							</p>
						</div>



						<div class="tt-row">

							<div class="tt-col-xl-4">

								<h5>ERAH Esport</h5>

								<div class="tt-embed">
									<iframe 
    class="tt-embed-item" 
    src="https://www.youtube.com/embed/MCh7wI7gMOU" 
    allowfullscreen 
    loading="lazy" 
    frameborder="0"
    title="Vidéo YouTube : Présentation ERAH Esport">
</iframe>

								</div>

							</div>
							<div class="tt-col-xl-4">

								<h5>Interview HopLan 2025</h5>

								<div class="tt-embed">
									<iframe 
    class="tt-embed-item" 
    src="https://www.youtube.com/embed/n_LEo-tp3Jk" 
    allowfullscreen 
    loading="lazy" 
    frameborder="0"
    title="Vidéo YouTube : Présentation ERAH Esport - HopLan 2025">
</iframe>

								</div>

							</div>
							<div class="tt-col-xl-4">

								<h5>Valorant Story</h5>

								<div class="tt-embed">
									<iframe 
    class="tt-embed-item" 
    src="https://www.youtube.com/embed/6-ebq2tKpAs" 
    allowfullscreen 
    loading="lazy" 
    frameborder="0"
    title="Vidéo YouTube : Présentation ERAH Esport - Valorant Story">
</iframe>


								</div>




							</div>
						</div>


					</div>
				</div>
			</div>
		</div>


		<div class="tt-section">
			<div class="tt-section-inner tt-wrap">

				<div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-120">
					<h2 class="tt-heading-title tt-text-reveal" style="font-style: normal;">Ils nous font confiance</h2>
					<p class="max-width-500 tt-anim-fadeinup text-muted">Découvrez nos partenaires et collaborateurs</p>
				</div>



				<ul class="tt-logo-wall tt-anim-fadeinup">
					<li>
						<a href="https://mende.fr/" class="tt-logo-wall-item cursor-alter"
							target="_blank" rel="noopener">
							<div class="tt-lv-item-inner">
								<img src="/template/assets/img/clients/client-1-light.png" class="tt-lv-img-light" loading="lazy"
									alt="Image">
								<img src="/template/assets/img/clients/client-1-dark.png" class="tt-lv-img-dark" loading="lazy"
									alt="Image">
							</div>
						</a>
					</li>
					<li>
						<a href="https://coeurdelozere.fr/" class="tt-logo-wall-item cursor-alter"
							target="_blank" rel="noopener">
							<div class="tt-lv-item-inner">
								<img src="/template/assets/img/clients/client-6-light.png" class="tt-lv-img-light" loading="lazy"
									alt="Image">
								<img src="/template/assets/img/clients/client-6-dark.png" class="tt-lv-img-dark" loading="lazy"
									alt="Image">
							</div>
						</a>
					</li>
					<li>
						<a href="https://www.intersport.fr/Loz%C3%A8re-48/MENDE-48000/INTERSPORT-MENDE/00534_000/" class="tt-logo-wall-item cursor-alter"
							target="_blank" rel="noopener">
							<div class="tt-lv-item-inner">
								<img src="/template/assets/img/clients/client-7-light.png" class="tt-lv-img-light" loading="lazy"
									alt="Image">
								<img src="/template/assets/img/clients/client-7-dark.png" class="tt-lv-img-dark" loading="lazy"
									alt="Image">
							</div>
						</a>
					</li>
					<li>
						<a href="https://www.lalozerenouvelle.com/" class="tt-logo-wall-item cursor-alter"
							target="_blank" rel="noopener">
							<div class="tt-lv-item-inner">
								<img src="/template/assets/img/clients/client-4-light.png" class="tt-lv-img-light" loading="lazy"
									alt="Image">
								<img src="/template/assets/img/clients/client-4-dark.png" class="tt-lv-img-dark" loading="lazy"
									alt="Image">
							</div>
						</a>
					</li>
					<li>
						<a href="https://www.formationsuniversitaires.fr/" class="tt-logo-wall-item cursor-alter"
							target="_blank" rel="noopener">
							<div class="tt-lv-item-inner">
								<img src="/template/assets/img/clients/client-8-dark.webp" class="tt-lv-img-light" loading="lazy"
									alt="Image">
								<img src="/template/assets/img/clients/client-8-light.webp" class="tt-lv-img-dark" loading="lazy"
									alt="Image">
							</div>
						</a>
					</li>

				</ul>

				<br>
				<br>


			</div>
		</div>


		</div>


<footer id="tt-footer" class="border-top">
			<div class="tt-footer-inner tt-wrap">

				<div class="tt-row">
					<div class="tt-col-xl-3 tt-col-sm-6">
						<div class="tt-footer-widget">
							<h5 class="tt-footer-widget-heading">Support</h5>
							<ul class="tt-footer-widget-list">
								<li><a href="https://www.vlr.gg/team/18024/erah-esport" class="tt-link" target="_blank"
										rel="noopener">Page VLR</a></li>
								<li><a href="https://tracker.gg/valorant/premier/teams/f698836f-cfac-4872-bf2f-9bfaaeeefc25/matches"
										class="tt-link" target="_blank" rel="noopener">Ligue Invite</a></li>
								<li><a href="https://discord.gg/9G89kkSjRx" class="tt-link" target="_blank"
										rel="noopener">Discord</a></li>
								<li><a href="https://www.linkedin.com/company/erah-association/" class="tt-link"
										target="_blank" rel="noopener">LinkedIn</a></li>

                                <li><a href="mentions-legales.html" class="tt-link">Mentions Légales</a></li>
							</ul>
						</div>
					</div>
<div class="tt-col-xl-3 tt-col-sm-6">
          <div class="tt-footer-widget">
            <h5 class="tt-footer-widget-heading">Sitemap</h5>
            <ul class="tt-footer-widget-list">
              <li><a href="about.html" class="tt-link">A propos</a></li>
              <li><a href="nos-stages.html" class="tt-link">Nos Stages</a></li>
              <li><a href="mende.html" class="tt-link">Mende</a></li>
              <li><a href="boutique.html" class="tt-link">Boutique</a></li>
              @auth
                <li><a href="{{ route('app.profile') }}" class="tt-link">Mon profil</a></li>
              @else
                <li><a href="{{ route('login') }}" class="tt-link">Se connecter</a></li>
              @endauth
              <li><a href="contact.html" class="tt-link">Contact</a></li>
              <li><a href="#" id="manage-cookies" class="tt-link">Gérer mes cookies</a></li>
            </ul>
          </div>
        </div>

					<div class="tt-col-xl-3 tt-col-sm-6">
						<div class="tt-footer-widget">
							<h5 class="tt-footer-widget-heading">Contact</h5>
							<ul class="tt-footer-widget-list">
								<li>
									<a href="https://maps.app.goo.gl/MTiizsoAEUrp7NpZ6" class="tt-link" target="_blank"
										rel="nofollow noopener">Mende, 48000</a>
								</li>
								<li><a href="mailto:erah.association@gmail.com"
										class="tt-link">erah.association@gmail.com</a></li>
								<li><a href="tel:+33649425578" class="tt-link"> +(33) 06 49 42 55 78</a></li>
								<li>
									<div class="tt-social-buttons">
										<ul>
											<li><a href="https://www.twitch.tv/erah_association"
													class="tt-magnetic-item" target="_blank" rel="noopener" aria-label="Twitch ERAH Esport"><i
														class="fa-brands fa-twitch"></i></a></li>
											<li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item"
													target="_blank" rel="noopener" aria-label="Instagram ERAH Esport"><i
														class="fa-brands fa-instagram"></i></a></li>
											<li><a href="https://x.com/ErahEsport" class="tt-magnetic-item"
													target="_blank" rel="noopener" aria-label="X ERAH Esport"><i
														class="fa-brands fa-twitter"></i></a></li>
											<li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item"
													target="_blank" rel="noopener" aria-label="Discord ERAH Esport"><i
														class="fa-brands fa-discord"></i></a></li>

										</ul>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="tt-col-xl-3 tt-col-sm-6 tt-justify-content-xl-end">
						<div class="tt-footer-widget">
							<ul class="tt-footer-widget-list">
								<li>
									<div class="tt-footer-logo">
										<a href="index.html" class="tt-magnetic-item">
											<img src="/template/assets/img/logo.png" class="tt-logo-light" loading="lazy"
												alt="Logo"> <img src="/template/assets/img/logo.png" class="tt-logo-dark"
												loading="lazy" alt="Logo"> </a>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</footer>


		<a href="#" class="tt-scroll-to-top">
			<div class="tt-stt-progress tt-magnetic-item">
				<svg class="tt-stt-progress-circle" width="100%" height="100%" viewBox="-1 -1 102 102">
					<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
				</svg>
			</div>
		</a>

		</div>


	</main>

	<!-- Librairies JS -->
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





</body>

</html>


