import React from 'react';
import { useMemo, useState } from 'react';

import HelpCenterLayout from '../../../Layouts/HelpCenterLayout';
import GuidedTourNavigator from './GuidedTourNavigator';
import HelpAssistantCard from './HelpAssistantCard';
import HelpFaqExplorer from './HelpFaqExplorer';
import HelpFeatureGrid from './HelpFeatureGrid';
import HelpSearchHero from './HelpSearchHero';
import HelpSectionNav from './HelpSectionNav';
import HelpVideoBlock from './HelpVideoBlock';

function IntroPanels({ intro }) {
    return (
        <section id="discover-erah" className="space-y-6">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">{intro.eyebrow}</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    {intro.title}
                </h2>
                <p className="mt-4 text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                    {intro.description}
                </p>
            </div>

            <div className="grid gap-4 lg:grid-cols-3">
                {intro.panels.map((panel) => (
                    <article
                        key={panel.title}
                        className="rounded-[1.8rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(9,10,15,0.98))] p-5"
                    >
                        <h3 className="text-xl font-semibold text-white">{panel.title}</h3>
                        <p className="mt-4 text-sm leading-7 text-white/70">{panel.description}</p>
                        <div className="mt-5 space-y-2">
                            {panel.items.map((item) => (
                                <div
                                    key={item}
                                    className="rounded-[1rem] border border-white/8 bg-black/20 px-3 py-3 text-sm leading-6 text-white/78"
                                >
                                    {item}
                                </div>
                            ))}
                        </div>
                    </article>
                ))}
            </div>
        </section>
    );
}

function CategoryExplorer({ categories }) {
    return (
        <section className="space-y-6">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Grandes categories</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    Des portes d entree claires pour ne pas se perdre dans la plateforme.
                </h2>
                <p className="mt-4 text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                    Chaque categorie vous emmene ensuite vers les bonnes questions, les bons repères et les bonnes pages.
                </p>
            </div>

            <div className="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                {categories.map((category) => (
                    <a
                        key={category.slug}
                        href={category.url}
                        className="rounded-[1.8rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(7,8,12,0.98))] p-5 transition hover:border-red-400/30 hover:-translate-y-0.5"
                    >
                        <div className="flex items-center justify-between gap-3">
                            <span className="inline-flex rounded-full border border-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/45">
                                {category.bucket}
                            </span>
                            <span className="text-sm font-semibold text-white/55">{category.articles_count} articles</span>
                        </div>
                        <h3 className="mt-4 text-xl font-semibold text-white">{category.title}</h3>
                        <p className="mt-3 text-sm leading-7 text-white/70">{category.description}</p>
                        {category.articles_preview?.length ? (
                            <div className="mt-5 space-y-2">
                                {category.articles_preview.map((article) => (
                                    <div
                                        key={article.id}
                                        className="rounded-[1rem] border border-white/6 bg-black/20 px-3 py-3 text-sm text-white/82"
                                    >
                                        {article.title}
                                    </div>
                                ))}
                            </div>
                        ) : null}
                    </a>
                ))}
            </div>
        </section>
    );
}

function QuickQuestions({ items }) {
    return (
        <section id="quick-questions" className="space-y-6">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Questions rapides</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    Les demandes qui reviennent le plus souvent quand on decouvre ERAH.
                </h2>
            </div>

            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                {items.map((item) => (
                    <a
                        key={item.id}
                        href={item.href}
                        className="rounded-[1.6rem] border border-white/10 bg-black/25 p-5 transition hover:border-red-400/30"
                    >
                        <h3 className="text-lg font-semibold text-white">{item.title}</h3>
                        <p className="mt-3 text-sm leading-7 text-white/70">{item.answer}</p>
                    </a>
                ))}
            </div>
        </section>
    );
}

