@extends('marketing.layouts.template')

@section('title', $orderNumber.' | Commande cadeau')
@section('meta_description', 'Detail et suivi de votre commande cadeau ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-order-detail-page {
        --gift-order-border: rgba(255, 255, 255, 0.12);
        --gift-order-muted: rgba(255, 255, 255, 0.72);
    }

    .gift-order-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.55fr);
        gap: 22px;
    }

    .gift-order-card {
        padding: 24px;
        border: 1px solid var(--gift-order-border);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-order-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 12px;
    }

    .gift-order-head h2 {
        margin: 0;
        color: #fff;
        font-size: clamp(30px, 3vw, 48px);
        line-height: 0.95;
    }

    .gift-order-status {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 7px 12px;
        border-radius: 999px;
        border: 1px solid var(--gift-order-border);
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .gift-order-status.is-approved { border-color: rgba(90, 179, 61, 0.38); background: rgba(90, 179, 61, 0.16); }
    .gift-order-status.is-shipped { border-color: rgba(25, 137, 217, 0.35); background: rgba(25, 137, 217, 0.16); }
    .gift-order-status.is-delivered { border-color: rgba(111, 196, 85, 0.38); background: rgba(111, 196, 85, 0.19); }
    .gift-order-status.is-rejected { border-color: rgba(214, 78, 77, 0.35); background: rgba(214, 78, 77, 0.16); }
    .gift-order-status.is-cancelled { border-color: rgba(143, 143, 143, 0.35); background: rgba(143, 143, 143, 0.16); }

    .gift-order-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .gift-order-meta-item {
        padding: 14px 16px;
        border: 1px solid var(--gift-order-border);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-order-meta-item span {
        display: block;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, 0.58);
        font-size: 11px;
        letter-spacing: 0.11em;
        text-transform: uppercase;
    }

    .gift-order-meta-item strong {
        color: #fff;
        font-size: 16px;
    }

    .gift-order-info {
        margin-top: 20px;
        padding: 16px;
        border: 1px solid var(--gift-order-border);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-order-info p {
        margin: 0;
        color: var(--gift-order-muted);
        line-height: 1.7;
    }

    .gift-order-timeline {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .gift-order-step {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        padding: 12px 14px;
        border: 1px solid var(--gift-order-border);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-order-step-dot {
        width: 10px;
        height: 10px;
        margin-top: 7px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.32);
    }

    .gift-order-step strong {
        display: block;
        margin-bottom: 4px;
        color: #fff;
        font-size: 15px;
    }

    .gift-order-step p {
        margin: 0;
        color: var(--gift-order-muted);
        font-size: 13px;
    }

    .gift-order-step.is-completed .gift-order-step-dot { background: #66c450; }
    .gift-order-step.is-current .gift-order-step-dot { background: #ea5252; box-shadow: 0 0 0 4px rgba(234, 82, 82, 0.2); }
    .gift-order-step.is-skipped .gift-order-step-dot { background: rgba(255, 255, 255, 0.16); }

    .gift-order-events {
        margin-top: 22px;
        display: grid;
        gap: 12px;
    }

    .gift-order-event {
        padding: 14px 15px;
        border: 1px solid var(--gift-order-border);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-order-event-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 6px;
    }

    .gift-order-event-head strong {
        color: #fff;
        font-size: 15px;
    }

    .gift-order-event-head span {
        color: rgba(255, 255, 255, 0.58);
        font-size: 12px;
    }

    .gift-order-event p {
        margin: 0;
        color: var(--gift-order-muted);
        line-height: 1.65;
    }

    body.tt-lightmode-on .gift-order-detail-page {
        --gift-order-border: rgba(148, 163, 184, 0.24);
        --gift-order-muted: rgba(51, 65, 85, 0.78);
    }

    body.tt-lightmode-on .gift-order-card,
    body.tt-lightmode-on .gift-order-meta-item,
    body.tt-lightmode-on .gift-order-info,
    body.tt-lightmode-on .gift-order-step,
    body.tt-lightmode-on .gift-order-event {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(248, 250, 252, 0.88));
        box-shadow: 0 18px 42px rgba(148, 163, 184, 0.16);
    }

    body.tt-lightmode-on .gift-order-head h2,
    body.tt-lightmode-on .gift-order-meta-item strong,
    body.tt-lightmode-on .gift-order-step strong,
    body.tt-lightmode-on .gift-order-event-head strong {
        color: #0f172a;
    }

    body.tt-lightmode-on .gift-order-status {
        background: rgba(255, 255, 255, 0.88);
        color: #0f172a;
    }

    @media (max-width: 1199.98px) {
        .gift-order-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .gift-order-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <div class="gift-order-detail-page">
        <div id="page-header" class="ph-full ph-center ph-cap-xxlg ph-caption-parallax">
            <div class="page-header-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Commande cadeau</h2>
                        <h1 class="ph-caption-title">{{ $orderNumber }}</h1>
                        <div class="ph-caption-description max-width-700">
                            Detail complet, timeline et historique de votre demande cadeau.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tt-page-content">
            <div class="tt-section padding-top-80 padding-bottom-100 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    <div class="gift-order-layout">
                        <section class="gift-order-card">
                            <div class="gift-order-head">
                                <h2>{{ $orderNumber }}</h2>
                                <span class="gift-order-status is-{{ $redemption->status }}">{{ $statusLabel }}</span>
                            </div>

                            <div class="gift-order-meta-grid">
                                <div class="gift-order-meta-item">
                                    <span>Date de demande</span>
                                    <strong>{{ optional($redemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                </div>
                                <div class="gift-order-meta-item">
                                    <span>Cadeau</span>
                                    <strong>{{ $redemption->gift->title ?? 'Cadeau' }}</strong>
                                </div>
                                <div class="gift-order-meta-item">
                                    <span>Cout</span>
                                    <strong>{{ (int) $redemption->cost_points_snapshot }} pts</strong>
                                </div>
                                <div class="gift-order-meta-item">
                                    <span>Statut actuel</span>
                                    <strong>{{ $statusLabel }}</strong>
                                </div>
                            </div>

                            @if($redemption->status === 'rejected' && $redemption->reason)
                                <div class="gift-order-info">
                                    <p><strong>Motif du rejet:</strong> {{ $redemption->reason }}</p>
                                </div>
                            @endif

                            @if($redemption->tracking_code || $redemption->tracking_carrier || $redemption->shipping_note)
                                <div class="gift-order-info">
                                    <p>
                                        <strong>Expedition:</strong>
                                        @if($redemption->tracking_code)
                                            Tracking {{ $redemption->tracking_code }}
                                        @else
                                            Tracking non renseigne
                                        @endif
                                        @if($redemption->tracking_carrier)
                                            - Transporteur {{ $redemption->tracking_carrier }}
                                        @endif
                                        @if($trackingUrl)
                                            - <a href="{{ $trackingUrl }}" target="_blank" rel="noopener">Ouvrir le suivi colis</a>
                                        @endif
                                        @if($redemption->shipping_note)
                                            <br>Note: {{ $redemption->shipping_note }}
                                        @endif
                                    </p>
                                </div>
                            @endif

                            <div class="gift-order-info">
                                <p>
                                    Le suivi est mis a jour automatiquement a chaque action admin (validation, expedition, livraison, rejet).
                                </p>
                            </div>

                            <div class="gift-order-events">
                                <div class="tt-heading tt-heading-sm no-margin">
                                    <h3 class="tt-heading-title">Historique complet</h3>
                                </div>

                                @forelse($timelineEvents as $event)
                                    <article class="gift-order-event">
                                        <div class="gift-order-event-head">
                                            <strong>{{ $event['title'] }}</strong>
                                            <span>{{ $event['happened_at'] ?? '-' }}</span>
                                        </div>
                                        <p>{{ $event['summary'] }}</p>
                                        <p class="margin-top-10">Par: {{ $event['actor'] }}</p>
                                    </article>
                                @empty
                                    <article class="gift-order-event">
                                        <p>Aucun evenement enregistre pour le moment sur cette commande.</p>
                                    </article>
                                @endforelse
                            </div>
                        </section>

                        <aside class="gift-order-card">
                            <div class="tt-heading tt-heading-sm no-margin">
                                <h3 class="tt-heading-title">Timeline commande</h3>
                            </div>

                            <div class="gift-order-timeline">
                                @foreach($timelineSteps as $step)
                                    <div class="gift-order-step is-{{ $step['state'] }}">
                                        <span class="gift-order-step-dot" aria-hidden="true"></span>
                                        <div>
                                            <strong>{{ $step['label'] }}</strong>
                                            <p>{{ $step['at'] ?: 'En attente de mise a jour' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="gift-order-info margin-top-20">
                                <p>Besoin d une autre commande? Retournez au catalogue cadeaux pour lancer une nouvelle demande.</p>
                            </div>

                            <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-secondary margin-top-20">
                                <span data-hover="Catalogue cadeaux">Retour au catalogue</span>
                            </a>
                            <a href="{{ route('gifts.redemptions') }}" class="tt-btn tt-btn-outline margin-top-10">
                                <span data-hover="Mes commandes cadeaux">Retour a mes commandes</span>
                            </a>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="/template/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/template/assets/vendor/gsap/gsap.min.js"></script>
    <script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js"></script>
    <script src="/template/assets/vendor/gsap/ScrollTrigger.min.js"></script>
    <script src="/template/assets/vendor/lenis.min.js"></script>
    <script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js"></script>
    <script src="/template/assets/vendor/isotope/isotope.pkgd.min.js"></script>
    <script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js"></script>
    <script src="/template/assets/vendor/fancybox/js/fancybox.umd.js"></script>
    <script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js"></script>
    <script src="/template/assets/js/theme.js"></script>
@endsection
