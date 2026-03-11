import React from 'react';
import { Link } from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'A l instant';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

function StatCard({ label, value, accent = false }) {
    return (
        <div className={`rounded-[1.4rem] border px-4 py-3 ${accent ? 'border-red-400/25 bg-red-500/10' : 'border-white/8 bg-white/[0.03]'}`}>
            <div className="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/45">{label}</div>
            <div className="mt-2 text-lg font-semibold text-white">{value}</div>
        </div>
    );
}

export default function ConversationSidebar({
    conversations,
    sidebar,
    activeConversationId,
    baseUrl,
    onNewConversation,
    onCloseMobile,
}) {
    return (
        <aside className="flex h-full flex-col overflow-hidden rounded-[2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(7,8,12,0.94))]">
            <div className="border-b border-white/8 px-5 py-5 sm:px-6">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Console ERAH</p>
                        <h2 className="mt-3 text-2xl font-semibold tracking-[-0.04em] text-white" style={{ fontFamily: '"Big Shoulders Display", system-ui, sans-serif' }}>
                            Historique assistant
                        </h2>
                        <p className="mt-2 text-sm leading-6 text-white/60">
                            Reprenez vos discussions sans perdre le fil.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={onNewConversation}
                        className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-red-400/30 bg-red-500/12 px-4 text-xs font-semibold uppercase tracking-[0.16em] text-white transition hover:border-red-300/50 hover:bg-red-500/18"
                    >
                        Nouvelle
                    </button>
                </div>

                <div className="mt-5 grid grid-cols-2 gap-3">
                    <StatCard label="Ligue" value={sidebar.league || 'Bronze'} accent />
                    <StatCard label="XP" value={sidebar.xp ?? 0} />
                    <StatCard label="Points" value={sidebar.points ?? sidebar.bet_points ?? 0} />
                    <StatCard label="Niveau" value={sidebar.level ?? 1} />
                </div>
            </div>

            <div className="space-y-5 overflow-y-auto px-5 py-5 sm:px-6">
                {sidebar.recommended_actions?.length ? (
                    <section>
                        <p className="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">Actions conseillees</p>
                        <div className="mt-3 space-y-2.5">
                            {sidebar.recommended_actions.map((action) => (
                                <a
                                    key={action.label}
                                    href={action.url}
                                    className="block rounded-[1.3rem] border border-white/8 bg-black/20 px-4 py-3 transition hover:border-red-400/30"
                                >
                                    <div className="text-sm font-semibold text-white">{action.label}</div>
                                    <div className="mt-1 text-xs leading-5 text-white/55">{action.description}</div>
                                </a>
                            ))}
                        </div>
                    </section>
                ) : null}

                <section>
                    <div className="flex items-center justify-between gap-3">
                        <p className="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">Conversations</p>
                        <span className="text-xs text-white/35">{conversations.length}</span>
                    </div>
                    <p className="mt-2 text-xs leading-5 text-white/38">
                        Renommage et suppression depuis la conversation ouverte.
                    </p>

                    <div className="mt-3 space-y-2.5">
                        {conversations.length ? (
                            conversations.map((conversation) => (
                                <Link
                                    key={conversation.id}
                                    href={conversation.url}
                                    preserveScroll
                                    onClick={onCloseMobile}
                                    className={`block rounded-[1.5rem] border px-4 py-3 transition ${
                                        activeConversationId === conversation.id
                                            ? 'border-red-400/30 bg-red-500/10 shadow-[0_0_0_1px_rgba(248,113,113,0.1)]'
                                            : 'border-white/8 bg-white/[0.03] hover:border-white/14 hover:bg-white/[0.045]'
                                    }`}
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="min-w-0">
                                            <div className="truncate text-sm font-semibold text-white">{conversation.title}</div>
                                            <div className="mt-1 text-xs leading-5 text-white/48">
                                                {conversation.last_message_preview || 'Conversation prete a reprendre.'}
                                            </div>
                                        </div>
                                        <span className="shrink-0 rounded-full border border-white/8 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/42">
                                            {conversation.last_message_role || 'chat'}
                                        </span>
                                    </div>

                                    <div className="mt-3 flex items-center justify-between gap-3 text-[11px] text-white/36">
                                        <span>{conversation.message_count} msg</span>
                                        <span>{formatDate(conversation.last_message_at)}</span>
                                    </div>
                                </Link>
                            ))
                        ) : (
                            <div className="rounded-[1.5rem] border border-dashed border-white/12 bg-black/20 px-4 py-5 text-sm leading-6 text-white/52">
                                Aucune conversation enregistree pour le moment.
                            </div>
                        )}
                    </div>
                </section>

                {sidebar.upcoming_matches?.length ? (
                    <section>
                        <p className="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">Matchs a surveiller</p>
                        <div className="mt-3 space-y-2.5">
                            {sidebar.upcoming_matches.map((match) => (
                                <a
                                    key={`${match.title}-${match.starts_at}`}
                                    href={match.url}
                                    className="block rounded-[1.3rem] border border-white/8 bg-black/20 px-4 py-3 transition hover:border-red-400/30"
                                >
                                    <div className="text-sm font-semibold text-white">{match.title}</div>
                                    <div className="mt-1 text-xs leading-5 text-white/55">
                                        {match.subtitle || formatDate(match.starts_at)}
                                    </div>
                                </a>
                            ))}
                        </div>
                    </section>
                ) : null}

                <div className="rounded-[1.5rem] border border-white/8 bg-white/[0.03] px-4 py-4 text-sm leading-6 text-white/56">
                    <p className="font-semibold text-white">Point d entree rapide</p>
                    <p className="mt-2">
                        Vous pouvez aussi ouvrir directement l assistant depuis le dashboard ERAH pour repartir sur une nouvelle demande.
                    </p>
                    <div className="mt-3">
                        <a
                            href={baseUrl}
                            className="inline-flex items-center rounded-full border border-white/10 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/78 transition hover:border-red-400/30 hover:text-white"
                        >
                            /console/assistant
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    );
}
