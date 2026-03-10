import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';

import Alert from '../Components/Alert';

const navLinks = [
    { label: 'Centre d aide', href: '/aide' },
    { label: 'FAQ', href: '/aide#faq-center' },
    { label: 'Clips', href: '/console/clips' },
    { label: 'Matchs', href: '/console/matches' },
    { label: 'Classements', href: '/console/leaderboards' },
];

export default function HelpCenterLayout({
    title,
    subtitle,
    mode = 'public',
    children,
}) {
    const page = usePage();
    const user = page.props.auth?.user ?? null;
    const flash = page.props.flash ?? {};

    return (
        <>
            <Head title={title}>
                {subtitle ? <meta name="description" content={subtitle} /> : null}
            </Head>

            <div className="relative min-h-screen overflow-hidden bg-[#07080c] text-white">
                <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(224,10,24,0.22),transparent_28%),radial-gradient(circle_at_top_right,rgba(255,255,255,0.03),transparent_24%),radial-gradient(circle_at_bottom_left,rgba(56,67,109,0.24),transparent_28%)]" />
                <div className="pointer-events-none absolute inset-0 opacity-50 [background-image:linear-gradient(rgba(255,255,255,0.025)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.025)_1px,transparent_1px)] [background-size:34px_34px]" />

                <div className="relative z-10">
                    <header className="border-b border-white/8 bg-black/35 backdrop-blur-xl">
                        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                            <div className="flex items-center gap-4">
                                <Link href="/aide" className="inline-flex items-center gap-3">
                                    <span className="inline-flex h-3 w-3 rounded-full bg-red-500 shadow-[0_0_18px_rgba(239,68,68,0.7)]" />
                                    <span className="text-sm font-semibold uppercase tracking-[0.22em] text-red-100">
                                        ERAH Help Center
                                    </span>
                                </Link>

                                {mode === 'console' ? (
                                    <span className="inline-flex rounded-full border border-red-400/25 bg-red-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-red-100">
                                        In-app
                                    </span>
                                ) : null}

                                {mode === 'admin' ? (
                                    <span className="inline-flex rounded-full border border-red-400/25 bg-red-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-red-100">
                                        Admin
                                    </span>
                                ) : null}
                            </div>

                            <div className="flex flex-wrap items-center gap-2">
                                {navLinks.map((item) => (
                                    <a
                                        key={item.href}
                                        href={item.href}
                                        className="inline-flex items-center rounded-full border border-white/8 bg-white/[0.03] px-3.5 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/75 transition hover:border-red-400/35 hover:text-white"
                                    >
                                        {item.label}
                                    </a>
                                ))}

                                {user ? (
                                    <a
                                        href={mode === 'admin' ? '/console/admin/dashboard' : '/console/dashboard'}
                                        className="inline-flex items-center rounded-full bg-white px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-black transition hover:bg-red-50"
                                    >
                                        Mon espace
                                    </a>
                                ) : (
                                    <>
                                        <a
                                            href="/login"
                                            className="inline-flex items-center rounded-full border border-white/12 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:border-red-400/35"
                                        >
                                            Connexion
                                        </a>
                                        <a
                                            href="/register"
                                            className="inline-flex items-center rounded-full bg-red-600 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-white transition hover:bg-red-500"
                                        >
                                            Inscription
                                        </a>
                                    </>
                                )}
                            </div>
                        </div>
                    </header>

                    <main className="mx-auto max-w-7xl space-y-8 px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
                        <Alert variant="success" message={flash.success} />
                        <Alert variant="error" message={flash.error} />
                        <Alert variant="info" message={flash.status} />

                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}
