@extends('marketing.layouts.template')

@section('title', ($page['category']['title'] ?? 'Categorie aide').' | ERAH Esport')
@section('meta_description', $page['category']['description'] ?? "Categorie d'aide ERAH")
@section('body_class', 'tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Categorie d aide</h2>
                    <h1 class="ph-caption-title">{{ $page['category']['title'] }}</h1>
                    <div class="ph-caption-description max-width-700">{{ $page['category']['description'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-lg-80 padding-bottom-lg-60 padding-bottom-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-lg-8">
                        <p class="text-muted">{{ $page['category']['intro'] ?: $page['category']['description'] }}</p>
                    </div>
                    <div class="tt-col-lg-4">
                        <div class="tt-btn-wrap margin-bottom-15">
                            <a href="{{ route('help.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour">Centre d aide</span>
                            </a>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ $page['category']['url'] }}" class="margin-top-40">
                    <div class="tt-form-group">
                        <label>Recherche dans cette categorie</label>
                        <input type="text" name="search" value="{{ $page['filters']['search'] ?? '' }}" placeholder="Chercher une question dans cette categorie">
                    </div>
                    <div class="tt-btn-wrap margin-top-20">
                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Chercher">Rechercher</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tt-section padding-top-lg-20 padding-bottom-lg-80 padding-bottom-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-grid ttgr-layout-2 ttgr-gap-3">
                    @foreach ($page['articles']['data'] as $article)
                        <div class="tt-grid-item">
                            <div class="ttgr-item-inner">
                                <div class="tt-heading tt-heading-sm margin-bottom-15">
                                    <h3 class="tt-heading-title">{{ $article['title'] }}</h3>
                                </div>
                                <p class="text-muted">{{ $article['summary'] ?: $article['short_answer'] }}</p>
                                <div class="tt-btn-wrap margin-top-20">
                                    <a href="{{ $article['url'] }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                        <span data-hover="Lire">Ouvrir l article</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($page['articles']['links']['prev'] || $page['articles']['links']['next'])
                    <div class="margin-top-40">
                        @if ($page['articles']['links']['prev'])
                            <div class="tt-btn-wrap margin-bottom-15">
                                <a href="{{ $page['articles']['links']['prev'] }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Retour">Page précédente</span>
                                </a>
                            </div>
                        @endif
                        @if ($page['articles']['links']['next'])
                            <div class="tt-btn-wrap">
                                <a href="{{ $page['articles']['links']['next'] }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Suivant">Page suivante</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection
