@extends('marketing.layouts.template')

@section('title', 'Mon profil | ERAH Plateforme')
@section('meta_description', 'Mettez a jour vos informations profil, vos reseaux, vos raccourcis et suivez votre progression.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .profile-form-card,
        .profile-side-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 28px;
        }

        .profile-avatar {
            width: 124px;
            height: 124px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .2);
            margin-bottom: 16px;
            background: rgba(255, 255, 255, .03);
        }

        .profile-kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 22px;
        }

        .profile-kpi-card {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 10px;
            padding: 14px 16px;
        }

        .profile-kpi-value {
            display: block;
            font-size: 26px;
            line-height: 1.1;
            font-weight: 700;
        }

        .profile-inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .profile-shortcut-card {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 10px;
            padding: 14px;
            display: block;
            height: 100%;
        }

        .profile-shortcut-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }

        .profile-shortcut-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .profile-shortcut-badge {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .profile-shortcut-badge.is-auth {
            border-color: rgba(64, 174, 255, .55);
            color: #bfe4ff;
        }

        .profile-shortcut-url {
            font-size: 13px;
            color: rgba(255, 255, 255, .68);
            word-break: break-all;
        }

        .profile-shortcut-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .profile-history-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .profile-history-list li {
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            padding: 16px 0;
        }

        .profile-history-list li:last-child {
            border-bottom: 0;
        }

        .profile-history-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .profile-history-kind {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .profile-history-kind.is-rank {
            border-color: rgba(77, 173, 255, .55);
            color: #c2e6ff;
        }

        .profile-history-kind.is-xp {
            border-color: rgba(91, 214, 143, .5);
            color: #d6ffe6;
        }

        .profile-history-story {
            margin: 0;
            line-height: 1.45;
        }

        .profile-history-extra {
            margin-top: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, .65);
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
        }

        .profile-security-card {
            margin-top: 30px;
        }

        .profile-security-note {
            margin: 0 0 14px;
            font-size: 13px;
            color: rgba(255, 255, 255, .66);
            line-height: 1.5;
        }

        .profile-security-logout .tt-btn,
        .profile-danger-form .tt-btn {
            width: 100%;
            justify-content: center;
        }

        .profile-danger-form .tt-form-group {
            margin-bottom: 14px;
        }

        .profile-danger-error {
            display: block;
            margin-bottom: 12px;
            color: #ff9f9f;
        }

        .profile-danger-trigger {
            width: 100%;
            justify-content: center;
        }

        .profile-danger-confirm {
            display: none;
            margin-top: 14px;
        }

        .profile-danger-confirm.is-open {
            display: block;
        }

        .profile-danger-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .profile-danger-buttons .tt-btn {
            flex: 1 1 180px;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .profile-form-card,
            .profile-side-card {
                padding: 20px;
            }

            .profile-kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $avatarUrl = $user->avatar_url ?: '/template/assets/img/logo.png';
        $isPublicApp = request()->routeIs('app.*');
        $historyRouteName = $isPublicApp ? 'app.profile.transactions' : 'profile.transactions';
        $deleteRouteName = $isPublicApp ? 'app.profile.destroy' : 'profile.destroy';
        $currentShortcuts = $currentShortcuts ?? [];
        $availableShortcuts = $availableShortcuts ?? [];
        $oldShortcutKeys = old('shortcuts');
        $selectedShortcutKeys = is_array($oldShortcutKeys)
            ? array_values(array_unique(array_map('strval', $oldShortcutKeys)))
            : array_column($currentShortcuts, 'key');
        $minShortcuts = (int) ($minShortcuts ?? 1);
        $maxShortcuts = (int) ($maxShortcuts ?? 5);
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                    <h1 class="ph-caption-title">Mon profil</h1>
                    <div class="ph-caption-description max-width-700">
                        Mettez a jour votre profil, vos reseaux et vos raccourcis personnels.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                        <h1 class="ph-caption-title">Mon profil</h1>
                        <div class="ph-caption-description max-width-700">
                            Mettez a jour votre profil, vos reseaux et vos raccourcis personnels.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Profile Settings - Profile Settings -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row tt-xl-row-reverse">
                    <div class="tt-col-xl-7">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="tt-form tt-form-creative tt-form-lg profile-form-card">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_profile_return" value="{{ request()->routeIs('app.*') ? 'app' : 'console' }}">

                            <h4 class="margin-bottom-10">Modifier mon profil</h4>
                            <small class="tt-form-text">Les champs modifies seront visibles sur votre profil public.</small>
                            <br>
                            <br>

                            <div class="tt-form-group">
                                <label for="name">Nom <span class="required">*</span></label>
                                <input class="tt-form-control" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="tt-form-group">
                                <label for="bio">Bio</label>
                                <textarea class="tt-form-control" id="bio" name="bio" rows="5">{{ old('bio', $user->bio) }}</textarea>
                            </div>

                            <div class="tt-form-group">
                                <label for="avatar">Photo de profil</label>
                                <input class="tt-form-control" id="avatar" name="avatar" type="file" accept="image/*">
                                <small class="tt-form-text">Formats: jpg, jpeg, png, webp - 4 Mo max.</small>
                            </div>

                            <div class="tt-row">
                                <div class="tt-col-lg-6">
                                    <div class="tt-form-group">
                                        <label for="twitter_url">Twitter / X</label>
                                        <input class="tt-form-control" id="twitter_url" name="twitter_url" type="url" value="{{ old('twitter_url', $user->twitter_url) }}" placeholder="https://x.com/...">
                                    </div>
                                </div>
                                <div class="tt-col-lg-6">
                                    <div class="tt-form-group">
                                        <label for="instagram_url">Instagram</label>
                                        <input class="tt-form-control" id="instagram_url" name="instagram_url" type="url" value="{{ old('instagram_url', $user->instagram_url) }}" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                                <div class="tt-col-lg-6">
                                    <div class="tt-form-group">
                                        <label for="tiktok_url">TikTok</label>
                                        <input class="tt-form-control" id="tiktok_url" name="tiktok_url" type="url" value="{{ old('tiktok_url', $user->tiktok_url) }}" placeholder="https://tiktok.com/...">
                                    </div>
                                </div>
                                <div class="tt-col-lg-6">
                                    <div class="tt-form-group">
                                        <label for="discord_url">Discord</label>
                                        <input class="tt-form-control" id="discord_url" name="discord_url" type="url" value="{{ old('discord_url', $user->discord_url) }}" placeholder="https://discord.gg/...">
                                    </div>
                                </div>
                            </div>

                            <div class="profile-inline-actions margin-top-30">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Mettre a jour">Mettre a jour</span>
                                </button>
                                <a href="{{ route('users.public', $user->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Voir profil public">Voir profil public</span>
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="tt-col-xl-5">
                        <div class="profile-side-card margin-bottom-30">
                            <img src="{{ $avatarUrl }}" alt="Avatar {{ $user->name }}" class="profile-avatar">
                            <h4 class="no-margin">{{ $user->name }}</h4>
                            <p class="tt-form-text no-margin">{{ $user->email }}</p>
                            <p class="tt-form-text margin-top-10">Role: {{ strtoupper((string) $user->role) }}</p>

                            <ul class="tt-list margin-top-20">
                                <li><strong>Ligue:</strong> {{ $progress->league->name ?? 'N/A' }}</li>
                                <li><strong>Rank points:</strong> {{ (int) ($progress->total_rank_points ?? 0) }}</li>
                                <li><strong>XP total:</strong> {{ (int) ($progress->xp_total ?? 0) }}</li>
                            </ul>

                            @if($user->twitter_url || $user->instagram_url || $user->tiktok_url || $user->discord_url)
                                <div class="tt-social-buttons margin-top-20">
                                    <ul>
                                        @if($user->twitter_url)
                                            <li><a href="{{ $user->twitter_url }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a></li>
                                        @endif
                                        @if($user->instagram_url)
                                            <li><a href="{{ $user->instagram_url }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="Instagram"><i class="fa-brands fa-instagram"></i></a></li>
                                        @endif
                                        @if($user->tiktok_url)
                                            <li><a href="{{ $user->tiktok_url }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="TikTok"><i class="fa-brands fa-tiktok"></i></a></li>
                                        @endif
                                        @if($user->discord_url)
                                            <li><a href="{{ $user->discord_url }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="Discord"><i class="fa-brands fa-discord"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="profile-kpi-grid">
                            <div class="profile-kpi-card">
                                <span class="profile-kpi-value">{{ (int) ($stats['likes'] ?? 0) }}</span>
                                <span class="tt-form-text">Likes</span>
                            </div>
                            <div class="profile-kpi-card">
                                <span class="profile-kpi-value">{{ (int) ($stats['comments'] ?? 0) }}</span>
                                <span class="tt-form-text">Commentaires</span>
                            </div>
                            <div class="profile-kpi-card">
                                <span class="profile-kpi-value">{{ (int) ($stats['duels'] ?? 0) }}</span>
                                <span class="tt-form-text">Duels</span>
                            </div>
                            <div class="profile-kpi-card">
                                <span class="profile-kpi-value">{{ (int) ($stats['bets'] ?? 0) }}</span>
                                <span class="tt-form-text">Bets</span>
                            </div>
                        </div>

                        <div class="profile-side-card profile-security-card">
                            <h5 class="margin-bottom-10">Session et securite</h5>
                            <p class="profile-security-note">
                                Se deconnecter de la session en cours ou supprimer votre compte de maniere definitive.
                            </p>

                            <form method="POST" action="{{ route('auth.logout') }}" class="profile-security-logout margin-bottom-20">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Se deconnecter">Se deconnecter</span>
                                </button>
                            </form>

                            @php
                                $deleteFormOpen = $errors->has('password');
                            @endphp
                            <div data-delete-account-block>
                                <button
                                    type="button"
                                    class="tt-btn tt-btn-primary tt-magnetic-item profile-danger-trigger"
                                    data-delete-open
                                    @if($deleteFormOpen) style="display:none;" @endif
                                >
                                    <span data-hover="Supprimer le compte">Supprimer le compte</span>
                                </button>

                                <form method="POST" action="{{ route($deleteRouteName) }}" class="tt-form profile-danger-form" onsubmit="return confirm('Confirmer la suppression definitive du compte ?');">
                                    @csrf
                                    @method('DELETE')

                                    <div class="profile-danger-confirm {{ $deleteFormOpen ? 'is-open' : '' }}" data-delete-confirm>
                                        <div class="tt-form-group">
                                            <label for="delete_password">Mot de passe actuel <span class="required">*</span></label>
                                            <input
                                                class="tt-form-control"
                                                id="delete_password"
                                                name="password"
                                                type="password"
                                                autocomplete="current-password"
                                                required
                                                data-delete-password
                                                @disabled(! $deleteFormOpen)
                                            >
                                        </div>

                                        @error('password')
                                            <small class="profile-danger-error">{{ $message }}</small>
                                        @enderror

                                        <div class="profile-danger-buttons">
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Confirmer la suppression">Confirmer la suppression</span>
                                            </button>
                                            <button type="button" class="tt-btn tt-btn-outline tt-magnetic-item" data-delete-cancel>
                                                <span data-hover="Annuler">Annuler</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top" id="profile-shortcuts">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-xl-5 margin-bottom-40">
                        <div class="tt-heading tt-heading-lg no-margin">
                            <h3 class="tt-heading-subtitle">Menu Plateforme</h3>
                            <h2 class="tt-heading-title">Mes raccourcis</h2>
                        </div>
                        <p class="tt-form-text margin-top-20">
                            Selectionnez entre {{ $minShortcuts }} et {{ $maxShortcuts }} raccourcis.
                        </p>
                        <div class="profile-inline-actions margin-top-20">
                            <span class="profile-shortcut-count">
                                Selection:
                                <strong id="profile-shortcuts-count">{{ count($selectedShortcutKeys) }}</strong> / {{ $maxShortcuts }}
                            </span>
                        </div>
                    </div>

                    <div class="tt-col-xl-7">
                        <form method="POST" action="{{ route('app.shortcuts.update') }}">
                            @csrf
                            <div class="tt-row">
                                @forelse($availableShortcuts as $shortcut)
                                    @php
                                        $shortcutKey = $shortcut['key'];
                                        $shortcutId = 'shortcut-'.$loop->index;
                                    @endphp
                                    <div class="tt-col-lg-6 margin-bottom-20">
                                        <label for="{{ $shortcutId }}" class="profile-shortcut-card">
                                            <span class="profile-shortcut-head">
                                                <span class="profile-shortcut-main">
                                                    <input
                                                        id="{{ $shortcutId }}"
                                                        type="checkbox"
                                                        name="shortcuts[]"
                                                        value="{{ $shortcutKey }}"
                                                        @checked(in_array($shortcutKey, $selectedShortcutKeys, true))
                                                        data-profile-shortcut-toggle
                                                    >
                                                    <span>{{ $shortcut['label'] }}</span>
                                                </span>
                                                <span class="profile-shortcut-badge {{ ($shortcut['requires_auth'] ?? false) ? 'is-auth' : '' }}">
                                                    {{ ($shortcut['requires_auth'] ?? false) ? 'auth' : 'public' }}
                                                </span>
                                            </span>
                                            <span class="profile-shortcut-url">{{ $shortcut['url'] }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <div class="tt-col-12">
                                        <p class="tt-form-text">Aucun raccourci disponible.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="profile-inline-actions margin-top-20">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Enregistrer les raccourcis">Enregistrer les raccourcis</span>
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('app.shortcuts.reset') }}" class="margin-top-20">
                            @csrf
                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Reinitialiser">Reinitialiser</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1000">
                <div class="tt-heading tt-heading-lg margin-bottom-20">
                    <h3 class="tt-heading-subtitle">Points</h3>
                    <h2 class="tt-heading-title">Histoires de transactions</h2>
                </div>
                <div class="profile-inline-actions margin-bottom-30">
                    <span class="tt-form-text">Affichage: 5 dernieres transactions.</span>
                    <a href="{{ route($historyRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Voir historique complet">Voir historique complet</span>
                    </a>
                </div>

                @if(($transactions ?? null) && $transactions->count())
                    <ul class="profile-history-list">
                        @foreach($transactions as $tx)
                            @php
                                $sourceMap = [
                                    'admin_grant' => 'attribution manuelle admin',
                                    'mission.daily' => 'mission quotidienne',
                                    'mission.weekly' => 'mission hebdomadaire',
                                    'duel.win' => 'duel remporte',
                                    'duel.loss' => 'duel termine',
                                    'bet.win' => 'pari gagne',
                                    'bet.refund' => 'remboursement de pari',
                                    'clip.like' => 'interaction sur clip',
                                    'clip.comment' => 'commentaire clip',
                                ];

                                $isRank = $tx->kind === \App\Models\PointsTransaction::KIND_RANK;
                                $kindLabel = $isRank ? 'Classement' : 'XP';
                                $kindClass = $isRank ? 'is-rank' : 'is-xp';
                                $pointsLabel = (int) $tx->points.' '.($isRank ? 'points de classement' : 'XP');
                                $sourceType = (string) $tx->source_type;
                                $sourceLabel = $sourceMap[$sourceType]
                                    ?? trim(ucwords(str_replace(['.', '_', ':'], ' ', $sourceType)));
                                $beforeValue = $isRank ? (int) $tx->before_rank_points : (int) $tx->before_xp;
                                $afterValue = $isRank ? (int) $tx->after_rank_points : (int) $tx->after_xp;
                                $story = 'Vous avez gagne '.$pointsLabel.' via '.$sourceLabel.'.';
                                $meta = is_array($tx->meta) ? $tx->meta : [];
                                $metaParts = [];

                                foreach ($meta as $metaKey => $metaValue) {
                                    if (is_scalar($metaValue)) {
                                        $metaParts[] = str_replace('_', ' ', (string) $metaKey).': '.$metaValue;
                                    }
                                }

                                $metaPreview = implode(' - ', array_slice($metaParts, 0, 2));
                            @endphp
                            <li>
                                <div class="profile-history-head">
                                    <span class="profile-history-kind {{ $kindClass }}">{{ $kindLabel }}</span>
                                    <span class="tt-form-text">{{ optional($tx->created_at)->format('d/m/Y H:i') }}</span>
                                </div>

                                <p class="profile-history-story">{{ $story }}</p>

                                <div class="profile-history-extra">
                                    <span>Avant: <strong>{{ $beforeValue }}</strong></span>
                                    <span>Apres: <strong>{{ $afterValue }}</strong></span>
                                    @if($metaPreview !== '')
                                        <span>{{ $metaPreview }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="tt-form-text">Aucune transaction de points.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
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
    <script>
        (function () {
            var shortcutsScope = document.getElementById('profile-shortcuts');
            if (!shortcutsScope) return;

            var toggles = Array.from(shortcutsScope.querySelectorAll('[data-profile-shortcut-toggle]'));
            var countEl = shortcutsScope.querySelector('#profile-shortcuts-count');
            var minCount = {{ (int) ($minShortcuts ?? 1) }};
            var maxCount = {{ (int) ($maxShortcuts ?? 5) }};

            function updateCount() {
                var checkedCount = toggles.filter(function (el) { return el.checked; }).length;

                if (countEl) {
                    countEl.textContent = String(checkedCount);
                }

                toggles.forEach(function (toggle) {
                    toggle.disabled = !toggle.checked && checkedCount >= maxCount;
                });
            }

            toggles.forEach(function (toggle) {
                toggle.addEventListener('change', updateCount);
            });

            var updateForm = shortcutsScope.querySelector('form[action="{{ route('app.shortcuts.update') }}"]');
            if (updateForm) {
                updateForm.addEventListener('submit', function (event) {
                    var checkedCount = toggles.filter(function (el) { return el.checked; }).length;
                    if (checkedCount < minCount || checkedCount > maxCount) {
                        event.preventDefault();
                        alert('Selection invalide: minimum ' + minCount + ', maximum ' + maxCount + '.');
                    }
                });
            }

            updateCount();
        })();

        (function () {
            var deleteBlock = document.querySelector('[data-delete-account-block]');
            if (!deleteBlock) return;

            var openButton = deleteBlock.querySelector('[data-delete-open]');
            var cancelButton = deleteBlock.querySelector('[data-delete-cancel]');
            var confirmBlock = deleteBlock.querySelector('[data-delete-confirm]');
            var passwordInput = deleteBlock.querySelector('[data-delete-password]');

            function openDeleteForm() {
                if (confirmBlock) {
                    confirmBlock.classList.add('is-open');
                }

                if (openButton) {
                    openButton.style.display = 'none';
                }

                if (passwordInput) {
                    passwordInput.disabled = false;
                    passwordInput.focus();
                }
            }

            function closeDeleteForm() {
                if (confirmBlock) {
                    confirmBlock.classList.remove('is-open');
                }

                if (openButton) {
                    openButton.style.display = '';
                }

                if (passwordInput) {
                    passwordInput.value = '';
                    passwordInput.disabled = true;
                }
            }

            if (openButton) {
                openButton.addEventListener('click', openDeleteForm);
            }

            if (cancelButton) {
                cancelButton.addEventListener('click', closeDeleteForm);
            }

            if (confirmBlock && confirmBlock.classList.contains('is-open') && passwordInput) {
                passwordInput.disabled = false;
            }
        })();
    </script>
@endsection
