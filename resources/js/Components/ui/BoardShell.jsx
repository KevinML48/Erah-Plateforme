import { useMemo, useState } from 'react';
import { usePage } from '@inertiajs/react';

import BoardBottomNav from './BoardBottomNav';
import BoardHeader from './BoardHeader';
import PlusDrawer from './PlusDrawer';
import PolygonBackground from './PolygonBackground';

function sectionFromUrl(url) {
    const segments = url.split('?')[0].split('/').filter(Boolean);
    if (!segments.length) {
        return 'dashboard';
    }

    if (segments[0] === 'app') {
        return segments[1] ?? 'dashboard';
    }

    return segments[0];
}

function themeFromSection(section) {
    if (['dashboard', 'clips', 'matches', 'leaderboards'].includes(section)) {
        return 'toy';
    }

    return 'erah';
}

export default function BoardShell({
    title,
    subtitle,
    hideNavigation = false,
    hideHeader = false,
    shellMode = 'default',
    topTabs,
    topTabsActive,
    onTopTabsChange,
    flash = {},
    children,
}) {
    const page = usePage();
    const [plusOpen, setPlusOpen] = useState(false);
    const user = page.props.auth?.user ?? null;

    const section = useMemo(() => sectionFromUrl(page.url), [page.url]);
    const bgTheme = themeFromSection(section);
    const tabs = topTabs?.length
        ? topTabs
        : [
            { label: 'Start', value: 'start' },
            { label: 'My Recent', value: 'recent' },
        ];
    const isOpen = shellMode === 'open';

    return (
        <div className={`board-shell-root ${isOpen ? 'is-open' : ''}`.trim()}>
            <PolygonBackground theme={bgTheme} className="board-polygon" />

            <div className={`board-shell-frame ${isOpen ? 'is-open' : ''}`.trim()}>
                {!hideHeader && (
                    <BoardHeader
                        title={title}
                        subtitle={subtitle}
                        tabs={tabs}
                        activeTab={topTabsActive ?? tabs[0]?.value}
                        onTabChange={onTopTabsChange}
                        user={user}
                    />
                )}

                <section className={`board-shell-content ${isOpen ? 'is-open' : ''}`.trim()}>
                    {(flash.success || flash.error || flash.status) && (
                        <div className="space-y-2">
                            {flash.success && <div className="board-flash board-flash-success">{flash.success}</div>}
                            {flash.error && <div className="board-flash board-flash-error">{flash.error}</div>}
                            {flash.status && <div className="board-flash">{flash.status}</div>}
                        </div>
                    )}

                    {children}
                </section>

                {!hideNavigation && user && <BoardBottomNav onOpenPlus={() => setPlusOpen(true)} />}
            </div>

            {!hideNavigation && user && <PlusDrawer open={plusOpen} onClose={() => setPlusOpen(false)} user={user} />}
        </div>
    );
}
