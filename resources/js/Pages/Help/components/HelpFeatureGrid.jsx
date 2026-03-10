import React from 'react';
export default function HelpFeatureGrid({ featureGrid }) {
    if (!featureGrid) {
        return null;
    }

    return (
        <section id="platform-features" className="space-y-6">
            <div className="max-w-3xl">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">
                    {featureGrid.eyebrow}
                </p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    {featureGrid.title}
                </h2>
                <p className="mt-4 text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                    {featureGrid.description}
                </p>
            </div>

            <div className="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                {featureGrid.items.map((item) => (
                    <article
                        key={item.key}
                        className="rounded-[1.8rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(8,9,14,0.96))] p-5"
                    >
                        <div className="flex items-center justify-between gap-3">
                            <h3 className="text-xl font-semibold text-white">{item.title}</h3>
                            <span className="inline-flex rounded-full border border-white/8 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                {item.access}
                            </span>
                        </div>

                        <p className="mt-4 text-sm leading-7 text-white/70">{item.description}</p>

                        <ul className="mt-5 space-y-2 text-sm leading-7 text-white/82">
                            {item.bullets.map((bullet) => (
                                <li key={bullet} className="rounded-[1rem] border border-white/6 bg-black/20 px-3 py-2">
                                    {bullet}
                                </li>
                            ))}
                        </ul>

                        <a
                            href={item.href}
                            className="mt-6 inline-flex min-h-[46px] items-center justify-center rounded-[1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35 hover:bg-white/[0.04]"
                        >
                            {item.cta_label}
                        </a>
                    </article>
                ))}
            </div>
        </section>
    );
}
