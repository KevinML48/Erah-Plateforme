import React from 'react';

import AssistantMarkdown from './AssistantMarkdown';

function formatMessageTime(value) {
    if (!value) {
        return 'Maintenant';
    }

    return new Intl.DateTimeFormat('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

function EmptyPromptCard({ prompt, onClick }) {
    return (
        <button
            type="button"
            onClick={() => onClick(prompt)}
            className="group rounded-[1.8rem] border border-white/8 bg-white/[0.03] p-4 text-left transition hover:border-red-400/30 hover:bg-red-500/[0.06]"
        >
            <div className="text-sm font-semibold text-white transition group-hover:text-red-50">{prompt}</div>
            <div className="mt-2 text-xs leading-5 text-white/50">Pre-remplir ce message</div>
        </button>
    );
}

function MessageBubble({ message }) {
    const isUser = message.role === 'user';

    return (
        <article className={`max-w-[92%] rounded-[1.8rem] border px-4 py-4 sm:max-w-[78%] sm:px-5 ${
            isUser
                ? 'ml-auto border-red-400/25 bg-[linear-gradient(135deg,rgba(220,38,38,0.18),rgba(255,255,255,0.04))]'
                : 'border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),rgba(8,9,13,0.92))]'
        }`}>
            <div className="mb-3 flex items-center justify-between gap-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/38">
                <span>{isUser ? 'Vous' : 'ERAH Assistant'}</span>
                <span>{formatMessageTime(message.created_at)}</span>
            </div>

            <AssistantMarkdown content={message.content} />

            {!isUser && message.metadata?.sources?.length ? (
                <div className="mt-4 flex flex-wrap gap-2">
                    {message.metadata.sources.map((source) => (
                        <a
                            key={`${source.type}-${source.title}`}
                            href={source.url}
                            className="inline-flex items-center rounded-full border border-white/10 bg-black/25 px-3 py-1.5 text-[11px] font-semibold text-white/72 transition hover:border-red-400/30 hover:text-white"
                        >
                            {source.title}
                        </a>
                    ))}
                </div>
            ) : null}
        </article>
    );
}

export default function MessageThread({
    conversation,
    messages,
    starterPrompts,
    focusedArticle,
    sidebar,
    availability,
    onPromptSelect,
    error,
    scrollAnchorRef,
    conversationActions,
}) {
    if (!messages.length) {
        return (
            <div className="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.72fr)]">
                <section className="rounded-[2.2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(7,8,12,0.94))] p-6 sm:p-8">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Nouvelle conversation</p>
                    <h2 className="mt-4 text-4xl font-semibold tracking-[-0.05em] text-white sm:text-5xl" style={{ fontFamily: '"Big Shoulders Display", system-ui, sans-serif' }}>
                        Parlez a ERAH comme a un vrai assistant produit.
                    </h2>
                    <p className="mt-4 max-w-2xl text-sm leading-7 text-white/65 sm:text-base">
                        L assistant peut expliquer la plateforme, vos points, vos missions, les matchs a venir, les paris, le profil et les prochains pas pertinents. Il reste strictement sur les donnees fiables.
                    </p>

                    {error ? (
                        <div className="mt-5 rounded-[1.4rem] border border-red-400/25 bg-red-500/10 px-4 py-3 text-sm text-red-50">
                            {error}
                        </div>
                    ) : null}

                    {!availability.enabled ? (
                        <div className="mt-5 rounded-[1.6rem] border border-white/10 bg-black/25 px-4 py-4 text-sm leading-6 text-white/60">
                            L assistant est actuellement desactive par configuration. L historique reste visible, mais l envoi de nouveaux messages est bloque.
                        </div>
                    ) : null}

                    <div className="mt-8 grid gap-3 md:grid-cols-2">
                        {starterPrompts.map((prompt) => (
                            <EmptyPromptCard key={prompt} prompt={prompt} onClick={onPromptSelect} />
                        ))}
                    </div>
                </section>

                <aside className="space-y-5">
                    {focusedArticle ? (
                        <div className="rounded-[2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(7,8,12,0.94))] p-5">
                            <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Contexte FAQ</p>
                            <h3 className="mt-3 text-xl font-semibold text-white">{focusedArticle.title}</h3>
                            <p className="mt-3 text-sm leading-7 text-white/65">{focusedArticle.summary}</p>
                            <a
                                href={focusedArticle.url}
                                className="mt-4 inline-flex items-center rounded-full border border-white/10 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/78 transition hover:border-red-400/30 hover:text-white"
                            >
                                Voir la source
                            </a>
                        </div>
                    ) : null}

                    <div className="rounded-[2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(7,8,12,0.94))] p-5">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Ce qui peut etre personnalise</p>
                        <div className="mt-4 grid gap-3">
                            <div className="rounded-[1.4rem] border border-white/8 bg-black/20 px-4 py-3 text-sm text-white/70">
                                Ligue: <span className="font-semibold text-white">{sidebar.league || 'Bronze'}</span>
                            </div>
                            <div className="rounded-[1.4rem] border border-white/8 bg-black/20 px-4 py-3 text-sm text-white/70">
                                Notifications non lues: <span className="font-semibold text-white">{sidebar.unread_notifications ?? 0}</span>
                            </div>
                            {(sidebar.profile_suggestions || []).map((suggestion) => (
                                <div
                                    key={suggestion}
                                    className="rounded-[1.4rem] border border-white/8 bg-black/20 px-4 py-3 text-sm leading-6 text-white/65"
                                >
                                    {suggestion}
                                </div>
                            ))}
                        </div>
                    </div>
                </aside>
            </div>
        );
    }

    return (
        <div className="rounded-[2.2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(7,8,12,0.95))] p-4 sm:p-5">
            <div className="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-white/8 pb-4">
                <div>
                    <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Conversation active</p>
                    <h2 className="mt-2 text-2xl font-semibold text-white" style={{ fontFamily: '"Big Shoulders Display", system-ui, sans-serif' }}>
                        {conversation?.title || 'Nouvelle conversation'}
                    </h2>
                </div>

                <div className="flex flex-wrap items-center justify-end gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/42">
                    {conversation?.provider ? (
                        <span className="rounded-full border border-white/10 px-3 py-2">{conversation.provider}</span>
                    ) : null}
                    {conversation?.model ? (
                        <span className="rounded-full border border-white/10 px-3 py-2">{conversation.model}</span>
                    ) : null}
                    {conversationActions?.canManage && !conversationActions.isRenaming ? (
                        <button
                            type="button"
                            disabled={conversationActions.disabled}
                            onClick={conversationActions.onRenameStart}
                            className="inline-flex min-h-[40px] items-center justify-center rounded-full border border-white/10 px-3.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/72 transition hover:border-red-400/30 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Renommer
                        </button>
                    ) : null}
                    {conversationActions?.canManage ? (
                        <button
                            type="button"
                            disabled={conversationActions.disabled}
                            onClick={conversationActions.deleteConfirmationOpen ? conversationActions.onDeleteCancel : conversationActions.onDeleteStart}
                            className="inline-flex min-h-[40px] items-center justify-center rounded-full border border-red-400/22 px-3.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-red-100 transition hover:border-red-300/40 hover:bg-red-500/10 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {conversationActions.deleteConfirmationOpen ? 'Annuler' : 'Supprimer'}
                        </button>
                    ) : null}
                </div>
            </div>

            {conversationActions?.canManage && conversationActions.isRenaming ? (
                <form onSubmit={conversationActions.onRenameSubmit} className="mb-4 rounded-[1.5rem] border border-white/8 bg-black/20 p-4">
                    <div className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/42">Titre de conversation</div>
                    <div className="mt-3 flex flex-col gap-3 lg:flex-row">
                        <input
                            type="text"
                            value={conversationActions.renameDraft}
                            onChange={(event) => conversationActions.onRenameDraftChange(event.target.value)}
                            maxLength={160}
                            className="min-h-[48px] flex-1 rounded-[1.2rem] border border-white/10 bg-black/30 px-4 text-sm text-white placeholder:text-white/28 focus:border-red-400/35 focus:outline-none focus:ring-0"
                            placeholder="Donnez un titre clair a cette conversation"
                        />
                        <div className="flex flex-wrap gap-2">
                            <button
                                type="submit"
                                disabled={conversationActions.isSavingTitle || !conversationActions.renameDraft.trim()}
                                className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-red-400/30 bg-red-600 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {conversationActions.isSavingTitle ? 'Enregistrement...' : 'Enregistrer'}
                            </button>
                            <button
                                type="button"
                                disabled={conversationActions.isSavingTitle}
                                onClick={conversationActions.onRenameCancel}
                                className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-white/10 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/72 transition hover:border-white/18 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Fermer
                            </button>
                        </div>
                    </div>
                </form>
            ) : null}

            {conversationActions?.canManage && conversationActions.deleteConfirmationOpen ? (
                <div className="mb-4 rounded-[1.5rem] border border-red-400/22 bg-red-500/10 p-4">
                    <div className="text-sm font-semibold text-white">Supprimer cette conversation ?</div>
                    <p className="mt-2 text-sm leading-6 text-red-50/85">
                        Toute la discussion et ses messages seront retires de votre historique. Cette action est definitive.
                    </p>
                    <div className="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            onClick={conversationActions.onDeleteConfirm}
                            disabled={conversationActions.isDeletingConversation}
                            className="inline-flex min-h-[44px] items-center justify-center rounded-full border border-red-300/35 bg-red-600 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {conversationActions.isDeletingConversation ? 'Suppression...' : 'Confirmer'}
                        </button>
                        <button
                            type="button"
                            onClick={conversationActions.onDeleteCancel}
                            disabled={conversationActions.isDeletingConversation}
                            className="inline-flex min-h-[44px] items-center justify-center rounded-full border border-white/10 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/72 transition hover:border-white/18 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Conserver
                        </button>
                    </div>
                </div>
            ) : null}

            {error ? (
                <div className="mb-4 rounded-[1.4rem] border border-red-400/25 bg-red-500/10 px-4 py-3 text-sm text-red-50">
                    {error}
                </div>
            ) : null}

            <div className="space-y-4">
                {messages.map((message) => (
                    <MessageBubble key={message.id} message={message} />
                ))}
                <div ref={scrollAnchorRef} />
            </div>
        </div>
    );
}
