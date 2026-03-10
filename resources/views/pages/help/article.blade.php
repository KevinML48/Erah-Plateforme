@extends('marketing.layouts.template')

@section('title', ($page['article']['title'] ?? 'Article aide').' | ERAH Esport')
@section('meta_description', $page['article']['summary'] ?? ($page['article']['short_answer'] ?? "Article d'aide ERAH"))
@section('body_class', 'tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">{{ $page['article']['category']['title'] ?? 'Centre d aide' }}</h2>
                    <h1 class="ph-caption-title">{{ $page['article']['title'] }}</h1>
                    <div class="ph-caption-description max-width-700">{{ $page['article']['summary'] ?: $page['article']['short_answer'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-lg-80 padding-bottom-lg-60 padding-bottom-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-lg-8">
                        @foreach (preg_split("/\n{2,}/", (string) ($page['article']['body'] ?? '')) as $paragraph)
                            @php($paragraph = trim($paragraph))
                            @if ($paragraph !== '')
                                <p class="margin-bottom-20">{{ $paragraph }}</p>
                            @endif
                        @endforeach
                    </div>
                    <div class="tt-col-lg-4">
                        <div class="tt-heading tt-heading-sm margin-bottom-15">
                            <h3 class="tt-heading-title">Reponse courte</h3>
                        </div>
                        <p class="text-muted">{{ $page['article']['short_answer'] ?: $page['article']['summary'] }}</p>

                        <div class="tt-btn-wrap margin-top-20 margin-bottom-15">
                            <a href="{{ route('help.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour">Centre d aide</span>
                            </a>
                        </div>

                        @if (! empty($page['article']['category']))
                            <div class="tt-btn-wrap margin-bottom-15">
                                <a href="{{ route('help.categories.show', $page['article']['category']['slug']) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Categorie">{{ $page['article']['category']['title'] }}</span>
                                </a>
                            </div>
                        @endif

                        @if (! empty($page['article']['cta_url']))
                            <div class="tt-btn-wrap">
                                <a href="{{ $page['article']['cta_url'] }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Ouvrir">{{ $page['article']['cta_label'] ?: 'Ouvrir la page liee' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (! empty($page['relatedArticles']))
            <div class="tt-section padding-top-lg-20 padding-bottom-lg-80 padding-bottom-60 border-top">
                <div class="tt-section-inner tt-wrap">
                    <div class="tt-heading tt-heading-lg margin-bottom-50">
                        <h3 class="tt-heading-subtitle tt-text-uppercase">Articles lies</h3>
                        <h2 class="tt-heading-title">Continuer la lecture</h2>
                    </div>
                    <div class="tt-grid ttgr-layout-2 ttgr-gap-3">
                        @foreach ($page['relatedArticles'] as $article)
                            <div class="tt-grid-item">
                                <div class="ttgr-item-inner">
                                    <div class="tt-heading tt-heading-sm margin-bottom-15">
                                        <h3 class="tt-heading-title">{{ $article['title'] }}</h3>
                                    </div>
                                    <p class="text-muted">{{ $article['summary'] ?: $article['short_answer'] }}</p>
                                    <div class="tt-btn-wrap margin-top-20">
                                        <a href="{{ $article['url'] }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                            <span data-hover="Lire">Lire l article</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection
