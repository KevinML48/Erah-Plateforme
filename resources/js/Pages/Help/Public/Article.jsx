import React from 'react';
import { Link } from '@inertiajs/react';

import Badge from '../../../Components/Badge';
import HelpCenterLayout from '../../../Layouts/HelpCenterLayout';
import HelpAssistantCard from '../components/HelpAssistantCard';

function paragraphs(text) {
    return String(text || '')
        .split(/\n{2,}/)
        .map((block) => block.trim())
        .filter(Boolean);
}

export default function HelpArticlePage({ page }) {
    const article = page.article;

    return (
        <HelpCenterLayout title={article.title} subtitle={article.summary || article.short_answer}>
            <section className="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_360px]">
                <article className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-6">
                    <div className="flex flex-wrap items-center gap-3">
                        <Link href="/aide" className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted hover:text-white">
                            Centre d aide
                        </Link>
                        <span className="text-ui-muted">/</span>
                        {article.category ? (
                            <>
                                <Link href={`/aide/categorie/${article.category.slug}`} className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted hover:text-white">
                                    {article.category.title}
                                </Link>
                                <span className="text-ui-muted">/</span>
                            </>
                        ) : null}
                        <span className="text-xs font-semibold uppercase tracking-[0.16em] text-red-200">Article</span>
                    </div>

                    <div className="mt-5 flex flex-wrap items-center gap-2">
                        {article.category?.title ? <Badge variant="status">{article.category.title}</Badge> : null}
                        {article.is_faq ? <Badge variant="warning">FAQ</Badge> : null}
                        {article.is_featured ? <Badge variant="league">Mis en avant</Badge> : null}
                    </div>

                    <h2 className="mt-5 text-3xl font-semibold text-white sm:text-4xl">{article.title}</h2>
                    <p className="mt-4 text-sm leading-7 text-ui-muted">{article.summary || article.short_answer}</p>

                    <div className="mt-8 space-y-5">
                        {paragraphs(article.body).map((block, index) => (
                            <p key={index} className="text-sm leading-8 text-white/90 sm:text-base">
                                {block}
                            </p>
                        ))}
                    </div>
                </article>

                <div className="space-y-4">
                    <article className="rounded-[1.75rem] border border-ui-border/12 bg-black/25 p-5">
                        <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Reponse courte</p>
                        <p className="mt-4 text-sm leading-7 text-white/90">{article.short_answer || article.summary}</p>

                        {article.keywords?.length ? (
                            <div className="mt-5 flex flex-wrap gap-2">
                                {article.keywords.map((keyword) => (
                                    <span key={keyword} className="inline-flex rounded-full border border-white/8 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-ui-muted">
                                        {keyword}
                                    </span>
                                ))}
                            </div>
                        ) : null}

                        {article.cta_url ? (
                            <a href={article.cta_url} className="toy-pill-btn toy-pill-btn-primary mt-6 inline-flex">
                                {article.cta_label || 'Ouvrir la page liee'}
                            </a>
                        ) : null}
                    </article>

                    <HelpAssistantCard
                        assistant={{
                            title: 'Base de connaissance IA',
                            description: 'La reponse courte, le corps de l article et ses mots cles sont deja prets pour une future recuperation documentaire.',
                            status: 'Source de verite',
                        }}
                    />
                </div>
            </section>

            <section className="grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
                <article className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-5">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Articles lies</p>
                    <div className="mt-5 grid gap-3 lg:grid-cols-2">
                        {page.relatedArticles.map((related) => (
                            <Link
                                key={related.id}
                                href={related.url}
                                className="block rounded-2xl border border-white/6 bg-black/20 p-4 transition hover:border-red-400/30"
                            >
                                <h3 className="text-sm font-semibold text-white">{related.title}</h3>
                                <p className="mt-2 text-sm leading-6 text-ui-muted">{related.summary || related.short_answer}</p>
                            </Link>
                        ))}
                    </div>
                </article>

                <article className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-5">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Glossaire</p>
                    <div className="mt-5 space-y-3">
                        {page.glossary.map((term) => (
                            <div key={term.id} className="rounded-2xl border border-white/6 bg-black/20 p-4">
                                <h3 className="text-sm font-semibold text-white">{term.term}</h3>
                                <p className="mt-2 text-sm leading-6 text-ui-muted">{term.short_answer || term.definition}</p>
                            </div>
                        ))}
                    </div>
                </article>
            </section>
        </HelpCenterLayout>
    );
}
