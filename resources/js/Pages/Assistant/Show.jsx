import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Link } from '@inertiajs/react';

import HelpCenterLayout from '../../Layouts/HelpCenterLayout';
import AssistantComposer from './components/AssistantComposer';
import ConversationSidebar from './components/ConversationSidebar';
import MessageThread from './components/MessageThread';

function createOptimisticConversation(currentConversation, message) {
    const timestamp = new Date().toISOString();
    const userMessage = {
        id: `temp-user-${timestamp}`,
        role: 'user',
        content: message,
        created_at: timestamp,
        metadata: {},
    };

    const assistantMessage = {
        id: `temp-assistant-${timestamp}`,
        role: 'assistant',
        content: '',
        created_at: timestamp,
        metadata: {},
    };

    return {
        id: currentConversation?.id ?? null,
        title: currentConversation?.title || 'Nouvelle conversation',
        provider: currentConversation?.provider ?? null,
        model: currentConversation?.model ?? null,
        messages: [...(currentConversation?.messages ?? []), userMessage, assistantMessage],
        tempAssistantId: assistantMessage.id,
        tempUserId: userMessage.id,
    };
}

function updateConversationList(conversations, summary) {
    const filtered = conversations.filter((item) => item.id !== summary.id);

    return [summary, ...filtered];
}

function mergeConversationSummary(conversations, summary) {
    let matched = false;

    const next = conversations.map((item) => {
        if (item.id !== summary.id) {
            return item;
        }

        matched = true;

        return {
            ...item,
            ...summary,
        };
    });

    return matched ? next : updateConversationList(conversations, summary);
}

function parseEventChunk(rawChunk) {
    const lines = rawChunk.split(/\r?\n/);
    let event = 'message';
    const data = [];

    lines.forEach((line) => {
        if (line.startsWith('event:')) {
            event = line.slice(6).trim();
        }

        if (line.startsWith('data:')) {
            data.push(line.slice(5).trim());
        }
    });

    if (!data.length) {
        return null;
    }

    return {
        event,
        data: JSON.parse(data.join('\n')),
    };
}

async function readEventStream(response, onEvent) {
    const reader = response.body?.getReader();

    if (!reader) {
        throw new Error('Le flux de reponse est indisponible.');
    }

    const decoder = new TextDecoder();
    let buffer = '';

    while (true) {
        const { value, done } = await reader.read();

        if (done) {
            break;
        }

        buffer += decoder.decode(value, { stream: true });

        while (buffer.includes('\n\n')) {
            const boundary = buffer.indexOf('\n\n');
            const rawChunk = buffer.slice(0, boundary);
            buffer = buffer.slice(boundary + 2);
            const event = parseEventChunk(rawChunk);

            if (event) {
                onEvent(event);
            }
        }
    }
}

async function extractErrorMessage(response, fallback) {
    const payload = await response.json().catch(() => ({}));
    const validationMessage = Object.values(payload.errors || {}).flat()[0];

    return validationMessage || payload.message || fallback;
}

