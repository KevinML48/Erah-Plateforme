import React from 'react';
export default function HelpVideoBlock({ video }) {
    if (!video) {
        return null;
    }

    return (
        <section id="video-help" className="grid gap-5 xl:grid-cols-[minmax(0,1.1fr)_360px]">
            <article className="rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.03),rgba(9,10,15,0.98))] p-6 sm:p-8">
                <p className="text-[11px] font-semibold uppercase tracking-[0.22em] text-red-200">Bloc video</p>
                <h2 className="mt-3 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-[3rem]">
                    {video.title}
                </h2>
                <p className="mt-4 max-w-3xl text-sm leading-7 text-white/70 sm:text-base sm:leading-8">
                    {video.description}
                </p>

                <div className="mt-6 overflow-hidden rounded-[1.7rem] border border-white/8 bg-black/30">
                    {video.embed_url ? (
                        <div className="aspect-video">
                            <iframe
                                src={video.embed_url}
                                title={video.title}
                                className="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen
                            />
                        </div>
                    ) : (
                        <div className="flex aspect-video items-center justify-center px-6 text-center text-sm leading-7 text-white/58">
                            {video.fallback}
                        </div>
                    )}
                </div>
            </article>

            <aside className="rounded-[2rem] border border-white/10 bg-black/25 p-6">
                <p className="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">A retenir</p>
                <div className="mt-5 space-y-3">
                    {video.highlights?.map((item) => (
                        <article
                            key={item}
                            className="rounded-[1.2rem] border border-white/8 bg-white/[0.03] px-4 py-4 text-sm leading-7 text-white/72"
                        >
                            {item}
                        </article>
                    ))}
                </div>
            </aside>
        </section>
    );
}
