<header id="tt-header" class="tt-header-alter tt-header-scroll tt-header-filled">
	@php
		$platformShortcuts = app(\App\Services\ShortcutService::class)->getForUser(auth()->user());

		$primaryNavigation = [
			['label' => 'Accueil', 'url' => url('/')],
			['label' => 'A propos', 'url' => url('/about')],
			[
				'label' => 'Equipes',
				'children' => [
					['label' => 'Valorant VCL', 'url' => url('/valorant-vcl')],
					['label' => 'Staff ERAH', 'url' => url('/staff')],
					['label' => 'Medical', 'url' => url('/medical')],
				],
			],
			[
				'label' => 'Contenu',
				'children' => [
					['label' => 'Galerie Photo', 'url' => route('marketing.gallery-photos')],
					['label' => 'Galerie Video', 'url' => url('/galerie-video')],
					['label' => 'Nos evenements', 'url' => url('/evenement')],
					['label' => 'Nos stages', 'url' => url('/nos-stages')],
				],
			],
			[
				'label' => 'Plateforme',
				'url' => route('marketing.platform'),
				'children' => collect([
					['label' => 'FAQ', 'url' => route('marketing.faq')],
				])
					->merge(collect($platformShortcuts)->map(fn ($shortcut) => [
						'label' => $shortcut['label'],
						'url' => $shortcut['url'],
					]))
					->when(auth()->check() && auth()->user()->role === \App\Models\User::ROLE_ADMIN, fn ($items) => $items->push([
						'label' => 'Admin dashboard',
						'url' => route('admin.dashboard'),
					]))
					->values()
					->all(),
			],
		];

		$secondaryNavigation = [
			['label' => 'Supporter', 'url' => route('supporter.show')],
			['label' => 'Boutique', 'url' => route('marketing.boutique')],
			['label' => 'Postuler', 'url' => route('marketing.contact')],
			['label' => 'Mende', 'url' => url('/mende')],
		];

		$desktopPrimaryAccountAction = auth()->check()
			? ['label' => 'Mon profil', 'url' => route('app.profile')]
			: ['label' => 'Se connecter', 'url' => route('login')];

		$desktopAdminAction = auth()->check() && auth()->user()->role === \App\Models\User::ROLE_ADMIN
			? ['label' => 'Admin', 'url' => route('admin.dashboard')]
			: null;
	@endphp

	<div class="tt-header-inner tt-noise">
		<div class="tt-header-col tt-header-col-left">
			<div class="tt-logo">
				<a href="/" class="tt-magnetic-item">
					<img src="/template/assets/img/logo.png" class="tt-logo-light" alt="Logo">
					<img src="/template/assets/img/logo.png" class="tt-logo-dark" alt="Logo">
				</a>
			</div>
		</div>

		<div class="tt-header-col tt-header-col-center">
			<nav class="tt-main-menu tt-m-menu-center" aria-label="Navigation principale">
				<div class="tt-main-menu-holder">
					<div class="tt-main-menu-inner">
						<div class="tt-main-menu-content">
							<ul class="tt-main-menu-list">
								@foreach($primaryNavigation as $item)
									@if(!empty($item['children']))
										<li class="tt-submenu-wrap tt-submenu-master">
											<div class="tt-submenu-trigger">
												<a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
											</div>
											<div class="tt-submenu">
												<ul class="tt-submenu-list">
													@foreach($item['children'] as $child)
														<li><a href="{{ $child['url'] }}">{{ $child['label'] }}</a></li>
													@endforeach
												</ul>
											</div>
										</li>
									@else
										<li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
									@endif
								@endforeach

								<li class="tt-submenu-wrap tt-submenu-master tt-main-menu-secondary-more">
									<div class="tt-submenu-trigger">
										<a href="#">Plus</a>
									</div>
									<div class="tt-submenu">
										<ul class="tt-submenu-list">
											@foreach($secondaryNavigation as $item)
												<li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
											@endforeach
										</ul>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</nav>
		</div>

		<div class="tt-header-col tt-header-col-right">
			<div class="tt-header-account-cluster hide-from-xlg" aria-label="Actions utilisateur desktop">
				<a href="{{ $desktopPrimaryAccountAction['url'] }}"
					class="tt-btn tt-btn-secondary tt-btn-sm tt-magnetic-item tt-header-account-btn tt-header-account-btn-primary">
					<span data-hover="{{ $desktopPrimaryAccountAction['label'] }}">{{ $desktopPrimaryAccountAction['label'] }}</span>
				</a>
				@if($desktopAdminAction)
					<a href="{{ $desktopAdminAction['url'] }}"
						class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item tt-header-account-btn">
						<span data-hover="Admin">Admin</span>
					</a>
				@endif
				@auth
					<a href="{{ route('settings.index') }}"
						class="tt-header-account-icon tt-magnetic-item"
						aria-label="Parametres">
						<i class="fas fa-cog" aria-hidden="true"></i>
					</a>
				@endauth
			</div>

			<div id="tt-m-menu-toggle-btn-wrap">
				<div class="tt-m-menu-toggle-btn-text">
					<span class="tt-m-menu-text-menu">Menu</span>
					<span class="tt-m-menu-text-close">Close</span>
				</div>
				<div class="tt-m-menu-toggle-btn-holder">
					<a href="#" class="tt-m-menu-toggle-btn" aria-label="Ouvrir le menu">
						<span></span>
					</a>
				</div>
			</div>

			<div class="tt-style-switch">
				<div class="tt-style-switch-inner tt-magnetic-item">
					<div class="tt-stsw-light"><i class="fas fa-sun"></i></div>
					<div class="tt-stsw-dark"><i class="fas fa-moon"></i></div>
				</div>
			</div>
		</div>
	</div>
</header>
