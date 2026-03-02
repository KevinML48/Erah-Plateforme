import { Link } from '@inertiajs/react';
import { useState } from 'react';

import PillTabs from './PillTabs';
import PillButton from './PillButton';
import PillTabBar from './PillTabBar';
import PlusDrawer from './PlusDrawer';

function IconBell() {
    return (
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="1.8">
            <path d="M14.86 17H5a1 1 0 0 1-.8-1.6l1.2-1.6V10a6 6 0 1 1 12 0v3.8l1.2 1.6a1 1 0 0 1-.8 1.6H14.86" />
            <path d="M9.5 19a2.5 2.5 0 0 0 5 0" />
        </svg>
    );
}

function IconUser() {
    return (
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="1.8">
            <circle cx="12" cy="8" r="3.2" />
            <path d="M5.5 18.5a6.5 6.5 0 0 1 13 0" />
        </svg>
    );
}

function geoFromSection(section) {
    if (['clips', 'matches', 'wallet', 'bets'].includes(section)) {
        return 'library';
    }

    if (['settings', 'notifications', 'profile', 'admin'].includes(section)) {
        return 'profile';
    }

    return 'home';
}

export default function AppShell({
    title,
    subtitle,
    section = 'dashboard',
    user,
    hideNavigation = false,
    flash = {},
    topTabs,
    topTabsActive,
    onTopTabsChange,
    children,
}) {
    const [plusOpen, setPlusOpen] = useState(false);
    const geo = geoFromSection(section);

    return (
        <div className="toy-shell" data-geo={geo}>
            <header className="toy-topbar">
                <div className="toy-topbar-inner">
                    <div className="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                        <div className="min-w-0">
                            <p className="text-[11px] font-bold uppercase tracking-[0.18em] text-ui-muted">ERAH Arena</p>
                            <h1 className="truncate font-display text-2xl font-bold leading-none text-white">{title}</h1>
                            {subtitle && <p className="mt-1 text-xs text-ui-muted">{subtitle}</p>}
                        </div>

                        {!hideNavigation && user && (
                            <div className="flex items-center gap-2">
                                <Link href="/app/notifications" className="toy-pill-btn toy-pill-btn-secondary h-10 w-10 p-0" aria-label="Notifications">
                                    <IconBell />
                                </Link>
                                <Link href="/profile" className="toy-pill-btn toy-pill-btn-secondary gap-2 px-3 py-2 text-xs" aria-label="Profil">
                                    <IconUser />
                                    <span className="hidden sm:inline">{user.name}</span>
                                </Link>
                            </div>
                        )}
                    </div>

                    {topTabs?.length > 0 && (
                        <div className="px-4 pb-3 sm:px-5">
                            <PillTabs items={topTabs} active={topTabsActive} onChange={onTopTabsChange} />
                        </div>
                    )}
                </div>
            </header>

            <main className="toy-content">
                {(flash.success || flash.error || flash.status) && (
                    <div className="mb-3 space-y-2">
                        {flash.success && <div className="rounded-pill border border-emerald-500/25 bg-emerald-500/15 px-4 py-2 text-sm text-emerald-100">{flash.success}</div>}
                        {flash.error && <div className="rounded-pill border border-red-500/25 bg-red-500/15 px-4 py-2 text-sm text-red-100">{flash.error}</div>}
                        {flash.status && <div className="rounded-pill border border-ui-border/20 bg-ui-surface px-4 py-2 text-sm text-ui-text">{flash.status}</div>}
                    </div>
                )}

                <div className="toy-board">{children}</div>
            </main>

            {!hideNavigation && user && (
                <>
                    <PillTabBar onOpenPlus={() => setPlusOpen(true)} />
                    <PlusDrawer open={plusOpen} onClose={() => setPlusOpen(false)} user={user} />
                </>
            )}
        </div>
    );
}

