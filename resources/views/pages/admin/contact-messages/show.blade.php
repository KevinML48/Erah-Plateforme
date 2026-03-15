@extends('marketing.layouts.template')

@section('title', 'Detail Contact | ERAH Plateforme')
@section('meta_description', 'Detail et traitement d une demande de contact ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .adm-contact-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .03);
            display: grid;
            gap: 8px;
        }

        .adm-contact-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--adm-text-soft);
        }

        .adm-contact-meta strong,
        .adm-contact-meta a {
            color: var(--adm-text);
            line-height: 1.45;
            word-break: break-word;
        }

        .adm-contact-message {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 255, 255, .03);
            color: var(--adm-text);
            white-space: pre-line;
            line-height: 1.65;
        }

        @media (max-width: 991.98px) {
            .adm-contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Contact Ops',
        'heroTitle' => 'Message #'.$contactMessage->id,
        'heroDescription' => 'Demande recue le '.(optional($contactMessage->created_at)->format('d/m/Y H:i') ?: '-').' par '.$contactMessage->name.'.',
        'heroMaskDescription' => 'Analyse detaillee du message, statut et donnees techniques utiles.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Informations contact</h2>
                        </div>

                        <div class="adm-contact-grid">
                            <article class="adm-contact-meta">
                                <span>Nom</span>
                                <strong>{{ $contactMessage->name }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Email</span>
                                <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Categorie</span>
                                <strong>{{ $contactMessage->categoryLabel() }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Sujet</span>
                                <strong>{{ $contactMessage->subject }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Date de reception</span>
                                <strong>{{ optional($contactMessage->created_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Statut</span>
                                <strong>{{ $contactMessage->statusLabel() }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>Adresse IP</span>
                                <strong>{{ $contactMessage->ip_address ?: '-' }}</strong>
                            </article>
                            <article class="adm-contact-meta">
                                <span>User agent</span>
                                <strong>{{ $contactMessage->user_agent ?: '-' }}</strong>
                            </article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Message</h2>
                        </div>
                        <div class="adm-contact-message">{{ $contactMessage->message }}</div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Actions</h2>
                        </div>

                        <div class="adm-row-actions">
                            <a href="{{ route('admin.contact-messages.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour liste">Retour liste</span>
                            </a>

                            @foreach(\App\Models\ContactMessage::statusLabels() as $statusKey => $statusLabel)
                                @if($contactMessage->status !== $statusKey)
                                    <form method="POST" action="{{ route('admin.contact-messages.status', $contactMessage) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $statusKey }}">
                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                            <span data-hover="{{ $statusLabel }}">{{ $statusLabel }}</span>
                                        </button>
                                    </form>
                                @endif
                            @endforeach

                            <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" onsubmit="return confirm('Supprimer definitivement ce message de contact ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Supprimer">Supprimer</span>
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
