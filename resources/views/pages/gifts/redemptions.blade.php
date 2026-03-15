@extends('marketing.layouts.template')

@section('title', 'Mes commandes cadeaux | ERAH')
@section('meta_description', 'Historique et suivi de vos commandes cadeaux ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-orders-page {
        --gift-orders-border: rgba(255, 255, 255, 0.12);
        --gift-orders-muted: rgba(255, 255, 255, 0.72);
    }

    .gift-orders-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(200px, 0.45fr) auto;
        gap: 12px;
        align-items: center;
        margin: 20px 0 30px;
        padding: 18px;
        border: 1px solid var(--gift-orders-border);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-orders-toolbar input,
    .gift-orders-toolbar select {
        min-height: 42px;
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--gift-orders-border);
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.2);
        color: #fff;
    }

    .gift-orders-toolbar select option {
        color: #111;
    }

    .gift-order-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--gift-orders-border);
        border-radius: 18px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.02);
    }

    .gift-order-table th,
    .gift-order-table td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        text-align: left;
        color: #fff;
        vertical-align: middle;
    }

    .gift-order-table th {
        color: rgba(255, 255, 255, 0.68);
        font-size: 11px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .gift-order-table tr:last-child td {
        border-bottom: none;
    }

    .gift-order-status {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 6px 11px;
        border-radius: 999px;
        border: 1px solid var(--gift-orders-border);
        background: rgba(255, 255, 255, 0.04);
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

    .gift-order-meta {
        display: block;
        margin-top: 5px;
        color: var(--gift-orders-muted);
        font-size: 13px;
    }

    .gift-orders-empty {
        padding: 24px;
        border: 1px dashed var(--gift-orders-border);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.02);
        color: var(--gift-orders-muted);
    }

    body.tt-lightmode-on .gift-orders-page {
        --gift-orders-border: rgba(148, 163, 184, 0.24);
        --gift-orders-muted: rgba(51, 65, 85, 0.78);
    }

    body.tt-lightmode-on .gift-orders-toolbar {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(248, 250, 252, 0.88));
        box-shadow: 0 18px 42px rgba(148, 163, 184, 0.16);
    }

    body.tt-lightmode-on .gift-orders-toolbar input,
    body.tt-lightmode-on .gift-orders-toolbar select,
    body.tt-lightmode-on .gift-order-table,
    body.tt-lightmode-on .gift-orders-empty {
        background: rgba(255, 255, 255, 0.94);
    }

    body.tt-lightmode-on .gift-order-table th {
        color: rgba(71, 85, 105, 0.82);
    }

    body.tt-lightmode-on .gift-order-status {
        background: rgba(255, 255, 255, 0.88);
        color: #0f172a;
    }

    @media (max-width: 991.98px) {
        .gift-orders-toolbar {
            grid-template-columns: 1fr;
        }

        .gift-orders-toolbar .tt-btn {
            width: 100%;
        }

        .gift-order-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }

    @media (max-width: 767.98px) {
        .gift-order-table {
            display: flex;
            flex-direction: column;
            gap: 14px;
            overflow: visible;
            white-space: normal;
        }

        .gift-order-table thead {
            display: none;
        }

        .gift-order-table tbody {
            display: contents;
        }

        .gift-order-table tr {
            display: block;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.02);
        }

        .gift-order-table td {
            display: block;
            width: 100%;
            border: none;
            padding: 8px 0;
            margin: 0;
        }

        .gift-order-table td::before {
            content: attr(data-label);
            font-weight: 600;
            display: block;
            color: rgba(255, 255, 255, 0.64);
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .gift-order-status {
            display: inline-flex;
        }
    }
</style>
@endsection

@section('content')
    <div class="gift-orders-page">
        <div id="page-header" class="ph-full ph-center ph-cap-xxlg ph-caption-parallax">
            <div class="page-header-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Cadeaux</h2>
                        <h1 class="ph-caption-title">Mes commandes cadeaux</h1>
                        <div class="ph-caption-description max-width-700">
                            Suivez vos demandes, leur progression et les informations de livraison.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tt-page-content">
            <div class="tt-section padding-top-80 padding-bottom-100 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    <form class="gift-orders-toolbar" method="GET" action="{{ route('gifts.redemptions') }}">
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchTerm ?? '' }}"
                            placeholder="Rechercher une commande, tracking ou cadeau"
                            aria-label="Rechercher mes commandes cadeaux"
                        >

                        <select name="status" aria-label="Filtrer par statut">
                            <option value="all" @selected(($selectedStatus ?? 'all') === 'all')>Tous les statuts</option>
                            @foreach(($statuses ?? []) as $status)
                                <option value="{{ $status }}" @selected(($selectedStatus ?? 'all') === $status)>
                                    {{ $statusLabels[$status] ?? \Illuminate\Support\Str::headline((string) $status) }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                            <span data-hover="Filtrer">Filtrer</span>
                        </button>
                    </form>

                    @if(($redemptions ?? null) && $redemptions->count())
                        <table class="gift-order-table">
                            <thead>
                                <tr>
                                    <th>Commande</th>
                                    <th>Cadeau</th>
                                    <th>Date demande</th>
                                    <th>Statut</th>
                                    <th>Cout</th>
                                    <th>Tracking</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($redemptions as $redemption)
                                    @php
                                        $orderNumber = 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT);
                                        $status = (string) $redemption->status;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $orderNumber }}</strong>
                                            <span class="gift-order-meta">ID interne #{{ $redemption->id }}</span>
                                        </td>
                                        <td>{{ $redemption->gift->title ?? 'Cadeau' }}</td>
                                        <td>{{ optional($redemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                        <td>
                                            <span class="gift-order-status is-{{ $status }}">
                                                {{ $statusLabels[$status] ?? \Illuminate\Support\Str::headline($status) }}
                                            </span>
                                        </td>
                                        <td>{{ (int) $redemption->cost_points_snapshot }} pts</td>
                                        <td>
                                            @if($redemption->tracking_code)
                                                {{ $redemption->tracking_code }}
                                                @if($redemption->tracking_carrier)
                                                    <span class="gift-order-meta">{{ $redemption->tracking_carrier }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('gifts.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline">
                                                <span data-hover="Detail">Detail</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="margin-top-30">
                            {{ $redemptions->links() }}
                        </div>
                    @else
                        <div class="gift-orders-empty">
                            Aucune commande cadeau ne correspond a ce filtre pour le moment.
                        </div>
                    @endif
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
