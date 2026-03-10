import React from 'react';
export default function HelpSectionNav({ items = [] }) {
    if (!items.length) {
        return null;
    }

    return (
        <nav className="sticky top-0 z-20 -mx-4 border-y border-white/8 bg-black/45 px-4 py-3 backdrop-blur-xl sm:mx-0 sm:rounded-full sm:border sm:bg-black/35">
            <div className="flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:justify-center sm:overflow-visible">
                {items.map((item) => (
                    <a
                        key={item.href}
                        href={item.href}
                        className="inline-flex shrink-0 rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/70 transition hover:border-red-400/35 hover:text-white"
                    >
                        {item.label}
                    </a>
                ))}
            </div>
        </nav>
    );
}
