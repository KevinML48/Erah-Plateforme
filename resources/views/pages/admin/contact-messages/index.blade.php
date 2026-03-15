@extends('marketing.layouts.template')

@section('title', 'Admin Contacts | ERAH Plateforme')
@section('meta_description', 'Centre de suivi des demandes de contact ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-contact-subject {
            margin: 0 0 4px;
            font-size: 18px;
            color: var(--adm-text);
        }

        .adm-contact-preview {
            margin: 0;
            color: var(--adm-text-soft);
            font-size: 14px;
            line-height: 1.5;
        }

        .adm-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--adm-border);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--adm-text-soft);
        }

        .adm-status-pill.adm-status-new {
            border-color: rgba(255, 214, 100, .55);
            color: #ffe6a8;
        }

        .adm-status-pill.adm-status-processed {
            border-color: rgba(91, 218, 167, .55);
            color: #c4f6e4;
        }

        .adm-status-pill.adm-status-archived {
            border-color: rgba(173, 188, 214, .45);
            color: rgba(213, 222, 236, .9);
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Contact Ops',
        'heroTitle' => 'Demandes de contact',
        'heroDescription' => 'Suivi des messages entrants: qualification, traitement et archivage.',
        'heroMaskDescription' => 'Vue admin simple pour traiter chaque demande sans perdre le contexte.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue d ensemble</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Toutes les demandes sont stockees en base puis notifiees par email.</p>
                        </div>

                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['total'] }}</strong><span>Total</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['new'] }}</strong><span>Nouveaux</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['processed'] }}</strong><span>Traites</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['archived'] }}</strong><span>Archives</span></article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtres</h2>
                        </div>

                        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="adm-form adm-form-grid-3 tt-form tt-form-creative tt-form-lg">
                            <div class="tt-form-group adm-col-span-2">
                                <label for="contact-q">Recherche</label>
                                <input class="tt-form-control" id="contact-q" name="q" type="text" value="{{ $search }}" placeholder="Nom, email, sujet ou message">
                            </div>
                            <div class="tt-form-group">
                                <label for="contact-status">Statut</label>
                                <select class="tt-form-control" id="contact-status" name="status">
                                    <option value="all">Tous</option>
                                    @foreach($statusLabels as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="tt-form-group adm-form-cta adm-col-span-3">
                                <p class="adm-form-cta-copy">Filtrez rapidement les messages prioritaires ou deja traites.</p>
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Filtrer">Filtrer</span>
                                    </button>
                                    <a href="{{ route('admin.contact-messages.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                        <span data-hover="Reinitialiser">Reinitialiser</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Messages recus</h2>
                        </div>

                        @if($messages->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Contact</th>
                                            <th>Sujet</th>
                                            <th>Categorie</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($messages as $contactMessage)
                                            <tr>
                                                <td>
                                                    <strong>{{ $contactMessage->name }}</strong><br>
                                                    <small>{{ $contactMessage->email }}</small>
                                                </td>
                                                <td>
                                                    <p class="adm-contact-subject">{{ $contactMessage->subject }}</p>
                                                    <p class="adm-contact-preview">{{ \Illuminate\Support\Str::limit($contactMessage->message, 120) }}</p>
                                                </td>
                                                <td>{{ $contactMessage->categoryLabel() }}</td>
                                                <td>{{ optional($contactMessage->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>
                                                    <span class="adm-status-pill adm-status-{{ $contactMessage->status }}">
                                                        {{ $contactMessage->statusLabel() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="adm-row-actions">
                                                        <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                            <span data-hover="Detail">Detail</span>
                                                        </a>

                                                        @if($contactMessage->status !== \App\Models\ContactMessage::STATUS_ARCHIVED && $contactMessage->status !== \App\Models\ContactMessage::STATUS_PROCESSED)
                                                            <form method="POST" action="{{ route('admin.contact-messages.status', $contactMessage) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="{{ \App\Models\ContactMessage::STATUS_PROCESSED }}">
                                                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                                    <span data-hover="Traiter">Traiter</span>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($contactMessage->status !== \App\Models\ContactMessage::STATUS_ARCHIVED)
                                                            <form method="POST" action="{{ route('admin.contact-messages.status', $contactMessage) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="status" value="{{ \App\Models\ContactMessage::STATUS_ARCHIVED }}">
                                                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                                    <span data-hover="Archiver">Archiver</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="adm-pagin">{{ $messages->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @else
                            <div class="adm-empty">Aucune demande de contact pour ces filtres.</div>
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

