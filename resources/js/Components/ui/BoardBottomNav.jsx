import { Link, usePage } from '@inertiajs/react';

function HomeIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.9">
            <path d="M3.5 11.3 12 4l8.5 7.3V20H14v-5h-4v5H3.5z" />
        </svg>
    );
}

function ClipsIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.9">
            <rect x="3" y="6" width="18" height="12" rx="2.4" />
            <path d="M9 9v6m6-6v6" />
        </svg>
    );
}

function MatchIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.9">
            <path d="M7 5h10l2.4 5-7.4 9-7.4-9z" />
        </svg>
    );
}

function RankingIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.9">
            <path d="M4 19h16" />
            <path d="M7 16V9m5 7V6m5 10v-4" />
        </svg>
    );
}

const links = [
    { label: 'Home', href: '/dashboard', patterns: ['/dashboard'], Icon: HomeIcon },
    { label: 'Library', href: '/app/clips', patterns: ['/app/clips', '/clips'], Icon: ClipsIcon },
    { label: 'Plans', href: '/app/matches', patterns: ['/app/matches', '/matches'], Icon: MatchIcon },
    { label: 'Profile', href: '/leaderboards', patterns: ['/leaderboards', '/app/leaderboards'], Icon: RankingIcon },
];

function isActive(path, patterns) {
    return patterns.some((pattern) => path === pattern || path.startsWith(`${pattern}/`));
}

export default function BoardBottomNav({ onOpenPlus, hidden = false }) {
    const { url } = usePage();
    const path = url.split('?')[0];

    if (hidden) {
        return null;
    }

    return (
        <nav className="board-bottom-nav" aria-label="Navigation principale">
            <div className="board-bottom-nav-shell">
                <div className="board-bottom-nav-pill">
                    <div className="board-bottom-nav-side">
                        {links.slice(0, 2).map((item) => {
                            const active = isActive(path, item.patterns);
                            return (
                                <Link key={item.href} href={item.href} className={`board-bottom-link ${active ? 'is-active' : ''}`}>
                                    <item.Icon />
                                </Link>
                            );
                        })}
                    </div>

                    <button type="button" className="board-plus-button" onClick={onOpenPlus} aria-label="Ouvrir le menu plus">
                        <svg viewBox="0 0 24 24" className="h-6 w-6" fill="none" stroke="currentColor" strokeWidth="2.4">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>

                    <div className="board-bottom-nav-side">
                        {links.slice(2).map((item) => {
                            const active = isActive(path, item.patterns);
                            return (
                                <Link key={item.href} href={item.href} className={`board-bottom-link ${active ? 'is-active' : ''}`}>
                                    <item.Icon />
                                </Link>
                            );
                        })}
                    </div>
                </div>

                <div className="board-bottom-nav-labels" aria-hidden="true">
                    <span>{links[0].label}</span>
                    <span>{links[1].label}</span>
                    <span>Draw</span>
                    <span>{links[2].label}</span>
                    <span>{links[3].label}</span>
                </div>
            </div>
        </nav>
    );
}
