import React from 'react';
import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Badge from '../../../Components/Badge';
import Button from '../../../Components/Button';
import EmptyState from '../../../Components/EmptyState';
import Input from '../../../Components/Input';
import HelpCenterLayout from '../../../Layouts/HelpCenterLayout';
import HelpAssistantCard from '../components/HelpAssistantCard';

function ArticleRow({ article }) {
    return (
        <Link
            href={article.url}
            className="block rounded-[1.5rem] border border-ui-border/12 bg-black/20 p-5 transition hover:border-red-400/30"
        >
            <div className="flex flex-wrap items-center gap-2">
                {article.is_faq ? <Badge variant="warning">FAQ</Badge> : null}
                {article.is_featured ? <Badge variant="league">Prioritaire</Badge> : null}
            </div>
            <h3 className="mt-3 text-lg font-semibold text-white">{article.title}</h3>
            <p className="mt-3 text-sm leading-7 text-ui-muted">{article.summary || article.short_answer}</p>
        </Link>
    );
}

export default function HelpCategoryPage({ page }) {
    const [search, setSearch] = useState(page.filters.search);

    useEffect(() => {
        setSearch(page.filters.search);
    }, [page.filters.search]);

    return (
        <HelpCenterLayout title={page.category.title} subtitle={page.category.description}>
            <section className="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_340px]">
                <article className="rounded-[1.75rem] border border-ui-border/12 bg-[linear-gradient(135deg,rgba(255,255,255,0.03),rgba(225,6,19,0.08),rgba(15,17,26,0.96))] p-6">
                    <div className="flex flex-wrap items-center gap-3">
                        <Link href="/aide" className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted hover:text-white">
                            Centre d aide
                        </Link>
                        <span className="text-ui-muted">/</span>
                        <span className="text-xs font-semibold uppercase tracking-[0.16em] text-red-200">{page.category.title}</span>
                    </div>

                    <h2 className="mt-4 text-3xl font-semibold text-white">{page.category.title}</h2>
                    <p className="mt-3 text-sm leading-7 text-ui-muted">{page.category.intro || page.category.description}</p>

                    <form
                        className="mt-6 space-y-4"
                        onSubmit={(event) => {
                            event.preventDefault();
                            router.get(page.category.url, search ? { search } : {}, { preserveScroll: true, replace: true });
                        }}
                    >
                        <Input
                            label="Recherche dans cette categorie"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Chercher une question dans cette categorie"
                        />
                        <div className="flex flex-wrap gap-3">
                            <Button type="submit">Rechercher</Button>
                            <Link href="/aide" className="toy-pill-btn toy-pill-btn-secondary">
                                Retour au centre d aide
                            </Link>
                        </div>
                    </form>
                </article>

                <HelpAssistantCard
                    assistant={{
                        title: 'Reponse rapide IA',
                        description: 'Les articles de cette categorie servent deja de source de verite pour la future assistance ERAH.',
                        status: 'Preparation IA',
                    }}
                />
            </section>

            <section className="space-y-5">
                <div className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Articles</p>
                        <h3 className="mt-2 text-2xl font-semibold text-white">Contenu de la categorie</h3>
                    </div>
                    <Badge variant="status">{page.articles.meta.total} article(s)</Badge>
                </div>

                {page.articles.data.length > 0 ? (
                    <div className="grid gap-3 lg:grid-cols-2">
                        {page.articles.data.map((article) => (
                            <ArticleRow key={article.id} article={article} />
                        ))}
                    </div>
                ) : (
                    <EmptyState title="Aucun article" description="Cette categorie n a pas encore de contenu publie pour cette recherche." />
                )}

                {(page.articles.links.prev || page.articles.links.next) && (
                    <div className="flex flex-wrap gap-3">
                        {page.articles.links.prev ? (
                            <a href={page.articles.links.prev} className="toy-pill-btn toy-pill-btn-secondary">
                                Page precedente
                            </a>
                        ) : null}
                        {page.articles.links.next ? (
                            <a href={page.articles.links.next} className="toy-pill-btn toy-pill-btn-primary">
                                Page suivante
                            </a>
                        ) : null}
                    </div>
                )}
            </section>

            <section className="grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
                <article className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-5">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Questions frequentes</p>
                    <div className="mt-5 space-y-3">
                        {page.faq.length > 0 ? (
                            page.faq.map((faq) => <ArticleRow key={faq.id} article={faq} />)
                        ) : (
                            <EmptyState title="Aucune FAQ" description="Cette categorie n a pas encore de FAQ mise en avant." />
                        )}
                    </div>
                </article>

                <article className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-5">
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Autres categories</p>
                    <div className="mt-5 space-y-3">
                        {page.relatedCategories.map((category) => (
                            <Link
                                key={category.id}
                                href={category.url}
                                className="block rounded-2xl border border-white/6 bg-black/20 px-4 py-3 transition hover:border-red-400/30"
                            >
                                <p className="text-sm font-semibold text-white">{category.title}</p>
                                <p className="mt-1 text-xs text-ui-muted">{category.articles_count} articles</p>
                            </Link>
                        ))}
                    </div>
                </article>
            </section>
        </HelpCenterLayout>
    );
}
