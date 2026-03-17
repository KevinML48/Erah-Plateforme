@extends('marketing.layouts.template')

@section('title', 'Detail email admin | ERAH Plateforme')
@section('meta_description', 'Detail auditable d un email admin manuel.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-mail-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .adm-mail-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .03);
            display: grid;
            gap: 8px;
        }

        .adm-mail-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--adm-text-soft);
        }

        .adm-mail-meta strong,
        .adm-mail-body {
            color: var(--adm-text);
            word-break: break-word;
        }

        .adm-mail-body {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 20px;
            background: rgba(255, 255, 255, .03);
            line-height: 1.7;
        }

        @media (max-width: 991.98px) {
            .adm-mail-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Email #'.$email->id,
        'heroDescription' => 'Historique detaille, statut et contenu de l envoi manuel admin.',
        'heroMaskDescription' => 'Traçabilite complete: emetteur, destinataire, statut, provider et contenu.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Audit</h2>
                        </div>

                        <div class="adm-mail-meta-grid">
                            <article class="adm-mail-meta"><span>Statut</span><strong>{{ $email->statusLabel() }}</strong></article>
                            <article class="adm-mail-meta"><span>Categorie</span><strong>{{ $email->categoryLabel() }}</strong></article>
                            <article class="adm-mail-meta"><span>Emetteur</span><strong>{{ $email->senderAdmin?->name ?? '-' }}<br>{{ $email->senderAdmin?->email ?? '-' }}</strong></article>
                            <article class="adm-mail-meta"><span>Destinataire</span><strong>{{ $email->recipient_name ?: 'Destinataire externe' }}<br>{{ $email->recipient_email }}</strong></article>
                            <article class="adm-mail-meta"><span>Utilisateur interne</span><strong>{{ $email->recipientUser ? '#'.$email->recipientUser->id.' - '.$email->recipientUser->name : 'Non' }}</strong></article>
                            <article class="adm-mail-meta"><span>Mailer / provider</span><strong>{{ $email->mailer ?: '-' }} / {{ $email->provider ?: '-' }}</strong></article>
                            <article class="adm-mail-meta"><span>Mise en queue</span><strong>{{ optional($email->queued_at)->format('d/m/Y H:i') ?: '-' }}</strong></article>
                            <article class="adm-mail-meta"><span>Envoi</span><strong>{{ optional($email->sent_at)->format('d/m/Y H:i') ?: '-' }}</strong></article>
                            <article class="adm-mail-meta"><span>Echec</span><strong>{{ optional($email->failed_at)->format('d/m/Y H:i') ?: '-' }}</strong></article>
                            <article class="adm-mail-meta"><span>Message provider</span><strong>{{ $email->provider_message_id ?: '-' }}</strong></article>
                        </div>

                        @if(filled($email->failure_reason))
                            <div class="adm-empty margin-top-20">Motif d echec: {{ $email->failure_reason }}</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Contenu</h2>
                        </div>

                        <div class="adm-mail-meta margin-bottom-20"><span>Sujet</span><strong>{{ $email->subject }}</strong></div>
                        <div class="adm-mail-body">{!! $email->body_html !!}</div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Actions</h2>
                        </div>

                        <div class="adm-row-actions">
                            <a href="{{ route('admin.emails.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Retour liste">Retour liste</span></a>
                            @if($email->recipientUser)
                                <a href="{{ route('admin.users.show', $email->recipientUser->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Fiche utilisateur">Fiche utilisateur</span></a>
                            @endif
                            @if(in_array($email->status, [\App\Models\AdminOutboundEmail::STATUS_DRAFT, \App\Models\AdminOutboundEmail::STATUS_FAILED], true))
                                <a href="{{ route('admin.emails.create', ['draft' => $email->id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Modifier">Modifier</span></a>
                                @if($email->status === \App\Models\AdminOutboundEmail::STATUS_DRAFT)
                                    <form method="POST" action="{{ route('admin.emails.send', $email) }}" onsubmit="return confirm('Envoyer cet email maintenant ?');">
                                        @csrf
                                        <input type="hidden" name="confirm_token" value="{{ data_get($email->meta, 'confirm_token') }}">
                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Envoyer">Envoyer</span></button>
                                    </form>
                                @endif
                                @if($email->status === \App\Models\AdminOutboundEmail::STATUS_FAILED)
                                    <form method="POST" action="{{ route('admin.emails.retry', $email) }}" onsubmit="return confirm('Relancer cet email en echec ?');">
                                        @csrf
                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Reessayer l envoi">Reessayer l envoi</span></button>
                                    </form>
                                @endif
                            @endif
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