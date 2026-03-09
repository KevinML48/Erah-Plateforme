@extends('marketing.layouts.template')

@section('title', 'Codes live | ERAH Plateforme')
@section('meta_description', 'Codes live communautaires ERAH, recompenses et historique des redemptions.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Live</h2>
                    <h1 class="ph-caption-title">Codes live</h1>
                    <div class="ph-caption-description max-width-700">Saisissez les codes diffuses pendant les evenements et debloquez des bonus instantanes.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Codes live</h1>
                        <p>Chaque code peut etre limite en usages, dans le temps et rattache a une mission. Les derniers codes publies restent visibles tant qu ils sont actifs.</p>
                    </div>
                </div>

                <section class="community-surface">
                    <form method="POST" action="{{ request()->routeIs('app.*') ? route('app.live-codes.redeem') : route('live-codes.redeem') }}" class="tt-form community-form-grid">
                        @csrf
                        <div class="full tt-form-group">
                            <label for="code">Entrer un code live</label>
                            <input type="text" id="code" name="code" class="tt-form-control" maxlength="60" placeholder="ERAH2026">
                        </div>
                        <div class="full">
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Redeem">Redeem</span>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="community-grid margin-top-30">
                    @foreach($codes as $code)
                        <article class="community-card">
                            <div class="community-meta">
                                <span class="community-pill">{{ strtoupper($code->status) }}</span>
                                <span class="community-pill">{{ (int) $code->redemptions_count }} usage(s)</span>
                            </div>
                            <h3>{{ $code->label }}</h3>
                            <p class="no-margin">{{ $code->description ?: 'Code live deploye pendant un event ERAH.' }}</p>
                            <div class="community-meta">
                                <span class="community-pill">+{{ (int) $code->xp_reward }} XP</span>
                                <span class="community-pill">+{{ (int) $code->reward_points }} points</span>
                                <span class="community-pill">+{{ (int) $code->bet_points }} bet points</span>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="margin-top-30">{{ $codes->links() }}</div>

                <section class="community-surface">
                    <h3 class="no-margin">Mes redemptions recentes</h3>
                    @if($myRedemptions->count())
                        <table class="community-table margin-top-20">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Recompenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myRedemptions as $redemption)
                                    <tr>
                                        <td>{{ $redemption->liveCode?->code }}</td>
                                        <td>{{ optional($redemption->redeemed_at)->format('d/m/Y H:i') }}</td>
                                        <td>+{{ (int) $redemption->xp_reward }} XP / +{{ (int) $redemption->reward_points }} pts / +{{ (int) $redemption->bet_points }} bet</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="community-empty margin-top-20">Aucun code utilise pour le moment.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