function GlossarySection({ glossary }) {
    if (!glossary?.length) {
        return null;
    }

    return (
        <section className="space-y-5">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Glossaire</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[2.8rem]">
                    Quelques termes a connaitre pour lire la plateforme sans confusion.
                </h2>
            </div>

            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {glossary.map((term) => (
                    <article
                        key={term.slug}
                        className="rounded-[1.5rem] border border-white/10 bg-black/20 p-4"
                    >
                        <h3 className="text-lg font-semibold text-white">{term.term}</h3>
                        <p className="mt-3 text-sm leading-7 text-white/70">{term.short_answer || term.definition}</p>
                    </article>
                ))}
            </div>
        </section>
    );
}

function FooterCta({ footerCta, mode }) {
    return mode === 'console' ? (
        <section className="rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(255,255,255,0.03),rgba(225,6,19,0.08),rgba(10,11,16,0.98))] p-6 sm:p-8">
            <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">{footerCta.eyebrow}</p>
            <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                {footerCta.title}
            </h2>
            <p className="mt-4 max-w-3xl text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                {footerCta.description}
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
                <a
                    href={footerCta.primary_url}
                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-red-400/35 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500"
                >
                    {footerCta.primary_label}
                </a>
                <a
                    href={footerCta.secondary_url}
                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                >
                    {footerCta.secondary_label}
                </a>
            </div>
        </section>
    ) : (
        <section className="rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(255,255,255,0.03),rgba(225,6,19,0.08),rgba(10,11,16,0.98))] p-6 sm:p-8">
            <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">{footerCta.eyebrow}</p>
            <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                {footerCta.title}
            </h2>
            <p className="mt-4 max-w-3xl text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                {footerCta.description}
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
                <a
                    href={footerCta.login_url}
                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                >
                    {footerCta.login_label}
                </a>
                <a
                    href={footerCta.register_url}
                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-red-400/35 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500"
                >
                    {footerCta.register_label}
                </a>
            </div>
        </section>
    );
}

export default function HelpHubPage({ page, mode = 'public' }) {
    const [search, setSearch] = useState(page.filters?.search ?? '');

    return (
        <HelpCenterLayout
            mode={mode === 'console' ? 'console' : 'public'}
            title={page.hero.title}
            subtitle={page.hero.subtitle}
        >
            <HelpSearchHero
                hero={page.hero}
                searchValue={search}
                onSearchChange={setSearch}
                onSearchSubmit={() => {
                    const target = document.getElementById('faq-center');
                    target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }}
            />

            <HelpSectionNav items={page.sectionNav} />

            <IntroPanels intro={page.intro} />

            <GuidedTourNavigator journey={page.starterJourney} />

            <HelpFeatureGrid featureGrid={page.featureGrid} />

            <CategoryExplorer categories={page.categories} />

            <QuickQuestions items={page.quickQuestions} />

            <HelpVideoBlock video={page.video} />

            <HelpFaqExplorer
                faq={page.faq}
                search={search}
                onSearchChange={setSearch}
                initialCategory={page.filters?.category}
                initialArticle={page.filters?.article}
            />

            <GlossarySection glossary={page.glossary} />

            {mode === 'console' && page.consoleLinks?.length ? (
                <section className="space-y-5">
                    <div className="max-w-3xl">
                        <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Acces contextuels</p>
                        <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[2.8rem]">
                            Relancez le bon module sans refaire tout le chemin.
                        </h2>
                    </div>
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        {page.consoleLinks.map((link) => (
                            <a
                                key={link.url}
                                href={link.url}
                                className="rounded-[1.5rem] border border-white/10 bg-black/20 p-4 transition hover:border-red-400/30"
                            >
                                <h3 className="text-lg font-semibold text-white">{link.title}</h3>
                                <p className="mt-3 text-sm leading-7 text-white/70">{link.description}</p>
                            </a>
                        ))}
                    </div>
                </section>
            ) : null}

            <HelpAssistantCard assistant={page.assistant} />

            <FooterCta footerCta={page.footerCta} mode={mode} />
        </HelpCenterLayout>
    );
}
