import React from 'react';
import { useMemo, useState } from 'react';

function StepBadge({ label }) {
    return (
        <span className="inline-flex rounded-full border border-red-400/25 bg-red-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-red-100">
            {label}
        </span>
    );
}

export default function GuidedTourNavigator({ journey }) {
    const steps = journey?.steps ?? [];
    const [activeIndex, setActiveIndex] = useState(0);

    const current = steps[activeIndex];
    const progress = useMemo(() => {
        if (!steps.length) {
            return 0;
        }

        return Math.round(((activeIndex + 1) / steps.length) * 100);
    }, [activeIndex, steps.length]);

    if (!current) {
        return null;
    }

    return (
        <section id="starter-journey" className="space-y-6">
            <div className="flex flex-wrap items-end justify-between gap-4">
                <div className="max-w-3xl">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">
                        {journey.eyebrow}
                    </p>
                    <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3.1rem]">
                        {journey.title}
                    </h2>
                    <p className="mt-4 text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                        {journey.description}
                    </p>
                </div>

                <div className="min-w-[180px] rounded-[1.4rem] border border-white/8 bg-white/[0.03] px-5 py-4 text-right">
                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Progression</p>
                    <p className="mt-2 text-3xl font-semibold text-white">{progress}%</p>
                </div>
            </div>

            <div className="h-2 rounded-full bg-white/8">
                <div className="h-2 rounded-full bg-red-500 transition-all duration-300" style={{ width: `${progress}%` }} />
            </div>

            <div className="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]">
                <aside className="space-y-3">
                    {steps.map((step, index) => (
                        <button
                            key={step.id}
                            type="button"
                            onClick={() => setActiveIndex(index)}
                            className={[
                                'w-full rounded-[1.5rem] border px-4 py-4 text-left transition',
                                index === activeIndex
                                    ? 'border-red-400/35 bg-red-500/10 shadow-[0_20px_40px_rgba(106,6,14,0.22)]'
                                    : 'border-white/8 bg-white/[0.03] hover:border-white/14 hover:bg-white/[0.05]',
                            ].join(' ')}
                        >
                            <div className="flex items-start justify-between gap-3">
                                <div>
                                    <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                        {step.progress_label}
                                    </p>
                                    <h3 className="mt-2 text-base font-semibold text-white">{step.title}</h3>
                                </div>
                                {index === activeIndex ? <StepBadge label="Actif" /> : null}
                            </div>
                            <p className="mt-3 text-sm leading-6 text-white/65">{step.summary}</p>
                        </button>
                    ))}
                </aside>

                <article className="rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(9,10,15,0.98))] p-6 sm:p-8">
                    <div className="grid gap-5 lg:grid-cols-[minmax(0,1.15fr)_280px]">
                        <div>
                            <StepBadge label={current.progress_label} />
                            <h3 className="mt-5 text-3xl font-semibold tracking-[-0.03em] text-white">
                                {current.title}
                            </h3>
                            <p className="mt-4 text-base leading-8 text-white/75">{current.summary}</p>
                            <p className="mt-5 text-sm leading-8 text-white/82 sm:text-base">
                                {current.body}
                            </p>

                            <div className="mt-6 flex flex-wrap gap-3">
                                <a
                                    href={current.cta_url}
                                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-red-400/35 bg-red-600 px-5 text-sm font-semibold text-white transition hover:bg-red-500"
                                >
                                    {current.cta_label}
                                </a>
                                <button
                                    type="button"
                                    onClick={() => setActiveIndex((value) => Math.max(0, value - 1))}
                                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                                >
                                    Precedent
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setActiveIndex((value) => Math.min(steps.length - 1, value + 1))}
                                    className="inline-flex min-h-[48px] items-center justify-center rounded-[1.1rem] border border-white/12 px-4 text-sm font-semibold text-white transition hover:border-red-400/35"
                                >
                                    Suivant
                                </button>
                            </div>
                        </div>

                        <div className="rounded-[1.6rem] border border-white/8 bg-black/25 p-5">
                            <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">
                                Bloc visuel
                            </p>
                            <h4 className="mt-3 text-xl font-semibold text-white">{current.visual_title}</h4>
                            <p className="mt-3 text-sm leading-7 text-white/72">{current.visual_body}</p>

                            <div className="mt-6 grid gap-3">
                                {steps.map((step) => (
                                    <div
                                        key={step.id}
                                        className={[
                                            'rounded-[1.1rem] border px-3 py-3 text-sm',
                                            step.id === current.id
                                                ? 'border-red-400/30 bg-red-500/10 text-white'
                                                : 'border-white/6 bg-white/[0.03] text-white/65',
                                        ].join(' ')}
                                    >
                                        {step.progress_label} · {step.title}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    );
}
