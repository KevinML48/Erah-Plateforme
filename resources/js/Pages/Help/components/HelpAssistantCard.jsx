import React from 'react';
import { useMemo, useState } from 'react';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export default function HelpAssistantCard({ assistant }) {
    const [message, setMessage] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [result, setResult] = useState(null);

    const preview = assistant?.user_preview ?? null;
    const prompts = useMemo(() => assistant?.suggested_prompts ?? [], [assistant]);

    if (!assistant) {
        return null;
    }

    const ask = async (value) => {
        const question = String(value ?? message).trim();

        if (!question) {
            return;
        }

        setLoading(true);
        setError(null);

        try {
            const response = await fetch(assistant.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ message: question }),
            });

            if (!response.ok) {
                throw new Error("L'assistant n'a pas pu traiter la demande.");
            }

            const payload = await response.json();
            setResult(payload.data);
            setMessage(question);
        } catch (err) {
            setError(err.message || "L'assistant n'est pas disponible pour l'instant.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <section
            id="assistant-panel"
            className="grid gap-5 rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),rgba(10,11,17,0.98))] p-6 sm:p-8 xl:grid-cols-[minmax(0,0.92fr)_minmax(380px,0.88fr)]"
        >
            <article className="space-y-5">
                <div>
                    <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">
                        Assistant IA
                    </p>
                    <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                        {assistant.title}
                    </h2>
                    <p className="mt-4 max-w-2xl text-sm leading-7 text-white/72 sm:text-base sm:leading-8">
                        {assistant.description}
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <article className="rounded-[1.5rem] border border-white/8 bg-black/25 p-5">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Mode</p>
                        <p className="mt-3 text-lg font-semibold text-white">{assistant.status}</p>
                        <p className="mt-3 text-sm leading-7 text-white/65">{assistant.disclaimer}</p>
                    </article>

                    <article className="rounded-[1.5rem] border border-white/8 bg-black/25 p-5">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Source de verite</p>
                        <p className="mt-3 text-lg font-semibold text-white">FAQ, articles et glossaire</p>
                        <p className="mt-3 text-sm leading-7 text-white/65">
                            Les reponses viennent d&apos;abord de la base de connaissance. Si le moteur ne trouve pas
                            de source fiable, il reste cadre.
                        </p>
                    </article>
                </div>

                {preview ? (
                    <article className="rounded-[1.5rem] border border-red-400/15 bg-red-500/8 p-5">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-red-100/75">
                                    Si vous etes connecte
                                </p>
                                <h3 className="mt-2 text-xl font-semibold text-white">{preview.name}</h3>
                            </div>
                            <a
                                href={preview.profile_url}
                                className="inline-flex min-h-[42px] items-center justify-center rounded-[1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                            >
                                Voir le profil public
                            </a>
                        </div>

                        <div className="mt-5 grid gap-3 sm:grid-cols-4">
                            <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-3">
                                <p className="text-[11px] uppercase tracking-[0.14em] text-white/45">Ligue</p>
                                <p className="mt-2 text-lg font-semibold text-white">{preview.league}</p>
                            </div>
                            <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-3">
                                <p className="text-[11px] uppercase tracking-[0.14em] text-white/45">XP</p>
                                <p className="mt-2 text-lg font-semibold text-white">{preview.xp}</p>
                            </div>
                            <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-3">
                                <p className="text-[11px] uppercase tracking-[0.14em] text-white/45">Points</p>
                                <p className="mt-2 text-lg font-semibold text-white">{preview.points}</p>
                            </div>
                            <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-3">
                                <p className="text-[11px] uppercase tracking-[0.14em] text-white/45">Reward wallet</p>
                                <p className="mt-2 text-lg font-semibold text-white">{preview.reward_balance}</p>
                            </div>
                        </div>

                        {preview.suggestions?.length ? (
                            <div className="mt-5 space-y-2">
                                {preview.suggestions.map((suggestion) => (
                                    <div
                                        key={suggestion}
                                        className="rounded-[1rem] border border-white/8 bg-black/20 px-4 py-3 text-sm leading-6 text-white/75"
                                    >
                                        {suggestion}
                                    </div>
                                ))}
                            </div>
                        ) : null}
                    </article>
                ) : null}
            </article>

            <article className="space-y-4">
                <div className="rounded-[1.7rem] border border-white/10 bg-black/25 p-5">
                    <label className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                        Poser une question
                    </label>
                    <textarea
                        value={message}
                        onChange={(event) => setMessage(event.target.value)}
                        placeholder={assistant.placeholder}
                        className="mt-4 min-h-[140px] w-full resize-none rounded-[1.3rem] border border-white/8 bg-black/30 px-4 py-4 text-sm leading-7 text-white outline-none placeholder:text-white/35"
                    />
                    <div className="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            onClick={() => ask()}
                            disabled={loading}
                            className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-red-400/35 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {loading ? 'Recherche en cours...' : 'Demander a l assistant'}
                        </button>
                        <button
                            type="button"
                            onClick={() => {
                                setMessage('');
                                setResult(null);
                                setError(null);
                            }}
                            className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                        >
                            Reinitialiser
                        </button>
                    </div>
                </div>

                <div className="rounded-[1.7rem] border border-white/10 bg-black/25 p-5">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Questions rapides</p>
                    <div className="mt-4 flex flex-wrap gap-2">
                        {prompts.map((prompt) => (
                            <button
                                key={prompt}
                                type="button"
                                onClick={() => ask(prompt)}
                                className="inline-flex rounded-full border border-white/10 bg-white/[0.03] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/75 transition hover:border-red-400/35 hover:text-white"
                            >
                                {prompt}
                            </button>
                        ))}
                    </div>
                </div>

                <div className="rounded-[1.7rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(7,8,12,0.96))] p-5">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Reponse</p>

                    {error ? (
                        <div className="mt-4 rounded-[1.2rem] border border-red-400/25 bg-red-500/10 px-4 py-4 text-sm text-red-100">
                            {error}
                        </div>
                    ) : null}

                    {result ? (
                        <div className="mt-4 space-y-4">
                            <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-4">
                                <p className="text-sm leading-7 text-white">{result.answer}</p>
                            </div>

                            {result.details?.length ? (
                                <div className="space-y-3">
                                    {result.details.map((detail) => (
                                        <div
                                            key={detail}
                                            className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-4 text-sm leading-7 text-white/72"
                                        >
                                            {detail}
                                        </div>
                                    ))}
                                </div>
                            ) : null}

                            {result.sources?.length ? (
                                <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-4">
                                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                        Sources
                                    </p>
                                    <div className="mt-3 space-y-2">
                                        {result.sources.map((source) => (
                                            <a
                                                key={`${source.type}-${source.title}`}
                                                href={source.url}
                                                className="block rounded-[1rem] border border-white/6 bg-white/[0.03] px-3 py-3 text-sm text-white/80 transition hover:border-red-400/35"
                                            >
                                                {source.title}
                                                {source.category ? ` · ${source.category}` : ''}
                                            </a>
                                        ))}
                                    </div>
                                </div>
                            ) : null}

                            {result.next_steps?.length ? (
                                <div className="rounded-[1.2rem] border border-white/8 bg-black/20 px-4 py-4">
                                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                        Que faire ensuite
                                    </p>
                                    <ul className="mt-3 space-y-2 text-sm leading-7 text-white/72">
                                        {result.next_steps.map((step) => (
                                            <li key={step}>{step}</li>
                                        ))}
                                    </ul>
                                </div>
                            ) : null}
                        </div>
                    ) : (
                        <div className="mt-4 rounded-[1.2rem] border border-dashed border-white/10 bg-black/20 px-4 py-8 text-sm leading-7 text-white/58">
                            Posez une question concrete sur les points, les missions, les matchs, le profil, les
                            clips, les cadeaux ou la logique globale d ERAH.
                        </div>
                    )}
                </div>
            </article>
        </section>
    );
}
