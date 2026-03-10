import React from 'react';
export default function HelpSearchHero({
    hero,
    searchValue,
    onSearchChange,
    onSearchSubmit,
}) {
    return (
        <section className="grid gap-5 lg:grid-cols-[minmax(0,1.2fr)_380px]">
            <article className="overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(84,8,14,0.55),rgba(14,15,23,0.94)_38%,rgba(8,9,14,0.98))] p-6 shadow-[0_30px_90px_rgba(0,0,0,0.35)] sm:p-8 lg:p-10">
                <div className="max-w-4xl">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.28em] text-red-200">
                        {hero.eyebrow}
                    </p>
                    <h1 className="mt-4 max-w-4xl text-4xl font-semibold leading-[0.95] tracking-[-0.04em] text-white sm:text-5xl lg:text-6xl">
                        {hero.title}
                    </h1>
                    <p className="mt-5 max-w-3xl text-sm leading-7 text-white/72 sm:text-base sm:leading-8">
                        {hero.subtitle}
                    </p>
                    <p className="mt-4 max-w-3xl text-sm leading-7 text-red-100/80">
                        {hero.microcopy}
                    </p>
                </div>

                <form
                    className="mt-8 space-y-4"
                    onSubmit={(event) => {
                        event.preventDefault();
                        onSearchSubmit();
                    }}
                >
                    <div className="rounded-[1.6rem] border border-white/10 bg-black/30 p-2 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                        <div className="flex flex-col gap-2 sm:flex-row">
                            <input
                                value={searchValue}
                                onChange={(event) => onSearchChange(event.target.value)}
                                placeholder={hero.search_placeholder}
                                className="min-h-[56px] w-full rounded-[1.2rem] border border-transparent bg-transparent px-5 text-base text-white outline-none placeholder:text-white/35"
                            />
                            <div className="flex flex-wrap gap-2">
                                <a
                                    href={hero.primary_cta.href}
                                    className="inline-flex min-h-[56px] items-center justify-center rounded-[1.2rem] border border-red-400/35 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500"
                                >
                                    {hero.primary_cta.label}
                                </a>
                                <button
                                    type="submit"
                                    className="inline-flex min-h-[56px] items-center justify-center rounded-[1.2rem] border border-white/12 bg-white/[0.05] px-5 text-sm font-semibold text-white transition hover:border-red-400/35 hover:bg-white/[0.08]"
                                >
                                    Rechercher
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div className="mt-5 flex flex-wrap gap-2">
                    {hero.search_tags?.map((tag) => (
                        <button
                            key={tag}
                            type="button"
                            onClick={() => onSearchChange(tag)}
                            className="inline-flex rounded-full border border-white/10 bg-white/[0.04] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/72 transition hover:border-red-400/35 hover:text-white"
                        >
                            {tag}
                        </button>
                    ))}
                </div>

                <div className="mt-8 grid gap-3 border-t border-white/8 pt-6 sm:grid-cols-3">
                    {hero.stats?.map((item) => (
                        <div key={item.label} className="rounded-[1.25rem] border border-white/8 bg-white/[0.03] px-4 py-4">
                            <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                {item.label}
                            </p>
                            <p className="mt-3 text-3xl font-semibold text-white">{item.value}</p>
                        </div>
                    ))}
                </div>
            </article>

            <aside className="space-y-4 rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(10,11,17,0.96))] p-6 sm:p-8">
                <div>
                    <p className="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/45">
                        Lecture immediate
                    </p>
                    <h2 className="mt-3 text-2xl font-semibold text-white sm:text-[2rem]">
                        Une seule porte d entree pour comprendre ERAH.
                    </h2>
                </div>

                <p className="text-sm leading-7 text-white/70">
                    Le centre d aide sert a orienter un visiteur, rassurer un membre, et fournir la source
                    de verite du futur assistant ERAH.
                </p>

                <div className="space-y-3">
                    {hero.panel?.items?.map((item) => (
                        <article
                            key={item}
                            className="rounded-[1.4rem] border border-white/8 bg-black/25 px-4 py-4 text-sm leading-7 text-white/80"
                        >
                            {item}
                        </article>
                    ))}
                </div>

                <a
                    href={hero.secondary_cta.href}
                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35 hover:bg-white/[0.04]"
                >
                    {hero.secondary_cta.label}
                </a>
            </aside>
        </section>
    );
}
