import { Head, usePage } from '@inertiajs/react';
import { useMemo } from 'react';

import BoardShell from '../Components/ui/BoardShell';

const titleFromPath = (path) => {
    const segments = path.split('?')[0].split('/').filter(Boolean);
    const section = segments[0] === 'app' ? (segments[1] ?? 'dashboard') : (segments[0] ?? 'dashboard');

    const map = {
        dashboard: 'Dashboard',
        clips: 'Clips',
        matches: 'Matchs',
        leaderboards: 'Classement',
        notifications: 'Notifications',
        bets: 'Mes Paris',
        wallet: 'Wallet',
        duels: 'Duels',
        missions: 'Missions',
        gifts: 'Cadeaux',
        settings: 'Settings',
        onboarding: 'Onboarding',
        'ui-kit': 'UI Kit',
        styleboard: 'Styleboard',
    };

    return {
        title: map[section] ?? section.charAt(0).toUpperCase() + section.slice(1),
        section,
    };
};

export default function GameLayout({
    title,
    subtitle,
    hideNavigation = false,
    hideHeader = false,
    shellMode = 'default',
    topTabs,
    topTabsActive,
    onTopTabsChange,
    children,
}) {
    const page = usePage();
    const flash = page.props.flash ?? {};

    const computed = useMemo(() => titleFromPath(page.url), [page.url]);
    const finalTitle = title ?? computed.title;

    return (
        <>
            <Head title={finalTitle} />
            <BoardShell
                title={finalTitle}
                subtitle={subtitle}
                hideNavigation={hideNavigation}
                hideHeader={hideHeader}
                shellMode={shellMode}
                flash={flash}
                topTabs={topTabs}
                topTabsActive={topTabsActive}
                onTopTabsChange={onTopTabsChange}
            >
                {children}
            </BoardShell>
        </>
    );
}
