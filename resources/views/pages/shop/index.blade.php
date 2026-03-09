@extends('marketing.layouts.template')

@section('title', 'Boutique | ERAH Plateforme')
@section('meta_description', 'Boutique communautaire ERAH: badges, avatars, bordures et boosts.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Store</h2>
                    <h1 class="ph-caption-title">Boutique</h1>
                    <div class="ph-caption-description max-width-700">Depensez vos points communautaires dans les badges, bordures, avatars et boosts de progression.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Catalogue boutique</h1>
                        <p>La boutique repose sur les points communautaires deja utilises pour les cadeaux, codes live, missions et clips. Les achats sont immediatement historises.</p>
                    </div>
                </div>

                <section class="community-grid">
                    @foreach($items as $item)
                        <article class="community-card">
                            <div class="community-meta">
                                <span class="community-pill">{{ strtoupper($item->type) }}</span>
                                @if($item->is_featured)
                                    <span class="community-pill">Featured</span>
                                @endif
                            </div>
                            <h3>{{ $item->name }}</h3>
                            <p class="no-margin">{{ $item->description }}</p>
                            <div class="community-meta">
                                <span class="community-pill">{{ (int) $item->cost_points }} points</span>
                                <span class="community-pill">{{ $item->stock === null ? 'Stock illimite' : 'Stock '.$item->stock }}</span>
                            </div>
                            <form method="POST" action="{{ request()->routeIs('app.*') ? route('app.shop.purchase', $item->id) : route('shop.purchase', $item->id) }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Acheter">Acheter</span>
                                </button>
                            </form>
                        </article>
                    @endforeach
                </section>

                <div class="margin-top-30">{{ $items->links() }}</div>

                <section class="community-surface">
                    <h3 class="no-margin">Mes derniers achats</h3>
                    @if($purchases->count())
                        <table class="community-table margin-top-20">
                            <thead><tr><th>Article</th><th>Date</th><th>Cout</th><th>Statut</th></tr></thead>
                            <tbody>
                                @foreach($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->shopItem?->name }}</td>
                                        <td>{{ optional($purchase->purchased_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ (int) $purchase->cost_points }} pts</td>
                                        <td>{{ $purchase->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="community-empty margin-top-20">Aucun achat enregistre.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
