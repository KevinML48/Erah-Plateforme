@extends('marketing.layouts.template')

@section('title', 'Admin Avis | ERAH Plateforme')
@section('meta_description', 'Moderation des avis membres et historiques du club.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-review-list {
            display: grid;
            gap: 14px;
        }

        .adm-review-card {
            border: 1px solid var(--adm-border);
            border-radius: 22px;
            padding: 20px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                var(--adm-surface-bg);
            display: grid;
            gap: 16px;
        }

        .adm-review-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: start;
        }

        .adm-review-title {
            margin: 0;
            font-size: clamp(24px, 3vw, 38px);
            line-height: .98;
            color: var(--adm-text);
        }

        .adm-review-subtitle {
            margin: 8px 0 0;
            color: var(--adm-text-soft);
        }

        .adm-review-content {
            margin: 0;
            color: var(--adm-text);
            line-height: 1.65;
            font-size: 16px;
        }

        .adm-review-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .adm-review-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 6px;
        }

        .adm-review-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--adm-text-soft);
        }

        .adm-review-meta strong {
            font-size: 15px;
            line-height: 1.35;
            color: var(--adm-text);
        }

        .adm-review-filters {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(180px, 1fr));
            gap: 12px;
        }

        @media (max-width: 1199.98px) {
            .adm-review-filters,
            .adm-review-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .adm-review-head,
            .adm-review-filters,
            .adm-review-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Moderation des avis',
        'heroDescription' => 'Publier, masquer et trier les avis membres ou historiques sans casser la section home.',
        'heroMaskDescription' => 'Une moderation simple pour garder des avis visibles, propres et faciles a valoriser sur la home.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue d ensemble</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Les avis publies alimentent la home, la page publique complète et la mise en avant des profils membres.</p>
                        </div>

                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['total'] }}</strong><span>Total avis</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['published'] }}</strong><span>Publies</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['hidden'] }}</strong><span>Masques</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['members'] }}</strong><span>Avis membres</span></article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtres moderation</h2>
                        </div>

                        <form method="GET" action="{{ route('admin.reviews.index') }}" class="adm-form">
                            <div class="adm-review-filters">
                                <div class="tt-form-group">
                                    <label for="review-q">Recherche</label>
                                    <input class="tt-form-control" id="review-q" name="q" type="text" value="{{ $search }}" placeholder="Auteur, email ou extrait d avis">
                                </div>
                                <div class="tt-form-group">
                                    <label for="review-status">Statut</label>
                                    <select class="tt-form-control" id="review-status" name="status">
                                        <option value="all">Tous</option>
                                        @foreach($statusLabels as $statusKey => $statusLabel)
                                            <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="review-source">Source</label>
                                    <select class="tt-form-control" id="review-source" name="source">
                                        <option value="all">Toutes</option>
                                        @foreach($sourceLabels as $sourceKey => $sourceLabel)
                                            <option value="{{ $sourceKey }}" @selected($source === $sourceKey)>{{ $sourceLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="review-sort">Tri</label>
                                    <select class="tt-form-control" id="review-sort" name="sort">
                                        <option value="latest" @selected($sort === 'latest')>Plus recents</option>
                                        <option value="oldest" @selected($sort === 'oldest')>Plus anciens</option>
                                        <option value="published" @selected($sort === 'published')>Publication recente</option>
                                    </select>
                                </div>
                            </div>

                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Appliquer">Appliquer</span>
                                </button>
                                <a href="{{ route('admin.reviews.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Reinitialiser">Reinitialiser</span>
                                </a>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Bibliotheque avis</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Chaque carte resume l auteur, la source, la visibilite et les actions rapides de moderation.</p>
                        </div>

                        @if($reviews->count())
                            <div class="adm-review-list">
                                @foreach($reviews as $review)
                                    <article id="review-{{ $review->id }}" class="adm-review-card tt-anim-fadeinup">
                                        <div class="adm-review-head">
                                            <div>
                                                <div class="adm-row-actions margin-bottom-10">
                                                    <span class="adm-pill">{{ $review->statusLabel() }}</span>
                                                    <span class="adm-pill">{{ $review->sourceLabel() }}</span>
                                                    @if($review->user)
                                                        <span class="adm-pill">{{ $review->user->progress?->league?->name ?? 'Membre ERAH' }}</span>
                                                    @endif
                                                </div>

                                                <h3 class="adm-review-title">{{ $review->authorDisplayName() }}</h3>
                                                <p class="adm-review-subtitle">
                                                    @if($review->user)
                                                        {{ $review->user->email }} · <a href="{{ route('users.public', $review->user) }}" target="_blank" rel="noopener">Profil public</a>
                                                    @elseif($review->author_profile_url)
                                                        <a href="{{ $review->author_profile_url }}" target="_blank" rel="noopener">Source externe</a>
                                                    @else
                                                        Auteur externe sans lien
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="adm-row-actions">
                                                @if($review->status !== \App\Models\ClubReview::STATUS_PUBLISHED)
                                                    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ \App\Models\ClubReview::STATUS_PUBLISHED }}">
                                                        <input type="hidden" name="is_featured" value="{{ $review->is_featured ? 1 : 0 }}">
                                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                            <span data-hover="Publier">Publier</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($review->status !== \App\Models\ClubReview::STATUS_HIDDEN)
                                                    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ \App\Models\ClubReview::STATUS_HIDDEN }}">
                                                        <input type="hidden" name="is_featured" value="{{ $review->is_featured ? 1 : 0 }}">
                                                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                            <span data-hover="Masquer">Masquer</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer definitivement cet avis ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                        <span data-hover="Supprimer">Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <p class="adm-review-content">{{ $review->content }}</p>

                                        <div class="adm-review-grid">
                                            <article class="adm-review-meta">
                                                <span>Creation</span>
                                                <strong>{{ optional($review->created_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                            </article>
                                            <article class="adm-review-meta">
                                                <span>Publication</span>
                                                <strong>{{ optional($review->published_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                            </article>
                                            <article class="adm-review-meta">
                                                <span>Supporter</span>
                                                <strong>{{ $review->user?->isSupporterActive() ? 'Oui' : 'Non' }}</strong>
                                            </article>
                                            <article class="adm-review-meta">
                                                <span>Classement</span>
                                                <strong>{{ $review->user?->progress ? number_format((int) $review->user->progress->total_rank_points, 0, ',', ' ') . ' pts' : '-' }}</strong>
                                            </article>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $reviews->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun avis pour ce filtre.</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
