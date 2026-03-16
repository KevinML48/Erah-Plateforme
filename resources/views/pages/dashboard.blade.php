<!DOCTYPE html>

<!--
	Template:   Jesper - Creative Portfolio Showcase HTML Website Template
	Author:     Themetorium
	URL:        https://themetorium.net/
-->

<html lang="en">
	<head>

		<!-- Title -->
		<title>Dashboard | Plateforme ERAH</title>

		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="description" content="Dashboard de la plateforme ERAH avec acces rapide aux modules principaux.">
		<meta name="author" content="ERAH">

		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Favicon (http://www.favicon-generator.org/) -->
		<link rel="shortcut icon" href="/template/assets/img/logo.png" type="image/png">
		<link rel="icon" href="/template/assets/img/logo.png" type="image/png" sizes="512x512">
		<link rel="apple-touch-icon" href="/template/assets/img/logo.png">

		<!-- Your Google Analytics code goes here -->

		<!-- Google fonts (https://fonts.google.com/) -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> <!-- Body font -->
		<link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@100..900&display=swap" rel="stylesheet"> <!-- Secondary/Alter font -->

		<!-- Libs and Plugins CSS -->
		<link rel="stylesheet" href="/template/assets/vendor/fontawesome/css/all.min.css"> <!-- Font Icons CSS (https://fontawesome.com) Free version! -->
		<link rel="stylesheet" href="/template/assets/vendor/fancybox/css/fancybox.css"> <!-- Fancybox (lightbox) JS (https://fancyapps.com/) -->
		<link rel="stylesheet" href="/template/assets/vendor/swiper/css/swiper-bundle.min.css"> <!-- Swiper CSS (https://swiperjs.com/) -->

		<!-- Master CSS -->
		<link rel="stylesheet" href="/template/assets/css/helper.css">
		<link rel="stylesheet" href="/template/assets/css/theme.css">

		<!-- Light style CSS -->
		<link rel="stylesheet" href="/template/assets/css/theme-light.css">
		<style>
			.platform-dashboard-menu .ttgr-cat-trigger-holder {
				padding: 2px;
				border-radius: 999px;
				background: linear-gradient(135deg, rgba(255, 255, 255, .2), rgba(255, 255, 255, .04));
				box-shadow: 0 18px 32px rgba(0, 0, 0, .22);
			}

			.platform-dashboard-menu .ttgr-cat-trigger {
				position: relative;
				display: flex;
				align-items: center;
				min-width: 154px;
				min-height: 60px;
				padding: 0;
				border-radius: 999px;
				background: linear-gradient(135deg, #ef0a0a 0%, #cb0505 55%, #95090c 100%);
				box-shadow: inset 0 1px 0 rgba(255, 255, 255, .2), 0 16px 28px rgba(142, 4, 10, .28);
				overflow: hidden;
				transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
			}

			.platform-dashboard-menu .ttgr-cat-trigger::before {
				content: "";
				position: absolute;
				inset: 1px;
				border-radius: inherit;
				background: linear-gradient(180deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0) 42%);
				pointer-events: none;
			}

			body:not(.is-mobile) .platform-dashboard-menu .ttgr-cat-trigger:hover {
				transform: translateY(-2px);
				filter: saturate(1.05);
				box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22), 0 22px 38px rgba(142, 4, 10, .34);
			}

			.platform-dashboard-menu .ttgr-cat-text {
				position: relative;
				z-index: 1;
				display: flex;
				align-items: center;
				justify-content: flex-start;
				gap: 12px;
				width: 100%;
				padding: 0 24px 0 20px;
				text-align: left;
				overflow: visible;
			}

			.platform-dashboard-menu .ttgr-cat-text::before {
				content: "";
				flex: 0 0 auto;
				width: 8px;
				height: 8px;
				border-radius: 50%;
				background: rgba(255, 255, 255, .96);
				box-shadow: 0 0 0 7px rgba(255, 255, 255, .12);
			}

			.platform-dashboard-menu .ttgr-cat-text::after {
				content: "";
				position: absolute;
				right: 24px;
				top: 50%;
				width: 9px;
				height: 9px;
				border-top: 2px solid rgba(255, 255, 255, .92);
				border-right: 2px solid rgba(255, 255, 255, .92);
				transform: translateY(-50%) rotate(45deg);
			}

			.platform-dashboard-menu .ttgr-cat-text > span {
				position: static;
				display: inline-block;
				font-size: 14px;
				font-weight: 600;
				letter-spacing: .15em;
				text-transform: uppercase;
				transform: none !important;
			}

			body:not(.is-mobile) .platform-dashboard-menu .ttgr-cat-trigger:hover .ttgr-cat-text > span {
				transform: none !important;
			}

			body:not(.is-mobile) .platform-dashboard-menu .ttgr-cat-text > span::before {
				display: none;
			}

			.platform-dashboard-menu .ttgr-cat-nav {
				background: rgba(0, 0, 0, .95);
			}

			.platform-dashboard-menu .ttgr-cat-close-btn {
				position: absolute;
				top: 40px;
				left: 48px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				width: 120px;
				height: 120px;
				border: 2px solid rgba(255, 255, 255, .82);
				border-radius: 50%;
				background: rgba(255, 255, 255, .02);
				font-size: 18px;
				letter-spacing: .08em;
				text-transform: uppercase;
				z-index: 3;
			}

			.platform-dashboard-menu-shell {
				display: grid;
				grid-template-columns: minmax(340px, .9fr) minmax(360px, 1.1fr);
				gap: 48px;
				align-items: center;
				min-height: 100vh;
				padding: 120px 72px 96px;
			}

			.platform-dashboard-menu-copy {
				display: grid;
				gap: 20px;
			}

			.platform-dashboard-menu-eyebrow {
				color: rgba(255, 255, 255, .56);
				font-size: 13px;
				letter-spacing: .24em;
				text-transform: uppercase;
			}

			.platform-dashboard-menu-list {
				display: grid;
				gap: 14px;
				margin: 0;
				padding: 0;
				list-style: none;
			}

			.platform-dashboard-menu-item a {
				display: grid;
				grid-template-columns: 56px minmax(0, 1fr);
				gap: 18px;
				align-items: baseline;
				color: #fff;
				text-decoration: none;
			}

			.platform-dashboard-menu-index {
				color: rgba(255, 255, 255, .28);
				font-size: 13px;
				letter-spacing: .14em;
				text-transform: uppercase;
			}

			.platform-dashboard-menu-label {
				display: block;
				font-family: "Big Shoulders Display", sans-serif;
				font-size: clamp(52px, 5vw, 104px);
				line-height: .88;
				text-transform: uppercase;
				transition: color .25s ease, transform .3s ease;
			}

			.platform-dashboard-menu-meta {
				display: block;
				margin-top: 8px;
				color: rgba(255, 255, 255, .54);
				font-size: 13px;
				letter-spacing: .14em;
				text-transform: uppercase;
			}

			.platform-dashboard-menu-item a:hover .platform-dashboard-menu-label,
			.platform-dashboard-menu-item a:focus .platform-dashboard-menu-label,
			.platform-dashboard-menu-item a.is-active .platform-dashboard-menu-label {
				color: #e30613;
				transform: translateX(10px);
			}

			.platform-dashboard-menu-preview {
				position: relative;
				min-height: 620px;
				border-radius: 34px;
				overflow: hidden;
				border: 1px solid rgba(255, 255, 255, .1);
				background: rgba(255, 255, 255, .02);
				box-shadow: 0 34px 74px rgba(0, 0, 0, .28);
			}

			.platform-dashboard-menu-preview::after {
				content: "";
				position: absolute;
				inset: 0;
				background: linear-gradient(180deg, rgba(0, 0, 0, .08), rgba(0, 0, 0, .56));
				pointer-events: none;
			}

			.platform-dashboard-menu-preview img {
				position: absolute;
				inset: 0;
				width: 100%;
				height: 100%;
				object-fit: cover;
				transition: opacity .2s ease, transform .45s ease;
			}

			.platform-dashboard-menu-preview img.is-switching {
				opacity: .2;
				transform: scale(1.04);
			}

			.platform-dashboard-menu-preview-caption {
				position: absolute;
				left: 28px;
				right: 28px;
				bottom: 28px;
				z-index: 1;
				display: grid;
				gap: 10px;
			}

			.platform-dashboard-menu-preview-kicker {
				color: rgba(255, 255, 255, .7);
				font-size: 14px;
				letter-spacing: .12em;
				text-transform: uppercase;
			}

			.platform-dashboard-menu-preview-caption strong {
				font-family: "Big Shoulders Display", sans-serif;
				font-size: clamp(34px, 3vw, 58px);
				line-height: .94;
				text-transform: uppercase;
			}

			.platform-dashboard-menu-preview-description {
				margin: 0;
				max-width: 34ch;
				color: rgba(255, 255, 255, .72);
				font-size: 16px;
				line-height: 1.6;
			}

			@media (max-width: 1199.98px) {
				.platform-dashboard-menu .ttgr-cat-close-btn {
					top: 20px;
					left: 20px;
					width: 92px;
					height: 92px;
					font-size: 14px;
				}

				.platform-dashboard-menu-shell {
					grid-template-columns: 1fr;
					gap: 28px;
					padding: 120px 24px 40px;
				}

				.platform-dashboard-menu-preview {
					min-height: 360px;
				}
			}

			@media (max-width: 767.98px) {
				.platform-dashboard-menu .ttgr-cat-trigger {
					min-width: 132px;
					min-height: 54px;
				}

				.platform-dashboard-menu .ttgr-cat-text {
					padding: 0 18px;
				}

				.platform-dashboard-menu .ttgr-cat-text > span {
					font-size: 13px;
					letter-spacing: .12em;
				}

				.platform-dashboard-menu .ttgr-cat-close-btn {
					top: 14px;
					left: 14px;
					width: 76px;
					height: 76px;
					font-size: 11px;
				}

				.platform-dashboard-menu-shell {
					gap: 20px;
					padding: 92px 18px 24px;
				}

				.platform-dashboard-menu-label {
					font-size: clamp(42px, 13vw, 72px);
				}

				.platform-dashboard-menu-meta,
				.platform-dashboard-menu-preview-description {
					font-size: 12px;
				}

				.platform-dashboard-menu-preview {
					display: none;
				}
			}

			@media (max-width: 479.98px) {
				.platform-dashboard-menu .ttgr-cat-trigger {
					min-width: 120px;
					min-height: 50px;
				}

				.platform-dashboard-menu-preview-caption {
					left: 20px;
					right: 20px;
					bottom: 20px;
				}
			}
		</style>

	</head>

	
	<!-- ===========
	///// Body /////
	================
	* Use class "tt-transition" to enable page transitions.
	* Use class "tt-magic-cursor" to enable magic cursor.
	* Use class "tt-noise" to enable the background noise effect on the whole page. 
	* Use class "tt-smooth-scroll" to enable page smooth scroll. 
	* Use class "tt-lightmode-default" to enable light style by default (you must clear your browser's cookies and cache first!).
	* Note: there may be classes that are specific to this page only!
	-->
	<body id="body" class="tt-transition tt-noise tt-magic-cursor tt-smooth-scroll">

		@php
			$dashboardModules = [
				[
					'title' => 'Classements',
					'category' => 'Progression',
					'description' => 'Consultez les ligues, les positions globales et la dynamique competitive des membres.',
					'route' => route('leaderboards.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/classements.png',
				],
				[
					'title' => 'Clips',
					'category' => 'Contenu',
					'description' => 'Retrouvez les extraits publies, les performances recentes et la bibliotheque video.',
					'route' => route('clips.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/clips.png',
				],
				[
					'title' => 'Matchs',
					'category' => 'Competition',
					'description' => 'Suivez les rencontres a venir, les resultats et les details des matchs.',
					'route' => route('matches.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/matchs.png',
				],
				[
					'title' => 'Notifications',
					'category' => 'Infos',
					'description' => 'Centralisez les alertes importantes, les actions a suivre et les annonces de plateforme.',
					'route' => route('notifications.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/notifications.png',
				],
				[
					'title' => 'Assistant',
					'category' => 'Guidage',
					'description' => 'Discutez avec l assistant ERAH pour comprendre la plateforme et savoir quoi faire ensuite.',
					'route' => route('assistant.index'),
					'image' => '/template/assets/img/logo.png',
				],
				[
					'title' => 'Duels',
					'category' => 'Communaute',
					'description' => 'Accedez aux confrontations entre membres et aux interactions competitives de la communaute.',
					'route' => route('duels.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/duels.png',
				],
				[
					'title' => 'Missions',
					'category' => 'Objectifs',
					'description' => 'Suivez les cycles de missions, les recompenses a debloquer et votre progression active.',
					'route' => route('missions.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/missions.png',
				],
				[
					'title' => 'Paris',
					'category' => 'Predictions',
					'description' => 'Visualisez les pronostics, les mises disponibles et votre historique de paris.',
					'route' => route('bets.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/paris.png',
				],
				[
					'title' => 'Portefeuille',
					'category' => 'Compte',
					'description' => 'Gerez votre solde, les transactions recentes et les mouvements de credits.',
					'route' => route('wallet.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/wallets.png',
				],
				[
					'title' => 'Cadeaux',
					'category' => 'Boutique',
					'description' => 'Explorez les recompenses disponibles, les cadeaux a debloquer et le suivi de vos demandes.',
					'route' => route('gifts.index'),
					'image' => '/app-ui/assets/img/image-menu-plateforme/cadeaux.png',
				],
			];
		@endphp


		<!-- *************************************
		*********** Begin body inner ************* 
		************************************** -->
		<main id="body-inner">


			<!-- Begin page transition (do not remove!!!) 
			=========================== -->
			<div id="tt-page-transition">
				<div class="tt-ptr-overlay-top tt-noise"></div>
				<div class="tt-ptr-overlay-bottom tt-noise"></div>
				<div class="tt-ptr-preloader">
					<div class="tt-ptr-prel-content">
						<!-- Hint: You may need to change the img height and opacity to match your logo type. You can do this from the "theme.css" file (find: ".tt-ptr-prel-image"). -->
						<img src="/template/assets/img/logo.png" class="tt-ptr-prel-image" alt="Logo">
					</div> <!-- /.tt-ptr-prel-content -->
				</div> <!-- /.tt-ptr-preloader -->
			</div>
			<!-- End page transition -->

			<!-- Begin magic cursor 
			======================== -->
			<div id="magic-cursor">
				<div id="ball"></div>
			</div>
			<!-- End magic cursor --> 

			<!-- ===================
			///// Begin header /////
			========================
			* Use class "tt-header-scroll" to hide header on scroll down and show on scroll up.
			* Use class "tt-header-fixed" to set header to fixed position (no effect with class "tt-header-scroll").
			* Use class "tt-header-filled" to add background color to header on scroll (effect only with class "tt-header-scroll" and "tt-header-fixed").
			* Use class "tt-header-alter" to enable alternative layout (for desktop only!).
			-->
			@include('marketing.partials.header')

			
			<!-- *************************************
			*********** Begin content wrap *********** 
			************************************** -->
			<div id="tt-content-wrap">

				
				<!-- ========================
				///// Begin page header /////
				============================= 
				* Use class "ph-full" to enable fullscreen size (no effect on small screens!).
				* Use class "ph-full-m" to enable fullscreen size on small screens.
				* Use class "ph-cap-sm", "ph-cap-lg", "ph-cap-xlg", "ph-cap-xxlg" "ph-cap-xxxlg" or "ph-cap-xxxxlg" to set caption size (no class = default size).
				* Use class "ph-center" to align the content to the center. 
				* Use class "ph-caption-parallax" to enable caption parallax.
				* Use class "ph-image-parallax" to enable image/video parallax (if image/video exist).
				* Use class "ph-bg-is-light" if needed, it makes the elements dark and more visible if you use a very light background image (effect only if the image/video exist).
				-->
				<div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">

					<!-- Begin page header image 
					============================= 
					* Use class "ph-image-grayscale" to enable black & white image.
					* Use class "ph-image-cover-*" to set image overlay opacity. For example "ph-image-cover-2" or "ph-image-cover-2-5" (up to "ph-image-cover-9-5"). 
					-->
					<!-- <div class="ph-image ph-image-cover-1">
						<div class="ph-image-inner">
							<img src="/app-ui/assets/img/page-header/ph-1.jpg" alt="Image">
						</div>
					</div> -->
					<!-- End page header image -->

					<!-- Begin page header video 
					============================= 
					* Use class "ph-video-grayscale" to enable black & white video.
					* Use class "ph-video-cover-*" to set video overlay opacity. For example "ph-video-cover-2" or "ph-video-cover-2-5" (up to "ph-video-cover-9-5"). 
					* Use attribute "loop" in <video> tag to make the video play repeatedly.
					-->
					<!-- <div class="ph-video ph-video-cover-1">
						<div class="ph-video-inner">
							<video loop muted autoplay playsinline preload="metadata" poster="/app-ui/assets/vids/1920/video-1-1920.jpg">
								<source src="/app-ui/assets/vids/placeholder.mp4" data-src="/app-ui/assets/vids/1920/video-1-1920.mp4" type="video/mp4">
								<source src="/app-ui/assets/vids/placeholder.webm" data-src="/app-ui/assets/vids/1920/video-1-1920.webm" type="video/webm">
							</video>
						</div>
					</div> -->
					<!-- End page header video -->
						
					<div class="page-header-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h2 class="ph-caption-subtitle">Plateforme ERAH</h2>
								<h1 class="ph-caption-title">Modules</h1>
								<div class="ph-caption-description max-width-700">
									Accedez rapidement aux espaces cles de la plateforme et passez d un module a l autre depuis un point d entree unique.
								</div>
							</div> <!-- /.ph-caption-inner -->
						</div> <!-- /.ph-caption -->

					</div> <!-- /.page-header-inner -->

					<!-- Begin page header mask
					============================ 
					Note: ph-mask is basically a clone of caption. If you want to use a different text on the mask then it is a bit tricky to fit. For better results, make sure that it will be the same length as possible as the original caption text (especially the title). It should also contain the same number of lines. Sometimes this can be difficult to achieve, in which case we recommend simply using identical text to the original caption.
					-->
					<div class="page-header-inner ph-mask">
						<div class="ph-mask-inner tt-wrap">

							<div class="ph-caption">
								<div class="ph-caption-inner">
									<h2 class="ph-caption-subtitle">Plateforme</h2>
									<h1 class="ph-caption-title">ERAH</h1>
									<div class="ph-caption-description max-width-700">
										Naviguez entre competition, contenu, progression, recompenses et services sans quitter le dashboard.
									</div>
								</div> <!-- /.ph-caption-inner -->
							</div> <!-- /.ph-caption -->

						</div> <!-- /.ph-mask-inner -->
					</div>
					<!-- End page header mask -->


					<!-- Begin social buttons
					========================== -->
					<div class="ph-social">
						<ul>
							<li><a href="{{ route('matches.index') }}" class="tt-magnetic-item"><i class="fa-solid fa-gamepad"></i></a></li>
							<li><a href="{{ route('leaderboards.index') }}" class="tt-magnetic-item"><i class="fa-solid fa-trophy"></i></a></li>
							<li><a href="{{ route('clips.index') }}" class="tt-magnetic-item"><i class="fa-solid fa-video"></i></a></li>
							<li><a href="/faq" class="tt-magnetic-item"><i class="fa-solid fa-circle-question"></i></a></li>
						</ul>
					</div>
					<!-- End social buttons -->

					<!-- Begin scroll down
					=======================
					* Note: Circle shown only if class "ph-full" or "ph-full-m" is enabled in "page-header" but not on small screens! Otherwise, only the arrow icon will be shown to save space.
					-->
					<div class="tt-scroll-down">
						<!-- You can change "data-offset" attribute to set scroll top offset -->
						<a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
							<div class="tt-scrd-icon"></div>
							<svg viewBox="0 0 500 500">
								<defs>
									<path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
								</defs>
								<text dy="30">
									<!-- If you change the text, you probably have to change the CSS parameters as well. In the "theme.css" file, find ".tt-scroll-down text {" and change the "font-size" and "letter-spacing" to fit the text correctly. -->
									<textPath xlink:href="#textcircle">Explorer les modules - Explorer les modules -</textPath>
								</text>
							</svg>
						</a> <!-- /.tt-scroll-down-inner -->
					</div>
					<!-- End scroll down -->

				</div>
				<!-- End page header -->


				<!-- *************************************
				*********** Begin page content *********** 
				************************************** -->
				<div id="tt-page-content">


					<!-- =======================
					///// Begin tt-section /////
					============================ 
					* You can use padding classes if needed. For example "padding-top-xlg-150", "padding-bottom-xlg-150", "no-padding-top", "no-padding-bottom", etc.
					* You can use classes "border-top" and "border-bottom" if needed. 
					* Note: Each situation may be different and each section may need different classes according to your needs. More info about helper classes can be found in the file "helper.css".
					-->
					<div class="tt-section">
						<div class="tt-section-inner max-width-2200">

							<!-- Begin portfolio grid (works combined with tt-Ggrid!)
							========================== 
							* Use class "pgi-hover" to enable portfolio grid item hover effect (behavior depends on "ttgr-gap-*" classes below!).
							* Use class "pgi-cap-hover" to enable portfolio grid item caption hover effect (effect only with class "pgi-cap-inside"! Also no effect on mobile devices!).
							* Use class "pgi-cap-center" to position portfolio grid item caption to center.
							* Use class "pgi-cap-inside" to position portfolio grid item caption to inside.
							--> 
							<div id="portfolio-grid" class="pgi-hover" data-tour="dashboard-module-grid">

								<!-- Begin tt-Grid
								=================== 
								* Use class "ttgr-layout-2", "ttgr-layout-3", "ttgr-layout-4" to set grid layout (columns). No class = one column.
								* Use class "ttgr-layout-1-2", "ttgr-layout-2-1", "ttgr-layout-2-3", "ttgr-layout-3-2", "ttgr-layout-3-4" or "ttgr-layout-4-3" to set grid mixed layout (columns).
								* Use class "ttgr-layout-creative-1" or "ttgr-layout-creative-2" to set grid creative mixed layout (no effect with classes "ttgr-portrait", "ttgr-portrait-half", "ttgr-not-cropped" and "ttgr-shifted").
								* Use class "ttgr-portrait" or "ttgr-portrait-half" to enable portrait mode (no effect with classes "ttgr-layout-creative-1", "ttgr-layout-creative-2" and "ttgr-not-cropped").
								* Use class "ttgr-gap-1", "ttgr-gap-2", "ttgr-gap-3", "ttgr-gap-4", "ttgr-gap-5" or "ttgr-gap-6" to add space between items.
								* Use class "ttgr-not-cropped" to enable not cropped mode (effect only with classes "ttgr-layout-2", "ttgr-layout-3" and "ttgr-layout-4").
								* Use class "ttgr-shifted" to enable shifted layout (effect only with classes "ttgr-layout-2", "ttgr-layout-3" and "ttgr-layout-4").
								-->
								<div class="tt-grid ttgr-layout-creative-1 ttgr-gap-4">

									<!-- Begin tt-Ggrid top content 
									================================ -->
									<div class="tt-grid-top">

										<!-- Begin tt-Ggrid categories/filter
										====================================== -->
										<div class="tt-grid-categories platform-dashboard-menu">

											<!-- Begin tt-Ggrid categories trigger 
											======================================= 
											* Use class "ttgr-cat-fixed" to enable categories trigger fixed position.
											* Use class "ttgr-cat-colored" to enable categories trigger colored style.
											-->
											<div class="ttgr-cat-trigger-wrap ttgr-cat-fixed ttgr-cat-colored">
												<div class="ttgr-cat-trigger-holder">
													<a href="#portfolio-grid" class="ttgr-cat-trigger" data-offset="150">
														<div class="ttgr-cat-text hide-cursor">
															<span data-hover="Menu">Menu</span>
														</div>
													</a> <!-- /.ttgr-cat-trigger -->
												</div> <!-- /.ttgr-cat-trigger-holder -->
											</div>
											<!-- End tt-Ggrid categories trigger -->

											<!-- Begin tt-Ggrid categories nav 
											=================================== -->
											<div class="ttgr-cat-nav">
												<div class="ttgr-cat-close-btn">Fermer <i class="fas fa-times"></i></div> <!-- For mobile devices! -->
												<div class="ttgr-cat-list-holder cursor-close" data-lenis-prevent>
													<div class="ttgr-cat-list-inner">
														<div class="platform-dashboard-menu-shell">
															<div class="platform-dashboard-menu-copy">
																<span class="platform-dashboard-menu-eyebrow">Navigation plateforme</span>
																<ul class="platform-dashboard-menu-list hide-cursor">
																	@foreach ($dashboardModules as $module)
																		<li class="platform-dashboard-menu-item">
																			<a
																				href="{{ $module['route'] }}"
																				data-menu-image="{{ $module['image'] }}"
																				data-menu-title="{{ $module['title'] }}"
																				data-menu-eyebrow="{{ $module['category'] }}"
																				data-menu-description="{{ $module['description'] }}"
																				@if ($loop->first) class="is-active" @endif
																			>
																				<span class="platform-dashboard-menu-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
																				<span>
																					<span class="platform-dashboard-menu-label">{{ $module['title'] }}</span>
																					<span class="platform-dashboard-menu-meta">{{ $module['category'] }}</span>
																				</span>
																			</a>
																		</li>
																	@endforeach
																</ul>
															</div>
															<div class="platform-dashboard-menu-preview">
																<img id="platformDashboardMenuPreviewImage" src="{{ $dashboardModules[0]['image'] }}" alt="{{ $dashboardModules[0]['title'] }}">
																<div class="platform-dashboard-menu-preview-caption">
																	<span id="platformDashboardMenuPreviewEyebrow" class="platform-dashboard-menu-preview-kicker">{{ $dashboardModules[0]['category'] }}</span>
																	<strong id="platformDashboardMenuPreviewTitle">{{ $dashboardModules[0]['title'] }}</strong>
																	<p id="platformDashboardMenuPreviewDescription" class="platform-dashboard-menu-preview-description">{{ $dashboardModules[0]['description'] }}</p>
																</div>
															</div>
														</div>
													</div> <!-- /.ttgr-cat-links-inner -->
												</div> <!-- /.ttgr-cat-links-holder -->
											</div>
											<!-- End tt-Ggrid categories nav -->

										</div>
										<!-- End tt-Ggrid categories/filter-->

									</div>
									<!-- End tt-Grid top content -->


									<!-- Begin tt-Grid items wrap 
									============================== -->
									<div class="tt-grid-items-wrap isotope-items-wrap">

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item lifestyle">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"! Also no effect on small screens!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('leaderboards.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/classements.png" loading="lazy" alt="Classements">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('leaderboards.index') }}">Classements</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Progression</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item lifestyle">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('clips.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/clips.png" loading="lazy" alt="Clips">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('clips.index') }}">Clips</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Contenu</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item artistic">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('matches.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/matchs.png" loading="lazy" alt="Matchs">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('matches.index') }}">Matchs</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Competition</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item artistic">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('notifications.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/notifications.png" loading="lazy" alt="Notifications">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('notifications.index') }}">Notifications</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Infos</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item wellness">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('duels.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/duels.png" loading="lazy" alt="Duels">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('duels.index') }}">Duels</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Communaute</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item lifestyle">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('missions.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/missions.png" loading="lazy" alt="Missions">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('missions.index') }}">Missions</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Objectifs</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item lifestyle">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('bets.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/paris.png" loading="lazy" alt="Paris">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('bets.index') }}">Paris</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Predictions</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item wellness">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('wallet.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/wallets.png" loading="lazy" alt="Portefeuille">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('wallet.index') }}">Portefeuille</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Compte</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

										<!-- Begin tt-Grid item
										======================== -->
										<div class="tt-grid-item isotope-item wellness">
											<div class="ttgr-item-inner">

												<!-- Begin portfolio grid item 
												===============================
												* Use class "pgi-image-is-light" if needed, it makes the caption visible better if you use light image (only effect if "pgi-cap-inside" is enabled on "portfolio-grid"!).
												-->
												<div class="portfolio-grid-item">
													<a href="{{ route('gifts.index') }}" class="pgi-image-wrap" data-cursor="Ouvrir<br>module">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/image-menu-plateforme/cadeaux.png" loading="lazy" alt="Cadeaux">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('gifts.index') }}">Cadeaux</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Boutique</div>
																<!-- <div class="pgi-category">Varia</div> -->
															</div> <!-- /.pli-categories-wrap -->
														</div> <!-- /.pgi-caption-inner -->
													</div> <!-- /.pgi-caption -->
												</div>
												<!-- End portfolio grid item -->

											</div> <!-- /.ttgr-item-inner -->
										</div>
										<!-- End tt-Grid item -->

									</div>
									<!-- End tt-Grid items wrap  -->

								</div>
								<!-- End tt-Grid -->

								<!-- Begin tt-pagination (uncomment below code if you want to use pagination)
								========================= 
								* Use class "tt-pagin-center" to align center.
								-->
								<!-- <div class="tt-pagination tt-pagin-center tt-anim-fadeinup">
									<div class="tt-pagin-prev">
										<a href="" class="tt-pagin-item tt-magnetic-item"><i class="fas fa-arrow-left"></i></a>
									</div>
									<div class="tt-pagin-numbers">
										<a href="#" class="tt-pagin-item tt-magnetic-item active">1</a>
										<a href="" class="tt-pagin-item tt-magnetic-item">2</a>
										<a href="" class="tt-pagin-item tt-magnetic-item">3</a>
										<a href="" class="tt-pagin-item tt-magnetic-item">4</a>
									</div>
									<div class="tt-pagin-next">
										<a href="" class="tt-pagin-item tt-pagin-next tt-magnetic-item"><i class="fas fa-arrow-right"></i></a>
									</div>
								</div> -->
								<!-- End tt-pagination -->

							</div>
							<!-- End portfolio grid -->

						</div> <!-- /.tt-section-inner -->
					</div>
					<!-- End tt-section -->


					<!-- =======================
					///// Begin tt-section /////
					============================ 
					* You can use padding classes if needed. For example "padding-top-xlg-150", "padding-bottom-xlg-150", "no-padding-top", "no-padding-bottom", etc.
					* You can use classes "border-top" and "border-bottom" if needed. 
					* Note: Each situation may be different and each section may need different classes according to your needs. More info about helper classes can be found in the file "helper.css".
					-->
					<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
						<div class="tt-section-inner tt-wrap">

							<div class="tt-row margin-bottom-40">
								<div class="tt-col-xl-8">

									<!-- Begin tt-Heading 
									====================== 
									* Use class "tt-heading-xsm", "tt-heading-sm", "tt-heading-lg", "tt-heading-xlg", "tt-heading-xxlg" or "tt-heading-xxxlg" to set caption size (no class = default size).
									* Use class "tt-heading-center" to align tt-Heading to center.
									* Use class "tt-text-reveal" or "tt-anim-fadeinup" with title or subtitle element to enable text reveal animation.
									* Use prepared helper class "max-width-*" to add custom width if needed. Example: "max-width-800". More info about helper classes can be found in the file "helper.css".
									-->
									<div class="tt-heading tt-heading-xxxlg no-margin">
										<h3 class="tt-heading-subtitle tt-text-reveal">Aide</h3>
										<h2 class="tt-heading-title tt-text-reveal">Besoin d'infos<br> plateforme&nbsp;?</h2>
									</div>
									<!-- End tt-Heading -->

								</div> <!-- /.tt-col -->
							
								<div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

									<div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
										Si vous avez besoin d'informations sur ERAH, consultez la FAQ complete.<br>
										Vous y trouverez un tutoriel clair pour bien demarrer.
									</div>

									<!-- Begin big round button 
									============================ -->
									<div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
										<a href="/faq" class="tt-big-round-ptn-holder tt-magnetic-item">
											<div class="tt-big-round-ptn-inner">Voir la<br> FAQ</div>
										</a>
									</div>
									<!-- End big round button -->

								</div> <!-- /.tt-col -->
							</div><!-- /.tt-row --> 

						</div> <!-- /.tt-section-inner -->
					</div>
					<!-- End tt-section -->


				</div>
				<!-- End page content -->


				@include('marketing.partials.footer')

			</div>
			<!-- End content wrap -->


		</main>
		<!-- End body inner -->



        @include('partials.mission-live-toasts')
        @include('marketing.partials.guided-tour')

		<!-- ====================
		///// Scripts below /////
		===================== -->

		<!-- Core JS -->
		<script src="/template/assets/vendor/jquery/jquery.min.js"></script> <!-- jquery JS (https://jquery.com) -->

		<!-- Libs and Plugins JS -->
		<script src="/template/assets/vendor/gsap/gsap.min.js"></script> <!-- GSAP JS (https://gsap.com/) -->
		<script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js"></script> <!-- GSAP ScrollToPlugin JS (https://gsap.com/docs/v3/Plugins/ScrollToPlugin/) -->
		<script src="/template/assets/vendor/gsap/ScrollTrigger.min.js"></script> <!-- GSAP ScrollTrigger JS (https://gsap.com/docs/v3/Plugins/ScrollTrigger/) -->
		<script src="/template/assets/vendor/lenis.min.js"></script> <!-- Lenis (smooth scroll) JS (https://lenis.darkroom.engineering/) -->
		<script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js"></script> <!-- imagesloaded JS (http://imagesloaded.desandro.com) -->
		<script src="/template/assets/vendor/isotope/isotope.pkgd.min.js"></script> <!-- Isotope JS (http://isotope.metafizzy.co) -->
		<script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js"></script> <!-- Isotope Packery Mode JS (https://isotope.metafizzy.co/layout-modes/packery.html) -->
		<script src="/template/assets/vendor/fancybox/js/fancybox.umd.js"></script> <!-- Fancybox (lightbox) JS (https://fancyapps.com/) -->
		<script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js"></script> <!-- Swiper JS (https://swiperjs.com/) -->

		<!-- Master JS -->
		<script src="/template/assets/js/theme.js"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				var menuItems = document.querySelectorAll('.platform-dashboard-menu-item a');
				var previewImage = document.getElementById('platformDashboardMenuPreviewImage');
				var previewEyebrow = document.getElementById('platformDashboardMenuPreviewEyebrow');
				var previewTitle = document.getElementById('platformDashboardMenuPreviewTitle');
				var previewDescription = document.getElementById('platformDashboardMenuPreviewDescription');
				var previewSwapTimer = null;

				function setActiveMenuItem(item) {
					if (!item) {
						return;
					}

					menuItems.forEach(function (link) {
						link.classList.toggle('is-active', link === item);
					});

					if (previewEyebrow) {
						previewEyebrow.textContent = item.getAttribute('data-menu-eyebrow') || '';
					}

					if (previewTitle) {
						previewTitle.textContent = item.getAttribute('data-menu-title') || '';
					}

					if (previewDescription) {
						previewDescription.textContent = item.getAttribute('data-menu-description') || '';
					}

					if (!previewImage) {
						return;
					}

					var nextImage = item.getAttribute('data-menu-image');
					var nextTitle = item.getAttribute('data-menu-title') || 'Module';

					if (!nextImage || previewImage.getAttribute('src') === nextImage) {
						previewImage.setAttribute('alt', nextTitle);
						return;
					}

					previewImage.classList.add('is-switching');
					window.clearTimeout(previewSwapTimer);
					previewSwapTimer = window.setTimeout(function () {
						previewImage.setAttribute('src', nextImage);
						previewImage.setAttribute('alt', nextTitle);
						previewImage.classList.remove('is-switching');
					}, 120);
				}

				menuItems.forEach(function (item, index) {
					item.addEventListener('mouseenter', function () {
						setActiveMenuItem(item);
					});

					item.addEventListener('focus', function () {
						setActiveMenuItem(item);
					});

					if (index === 0) {
						setActiveMenuItem(item);
					}
				});
			});
		</script>



	</body>

</html>

