<!DOCTYPE html>

<!--
	Template:   Jesper - Creative Portfolio Showcase HTML Website Template
	Author:     Themetorium
	URL:        https://themetorium.net/
-->

<html lang="en">
	<head>

		<!-- Title -->
		<title>Portfolio | Jesper - Creative Portfolio Showcase HTML Website Template by Themetorium</title>

		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="description" content="Download Jesper - Creative Portfolio Showcase HTML Website Template that comes with rich features and well-commented code. Made by Themetorium.">
		<meta name="author" content="themetorium.net">

		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Favicon (http://www.favicon-generator.org/) -->
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">

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
								<h2 class="ph-caption-subtitle">My Work</h2>
								<h1 class="ph-caption-title">Projects</h1>
								<div class="ph-caption-description max-width-700">
									Discover a showcase of my creative journey that reflects my passion for crafting engaging digital experiences
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
									<h2 class="ph-caption-subtitle">Portfolio</h2>
									<h1 class="ph-caption-title">Cool Stuff</h1>
									<div class="ph-caption-description max-width-700">
										Explore a collection of my creative journey, showcasing my passion for designing immersive digital experiences
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
							<li><a href="https://www.facebook.com/themetorium" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a></li>
							<li><a href="https://dribbble.com/Themetorium" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-dribbble"></i></a></li>
							<li><a href="https://www.behance.net/Themetorium" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-behance"></i></a></li>
							<li><a href="https://www.youtube.com/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-youtube"></i></a></li>
							<!-- <li><a href="https://x.com/Themetorium" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a></li> -->
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
									<textPath xlink:href="#textcircle">Scroll to Explore - Scroll to Explore -</textPath>
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
							<div id="portfolio-grid" class="pgi-hover">

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
										<div class="tt-grid-categories">

											<!-- Begin tt-Ggrid categories trigger 
											======================================= 
											* Use class "ttgr-cat-fixed" to enable categories trigger fixed position.
											* Use class "ttgr-cat-colored" to enable categories trigger colored style.
											-->
											<div class="ttgr-cat-trigger-wrap ttgr-cat-fixed ttgr-cat-colored">
												<div class="ttgr-cat-trigger-holder">
													<a href="#portfolio-grid" class="ttgr-cat-trigger" data-offset="150">
														<div class="ttgr-cat-text hide-cursor">
															<span data-hover="Open">Filter</span>
														</div>
													</a> <!-- /.ttgr-cat-trigger -->
												</div> <!-- /.ttgr-cat-trigger-holder -->
											</div>
											<!-- End tt-Ggrid categories trigger -->

											<!-- Begin tt-Ggrid categories nav 
											=================================== -->
											<div class="ttgr-cat-nav">
												<div class="ttgr-cat-close-btn">Close <i class="fas fa-times"></i></div> <!-- For mobile devices! -->
												<div class="ttgr-cat-list-holder cursor-close" data-lenis-prevent>
													<div class="ttgr-cat-list-inner">
														<div class="ttgr-cat-list-content">
															<ul class="ttgr-cat-list hide-cursor">
																<li class="ttgr-cat-item"><a href="#" class="active">All</a></li>
																<li class="ttgr-cat-item"><a href="#" data-filter=".lifestyle">Lifestyle</a></li>
																<li class="ttgr-cat-item"><a href="#" data-filter=".artistic">Artistic</a></li>
																<li class="ttgr-cat-item"><a href="#" data-filter=".wellness">Wellness</a></li>
															</ul>
														</div> <!-- /.ttgr-cat-links-content -->
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
													<a href="{{ route('leaderboards.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-1.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Lifestyle</div>
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
													<a href="{{ route('clips.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-2.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Lifestyle</div>
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
													<a href="{{ route('matches.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-video-wrap ttgr-height">
																	<video class="pgi-video" loop muted preload="metadata" poster="/app-ui/assets/vids/1200/video-4-1200.jpg">
																		<source src="/app-ui/assets/vids/placeholder.mp4" data-src="/app-ui/assets/vids/1200/video-4-1200.mp4" type="video/mp4">
																		<source src="/app-ui/assets/vids/placeholder.webm" data-src="/app-ui/assets/vids/1200/video-4-1200.webm" type="video/webm">
																	</video>
																</figure> <!-- /.pgi-video-wrap -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('matches.index') }}">Matchs</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Artistic</div>
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
													<a href="{{ route('notifications.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-3.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Artistic</div>
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
													<a href="{{ route('duels.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-4.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Wellness</div>
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
													<a href="{{ route('missions.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-5.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Lifestyle</div>
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
													<a href="{{ route('bets.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-6.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Lifestyle</div>
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
													<a href="{{ route('wallet.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-7.jpg" loading="lazy" alt="image">
																</figure> <!-- /.pgi-image -->
															</div> <!-- /.pgi-image-inner -->
														</div> <!-- /.pgi-image-holder -->
													</a> <!-- /.pgi-image-wrap -->

													<div class="pgi-caption">
														<div class="pgi-caption-inner">
															<h2 class="pgi-title">
																<a href="{{ route('wallet.index') }}">Wallet</a>
															</h2>
															<div class="pgi-categories-wrap">
																<div class="pgi-category">Wellness</div>
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
													<a href="{{ route('gifts.index') }}" class="pgi-image-wrap" data-cursor="View<br>Project">
														<!-- Use class "cover-opacity-*" to set image overlay if needed. For example "cover-opacity-2". Useful if class "pgi-cap-inside" is enabled on "portfolio-grid". Note: It is individual and depends on the image you use. More info about helper classes in file "helper.css". -->
														<div class="pgi-image-holder">
															<div class="pgi-image-inner tt-anim-zoomin">
																<figure class="pgi-image ttgr-height">
																	<img src="/app-ui/assets/img/portfolio/1200/portfolio-8.jpg" loading="lazy" alt="image">
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
																<div class="pgi-category">Wellness</div>
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



	</body>

</html>


