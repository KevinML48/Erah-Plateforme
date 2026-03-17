@extends('marketing.layouts.template')

@section('title', 'Admin Emails | ERAH Plateforme')
@section('meta_description', 'Historique et supervision des emails admin envoyes depuis la plateforme.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-email-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--adm-border);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--adm-text-soft);
        }

        .adm-email-pill.status-sent {
            border-color: rgba(91, 218, 167, .55);
            color: #c4f6e4;
        }

        .adm-email-pill.status-queued {
            border-color: rgba(123, 191, 255, .5);
            color: #d7ebff;
        }

        .adm-email-pill.status-failed {
            border-color: rgba(255, 129, 129, .55);
            color: #ffd0d0;
        }

        .adm-email-pill.status-draft {
            border-color: rgba(241, 207, 109, .5);
            color: #ffefbf;
        }

        .adm-email-subject {
            margin: 0 0 4px;
            font-size: 18px;
            color: var(--adm-text);
        }

        .adm-email-preview {
            margin: 0;
            color: var(--adm-text-soft);
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Emails admin',
        'heroDescription' => 'Historique auditable des emails envoyes, en attente ou en echec.',
        'heroMaskDescription' => 'Une seule vue pour filtrer, relire et suivre chaque envoi manuel admin.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue d ensemble</h2>
                        </div>

                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['total'] }}</strong><span>Total</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['draft'] }}</strong><span>Brouillons</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['queued'] }}</strong><span>En queue</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['sent'] }}</strong><span>Envoyes</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) $stats['failed'] }}</strong><span>Echecs</span></article>
                        </div>

                        <div class="adm-row-actions margin-top-20">
                            <a href="{{ route('admin.emails.create') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Envoyer un email">Envoyer un email</span>
                            </a>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtres</h2>
                        </div>

                        <form method="GET" action="{{ route('admin.emails.index') }}" class="adm-form adm-form-grid-4 tt-form tt-form-creative tt-form-lg">
                            <div class="tt-form-group adm-col-span-2">
                                <label for="emails-q">Destinataire / sujet</label>
                                <input class="tt-form-control" id="emails-q" name="q" type="text" value="{{ $search }}" placeholder="Nom, email, sujet, contenu">
                            </div>
                            <div class="tt-form-group">
                                <label for="emails-status">Statut</label>
                                <select class="tt-form-control" id="emails-status" name="status">
                                    <option value="all">Tous</option>
                                    @foreach($statusLabels as $statusKey => $statusLabel)
                                        <option value="{{ $statusKey }}" @selected($status === $statusKey)>{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="tt-form-group">
                                <label for="emails-category">Categorie</label>
                                <select class="tt-form-control" id="emails-category" name="category">
                                    <option value="all">Toutes</option>
                                    @foreach($categoryLabels as $categoryKey => $categoryLabel)
                                        <option value="{{ $categoryKey }}" @selected($category === $categoryKey)>{{ $categoryLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="tt-form-group">
                                <label for="emails-date">Date</label>
                                <input class="tt-form-control" id="emails-date" name="date" type="date" value="{{ $date }}">
                            </div>
                            <div class="tt-form-group adm-form-cta adm-col-span-3">
                                <p class="adm-form-cta-copy">Filtrez l historique par destinataire, statut, categorie et date.</p>
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Filtrer">Filtrer</span></button>
                                    <a href="{{ route('admin.emails.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reinitialiser">Reinitialiser</span></a>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Historique</h2>
                        </div>

                        @if($emails->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Destinataire</th>
                                            <th>Sujet</th>
                                            <th>Categorie</th>
                                            <th>Statut</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($emails as $email)
                                            <tr>
                                                <td>
                                                    <strong>{{ $email->recipient_name ?: 'Destinataire externe' }}</strong><br>
                                                    <small>{{ $email->recipient_email }}</small>
                                                </td>
                                                <td>
                                                    <p class="adm-email-subject">{{ $email->subject }}</p>
                                                    <p class="adm-email-preview">{{ \Illuminate\Support\Str::limit($email->body_text, 120) }}</p>
                                                </td>
                                                <td>{{ $email->categoryLabel() }}</td>
                                                <td><span class="adm-email-pill status-{{ $email->status }}">{{ $email->statusLabel() }}</span></td>
                                                <td>{{ optional($email->sent_at ?: $email->queued_at ?: $email->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>
                                                    <div class="adm-row-actions">
                                                        <a href="{{ route('admin.emails.show', $email) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                            <span data-hover="Detail">Detail</span>
                                                        </a>
                                                        @if($email->status === \App\Models\AdminOutboundEmail::STATUS_DRAFT)
                                                            <a href="{{ route('admin.emails.create', ['draft' => $email->id]) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                                <span data-hover="Modifier">Modifier</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="adm-pagin">{{ $emails->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @else
                            <div class="adm-empty">Aucun email admin pour ces filtres.</div>
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