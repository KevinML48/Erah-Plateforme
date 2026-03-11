import React from 'react';

export default function AssistantComposer({
    value,
    onChange,
    onSubmit,
    onPromptClick,
    prompts,
    disabled = false,
    loading = false,
}) {
    const placeholder = disabled
        ? "L assistant est desactive pour le moment."
        : 'Ecrivez votre message ici. Vous pouvez demander une explication, un conseil, un prochain pas ou un detail sur la plateforme.';

    return (
        <div className="rounded-[2rem] border border-white/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.05),rgba(8,9,12,0.96))] p-4 sm:p-5">
            <div className="mb-3 flex flex-wrap gap-2">
                {prompts.slice(0, 4).map((prompt) => (
                    <button
                        key={prompt}
                        type="button"
                        disabled={disabled || loading}
                        onClick={() => onPromptClick(prompt)}
                        className="inline-flex min-h-[38px] items-center rounded-full border border-white/10 bg-white/[0.04] px-3.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:border-red-400/30 hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {prompt}
                    </button>
                ))}
            </div>

            <form onSubmit={onSubmit} className="space-y-4">
                <textarea
                    value={value}
                    onChange={(event) => onChange(event.target.value)}
                    placeholder={placeholder}
                    disabled={disabled || loading}
                    className="min-h-[124px] w-full resize-y rounded-[1.6rem] border border-white/10 bg-black/30 px-4 py-4 text-sm leading-7 text-white placeholder:text-white/28 focus:border-red-400/35 focus:outline-none focus:ring-0 disabled:cursor-not-allowed disabled:opacity-55"
                />

                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div className="text-xs leading-5 text-white/42">
                        {loading ? 'Generation en cours...' : 'Reponse en direct, historique conserve par conversation.'}
                    </div>

                    <button
                        type="submit"
                        disabled={disabled || loading || !value.trim()}
                        className="inline-flex min-h-[48px] items-center justify-center rounded-full border border-red-400/30 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {loading ? 'Generation...' : 'Envoyer'}
                    </button>
                </div>
            </form>
        </div>
    );
}