export default function AssistantShow({ page }) {
    const [conversations, setConversations] = useState(page.conversations);
    const [activeConversation, setActiveConversation] = useState(page.selected_conversation);
    const [draft, setDraft] = useState(page.prefill_prompt || '');
    const [titleDraft, setTitleDraft] = useState(page.selected_conversation?.title || '');
    const [isStreaming, setIsStreaming] = useState(false);
    const [isRenaming, setIsRenaming] = useState(false);
    const [isSavingTitle, setIsSavingTitle] = useState(false);
    const [isDeletingConversation, setIsDeletingConversation] = useState(false);
    const [deleteConfirmationOpen, setDeleteConfirmationOpen] = useState(false);
    const [error, setError] = useState(null);
    const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
    const scrollAnchorRef = useRef(null);

    useEffect(() => {
        setConversations(page.conversations);
        setActiveConversation(page.selected_conversation);
        setDraft(page.prefill_prompt || '');
        setTitleDraft(page.selected_conversation?.title || '');
        setIsRenaming(false);
        setIsSavingTitle(false);
        setIsDeletingConversation(false);
        setDeleteConfirmationOpen(false);
        setError(null);
    }, [page]);

    useEffect(() => {
        scrollAnchorRef.current?.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }, [activeConversation?.messages, isStreaming]);

    useEffect(() => {
        setTitleDraft(activeConversation?.title || '');
    }, [activeConversation?.id, activeConversation?.title]);

    const activeMessages = useMemo(() => activeConversation?.messages ?? [], [activeConversation]);
    const activeConversationSummary = useMemo(
        () => conversations.find((item) => item.id === activeConversation?.id) || null,
        [conversations, activeConversation?.id],
    );

    const resetConversation = () => {
        if (isStreaming) {
            return;
        }

        setActiveConversation(null);
        setDraft('');
        setTitleDraft('');
        setIsRenaming(false);
        setIsSavingTitle(false);
        setIsDeletingConversation(false);
        setDeleteConfirmationOpen(false);
        setError(null);
        setMobileSidebarOpen(false);
        window.history.replaceState({}, '', page.endpoints.index);
    };

    const applyPrompt = (prompt) => {
        if (isStreaming || !page.availability.enabled) {
            return;
        }

        void submitMessage(prompt);
    };

    const submitMessage = async (rawMessage) => {
        const message = rawMessage.trim();

        if (!message || isStreaming || !page.availability.enabled) {
            return;
        }

        setError(null);
        setIsStreaming(true);

        const optimisticConversation = createOptimisticConversation(activeConversation, message);
        const tempAssistantId = optimisticConversation.tempAssistantId;
        const tempUserId = optimisticConversation.tempUserId;

        setActiveConversation({
            id: optimisticConversation.id,
            title: optimisticConversation.title,
            provider: optimisticConversation.provider,
            model: optimisticConversation.model,
            messages: optimisticConversation.messages,
        });
        setDraft('');

        try {
            const response = await fetch(page.endpoints.stream_message, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'text/event-stream',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message,
                    conversation_id: activeConversation?.id ?? null,
                }),
            });

            if (!response.ok) {
                throw new Error(await extractErrorMessage(response, "Impossible d envoyer le message."));
            }

            await readEventStream(response, ({ event: eventName, data }) => {
                if (eventName === 'delta') {
                    setActiveConversation((current) => {
                        if (!current) {
                            return current;
                        }

                        return {
                            ...current,
                            messages: current.messages.map((item) => (
                                item.id === tempAssistantId
                                    ? { ...item, content: `${item.content || ''}${data.delta || ''}` }
                                    : item
                            )),
                        };
                    });
                }

                if (eventName === 'conversation') {
                    setActiveConversation((current) => {
                        if (!current) {
                            return current;
                        }

                        return {
                            ...current,
                            id: data.conversation.id,
                            title: data.conversation.title,
                            messages: current.messages.map((item) => (
                                item.id === tempUserId ? data.user_message : item
                            )),
                        };
                    });

                    setConversations((current) => updateConversationList(current, data.conversation));
                    window.history.replaceState({}, '', `${page.endpoints.index}?conversation=${data.conversation.id}`);
                }

                if (eventName === 'complete') {
                    setActiveConversation((current) => {
                        if (!current) {
                            return current;
                        }

                        return {
                            ...current,
                            id: data.conversation.id,
                            title: data.conversation.title,
                            provider: data.assistant_message.provider,
                            model: data.assistant_message.model,
                            messages: current.messages.map((item) => (
                                item.id === tempAssistantId ? data.assistant_message : item
                            )),
                        };
                    });

                    setConversations((current) => updateConversationList(current, data.conversation));
                    window.history.replaceState({}, '', `${page.endpoints.index}?conversation=${data.conversation.id}`);
                }

                if (eventName === 'error') {
                    throw new Error(data.message || 'Le flux a ete interrompu.');
                }
            });
        } catch (exception) {
            setError(exception.message || 'Une erreur est survenue pendant la generation.');
            setActiveConversation((current) => {
                if (!current) {
                    return current;
                }

                return {
                    ...current,
                    messages: current.messages.filter((item) => item.id !== tempAssistantId),
                };
            });
        } finally {
            setIsStreaming(false);
        }
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        await submitMessage(draft);
    };

    const handleRenameConversation = async (event) => {
        event.preventDefault();

        const nextTitle = titleDraft.trim();

        if (!activeConversationSummary?.rename_url || !nextTitle || isStreaming || isSavingTitle || isDeletingConversation) {
            return;
        }

        setIsSavingTitle(true);
        setError(null);

        try {
            const response = await fetch(activeConversationSummary.rename_url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    title: nextTitle,
                }),
            });

            if (!response.ok) {
                throw new Error(await extractErrorMessage(response, 'Impossible de renommer cette conversation.'));
            }

            const payload = await response.json();
            const summary = payload.data.conversation;

            setConversations((current) => mergeConversationSummary(current, summary));
            setActiveConversation((current) => (
                current
                    ? {
                        ...current,
                        title: summary.title,
                        rename_url: summary.rename_url,
                        delete_url: summary.delete_url,
                    }
                    : current
            ));
            setTitleDraft(summary.title);
            setIsRenaming(false);
        } catch (exception) {
            setError(exception.message || 'Une erreur est survenue pendant le renommage.');
        } finally {
            setIsSavingTitle(false);
        }
    };

    const handleDeleteConversation = async () => {
        if (!activeConversationSummary?.delete_url || isStreaming || isSavingTitle || isDeletingConversation) {
            return;
        }

        setIsDeletingConversation(true);
        setError(null);

        try {
            const response = await fetch(activeConversationSummary.delete_url, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(await extractErrorMessage(response, 'Impossible de supprimer cette conversation.'));
            }

            setConversations((current) => current.filter((item) => item.id !== activeConversationSummary.id));
            setActiveConversation(null);
            setTitleDraft('');
            setDraft('');
            setIsRenaming(false);
            setDeleteConfirmationOpen(false);
            window.history.replaceState({}, '', page.endpoints.index);
        } catch (exception) {
            setError(exception.message || 'Une erreur est survenue pendant la suppression.');
        } finally {
            setIsDeletingConversation(false);
        }
    };

    return (
        <HelpCenterLayout
            mode="console"
            title="Assistant ERAH"
            subtitle="Assistant conversationnel premium pour accompagner les utilisateurs ERAH dans leur console."
        >
            <section className="relative overflow-hidden rounded-[2.4rem] border border-white/8 bg-[linear-gradient(135deg,rgba(255,255,255,0.06),rgba(225,29,72,0.1),rgba(8,9,14,0.96))] px-5 py-6 sm:px-7 sm:py-7">
                <div className="pointer-events-none absolute inset-y-0 right-0 hidden w-1/2 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.12),transparent_54%)] lg:block" />

                <div className="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div className="max-w-3xl">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">{page.hero.eyebrow}</p>
                        <h1 className="mt-3 text-4xl font-semibold tracking-[-0.06em] text-white sm:text-5xl" style={{ fontFamily: '"Big Shoulders Display", system-ui, sans-serif' }}>
                            {page.hero.title}
                        </h1>
                        <p className="mt-4 text-sm leading-7 text-white/68 sm:text-base">
                            {page.hero.description}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <button
                            type="button"
                            onClick={() => setMobileSidebarOpen(true)}
                            className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-white/12 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:border-red-400/30 lg:hidden"
                        >
                            Conversations
                        </button>
                        <button
                            type="button"
                            onClick={resetConversation}
                            disabled={isStreaming}
                            className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-red-400/30 bg-red-500/10 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-red-500/16 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Nouvelle conversation
                        </button>
                        <Link
                            href="/console/help"
                            className="inline-flex min-h-[46px] items-center justify-center rounded-full border border-white/12 px-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/75 transition hover:border-red-400/30 hover:text-white"
                        >
                            Aide ERAH
                        </Link>
                    </div>
                </div>
            </section>

            <div className="grid min-h-[calc(100vh-14rem)] gap-6 xl:grid-cols-[minmax(320px,0.8fr)_minmax(0,1.2fr)]">
                <div className="hidden xl:block">
                    <ConversationSidebar
                        conversations={conversations}
                        sidebar={page.sidebar}
                        activeConversationId={activeConversation?.id ?? null}
                        baseUrl={page.endpoints.index}
                        onNewConversation={resetConversation}
                        onCloseMobile={() => setMobileSidebarOpen(false)}
                    />
                </div>

                {mobileSidebarOpen ? (
                    <div className="fixed inset-0 z-50 flex xl:hidden">
                        <div className="absolute inset-0 bg-black/70" onClick={() => setMobileSidebarOpen(false)} />
                        <div className="relative ml-auto h-full w-full max-w-[26rem] p-3">
                            <ConversationSidebar
                                conversations={conversations}
                                sidebar={page.sidebar}
                                activeConversationId={activeConversation?.id ?? null}
                                baseUrl={page.endpoints.index}
                                onNewConversation={resetConversation}
                                onCloseMobile={() => setMobileSidebarOpen(false)}
                            />
                        </div>
                    </div>
                ) : null}

                <section className="flex min-h-[calc(100vh-14rem)] flex-col gap-5">
                    <div className="flex-1">
                        <MessageThread
                            conversation={activeConversation}
                            messages={activeMessages}
                            starterPrompts={page.starter_prompts}
                            focusedArticle={page.focused_article}
                            sidebar={page.sidebar}
                            availability={page.availability}
                            onPromptSelect={applyPrompt}
                            error={error}
                            scrollAnchorRef={scrollAnchorRef}
                            conversationActions={{
                                canManage: Boolean(activeConversationSummary?.id),
                                isRenaming,
                                renameDraft: titleDraft,
                                onRenameDraftChange: setTitleDraft,
                                onRenameStart: () => {
                                    setError(null);
                                    setDeleteConfirmationOpen(false);
                                    setTitleDraft(activeConversation?.title || '');
                                    setIsRenaming(true);
                                },
                                onRenameCancel: () => {
                                    setTitleDraft(activeConversation?.title || '');
                                    setIsRenaming(false);
                                },
                                onRenameSubmit: handleRenameConversation,
                                isSavingTitle,
                                deleteConfirmationOpen,
                                onDeleteStart: () => {
                                    setError(null);
                                    setIsRenaming(false);
                                    setDeleteConfirmationOpen(true);
                                },
                                onDeleteCancel: () => setDeleteConfirmationOpen(false),
                                onDeleteConfirm: handleDeleteConversation,
                                isDeletingConversation,
                                disabled: isStreaming || isSavingTitle || isDeletingConversation,
                            }}
                        />
                    </div>

                    <div className="sticky bottom-0 z-20">
                        <AssistantComposer
                            value={draft}
                            onChange={setDraft}
                            onSubmit={handleSubmit}
                            onPromptClick={applyPrompt}
                            prompts={page.starter_prompts}
                            disabled={!page.availability.enabled}
                            loading={isStreaming}
                        />
                    </div>
                </section>
            </div>
        </HelpCenterLayout>
    );
}
