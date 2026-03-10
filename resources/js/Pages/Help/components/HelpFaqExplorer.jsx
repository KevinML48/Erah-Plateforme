import React from 'react';
import { useEffect, useMemo, useState } from 'react';

function paragraphs(text) {
    return String(text || '')
        .split(/\n{2,}/)
        .map((block) => block.trim())
        .filter(Boolean);
}

export default function HelpFaqExplorer({
    faq,
    search,
    onSearchChange,
    initialCategory = null,
    initialArticle = null,
}) {
    const [activeCategory, setActiveCategory] = useState(initialCategory);
    const [openSlug, setOpenSlug] = useState(initialArticle);

    useEffect(() => {
        setActiveCategory(initialCategory);
    }, [initialCategory]);

    useEffect(() => {
        if (initialArticle) {
            setOpenSlug(initialArticle);
        }
    }, [initialArticle]);

    const filteredItems = useMemo(() => {
        const query = String(search || '').toLowerCase().trim();

        return (faq?.items ?? []).filter((item) => {
            if (activeCategory && item.category?.slug !== activeCategory) {
                return false;
            }

            if (!query) {
                return true;
            }

            const haystack = [
                item.title,
                item.summary,
                item.short_answer,
                item.body,
                ...(item.keywords ?? []),
                item.category?.title,
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            return haystack.includes(query);
        });
    }, [activeCategory, faq?.items, search]);

    return (
        <section id="faq-center" className="space-y-6">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">{faq.eyebrow}</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    {faq.title}
                </h2>
                <p className="mt-4 text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                    {faq.description}
                </p>
            </div>

            <div className="grid gap-5 xl:grid-cols-[minmax(0,0.9fr)_minmax(380px,1.1fr)]">
                <article className="rounded-[1.8rem] border border-white/10 bg-black/25 p-5">
                    <div>
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                            Recherche FAQ
                        </p>
                        <div className="mt-4 rounded-[1.2rem] border border-white/10 bg-white/[0.03] p-2">
                            <input
                                value={search ?? ''}
                                onChange={(event) => onSearchChange(event.target.value)}
                                placeholder="Ex: Comment gagner des points, comment voir les matchs a venir..."
                                className="min-h-[52px] w-full rounded-[1rem] border border-transparent bg-transparent px-4 text-sm text-white outline-none placeholder:text-white/35"
                            />
                        </div>
                    </div>

                    <div className="mt-5 flex gap-2 overflow-x-auto pb-1">
                        <button
                            type="button"
                            onClick={() => setActiveCategory(null)}
                            className={[
                                'inline-flex shrink-0 rounded-full border px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] transition',
                                activeCategory === null
                                    ? 'border-red-400/35 bg-red-500/10 text-red-100'
                                    : 'border-white/10 bg-white/[0.03] text-white/70 hover:border-red-400/35 hover:text-white',
                            ].join(' ')}
                        >
                            Toutes
                        </button>
                        {faq.categories?.map((category) => (
                            <button
                                key={category.slug}
                                type="button"
                                onClick={() => setActiveCategory(category.slug)}
                                className={[
                                    'inline-flex shrink-0 rounded-full border px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] transition',
                                    activeCategory === category.slug
                                        ? 'border-red-400/35 bg-red-500/10 text-red-100'
                                        : 'border-white/10 bg-white/[0.03] text-white/70 hover:border-red-400/35 hover:text-white',
                                ].join(' ')}
                            >
                                {category.title} · {category.count}
                            </button>
                        ))}
                    </div>

                    <div className="mt-6 space-y-3">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                            Les plus frequentes
                        </p>
                        {(faq.featured ?? []).map((item) => (
                            <button
                                key={item.slug}
                                type="button"
                                onClick={() => setOpenSlug(item.slug)}
                                className={[
                                    'w-full rounded-[1.2rem] border px-4 py-4 text-left transition',
                                    openSlug === item.slug
                                        ? 'border-red-400/35 bg-red-500/10'
                                        : 'border-white/8 bg-white/[0.03] hover:border-red-400/35',
                                ].join(' ')}
                            >
                                <p className="text-sm font-semibold text-white">{item.title}</p>
                                <p className="mt-2 text-sm leading-6 text-white/62">{item.short_answer || item.summary}</p>
                            </button>
                        ))}
                    </div>
                </article>

                <article className="rounded-[1.8rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(9,10,15,0.98))] p-5">
                    <div className="flex items-center justify-between gap-3">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                            Reponses detaillees
                        </p>
                        <span className="inline-flex rounded-full border border-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/45">
                            {filteredItems.length} resultat(s)
                        </span>
                    </div>

                    <div className="mt-4 space-y-3">
                        {filteredItems.map((item) => {
                            const opened = openSlug === item.slug;
                            const blocks = paragraphs(item.body);

                            return (
                                <article
                                    key={item.slug}
                                    className={[
                                        'rounded-[1.4rem] border transition',
                                        opened ? 'border-red-400/30 bg-red-500/8' : 'border-white/8 bg-black/20',
                                    ].join(' ')}
                                >
                                    <button
                                        type="button"
                                        onClick={() => setOpenSlug((value) => (value === item.slug ? null : item.slug))}
                                        className="w-full px-4 py-4 text-left"
                                    >
                                        <div className="flex flex-wrap items-center gap-2">
                                            {item.category?.title ? (
                                                <span className="inline-flex rounded-full border border-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/45">
                                                    {item.category.title}
                                                </span>
                                            ) : null}
                                            {item.is_featured ? (
                                                <span className="inline-flex rounded-full border border-red-400/25 bg-red-500/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-red-100">
                                                    Prioritaire
                                                </span>
                                            ) : null}
                                        </div>
                                        <h3 className="mt-3 text-lg font-semibold text-white">{item.title}</h3>
                                        <p className="mt-2 text-sm leading-7 text-white/70">
                                            {item.short_answer || item.summary}
                                        </p>
                                    </button>

                                    {opened ? (
                                        <div className="border-t border-white/8 px-4 py-4">
                                            <div className="space-y-4">
                                                {blocks.map((block) => (
                                                    <p key={block} className="text-sm leading-7 text-white/82">
                                                        {block}
                                                    </p>
                                                ))}
                                            </div>

                                            {item.support?.steps?.length ? (
                                                <div className="mt-5 rounded-[1.2rem] border border-white/8 bg-black/20 p-4">
                                                    <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/45">
                                                        Etapes
                                                    </p>
                                                    <ol className="mt-3 space-y-2 text-sm leading-7 text-white/78">
                                                        {item.support.steps.map((step, index) => (
                                                            <li key={step}>
                                                                {index + 1}. {step}
                                                            </li>
                                                        ))}
                                                    </ol>
                                                </div>
                                            ) : null}

                                            {item.support?.tips?.length ? (
                                                <div className="mt-4 rounded-[1.2rem] border border-red-400/18 bg-red-500/8 p-4">
                                                    <p className="text-[11px] font-semibold uppercase tracking-[0.16em] text-red-100/75">
                                                        Conseil
                                                    </p>
                                                    <div className="mt-3 space-y-2 text-sm leading-7 text-red-50/88">
                                                        {item.support.tips.map((tip) => (
                                                            <p key={tip}>{tip}</p>
                                                        ))}
                                                    </div>
                                                </div>
                                            ) : null}

                                            {item.cta_url ? (
                                                <a
                                                    href={item.cta_url}
                                                    className="mt-5 inline-flex min-h-[46px] items-center justify-center rounded-[1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35 hover:bg-white/[0.04]"
                                                >
                                                    {item.cta_label || 'Ouvrir la page concernee'}
                                                </a>
                                            ) : null}
                                        </div>
                                    ) : null}
                                </article>
                            );
                        })}

                        {!filteredItems.length ? (
                            <div className="rounded-[1.2rem] border border-dashed border-white/10 bg-black/20 px-4 py-8 text-sm leading-7 text-white/58">
                                Aucune reponse ne correspond a votre recherche. Essayez un autre mot-cle ou revenez
                                aux categories principales.
                            </div>
                        ) : null}
                    </div>
                </article>
            </div>
        </section>
    );
}
