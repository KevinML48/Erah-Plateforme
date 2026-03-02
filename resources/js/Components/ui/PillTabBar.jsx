import { Link, usePage } from '@inertiajs/react';

function IconHome() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.8">
            <path d="M3.5 11.5L12 4l8.5 7.5V20H14v-5h-4v5H3.5z" />
        </svg>
    );
}

function IconLibrary() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.8">
            <rect x="3" y="5" width="18" height="14" rx="2.5" />
            <path d="M8.5 8v8M12 8v8M15.5 8v8" />
        </svg>
    );
}

function IconMatchs() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.8">
            <path d="M7 5h10l2.5 5-7.5 9-7.5-9z" />
        </svg>
    );
}

function IconClassement() {
    return (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.8">
            <path d="M4 19h16M7 16V9m5 7V6m5 10v-4" />
        </svg>
    );
}

const tabs = [
    { label: 'Home', href: '/dashboard', matches: ['/dashboard'], Icon: IconHome },
    { label: 'Clips', href: '/app/clips', matches: ['/app/clips', '/clips'], Icon: IconLibrary },
    { label: 'Matchs', href: '/app/matches', matches: ['/app/matches', '/matches'], Icon: IconMatchs },
    { label: 'Classement', href: '/leaderboards', matches: ['/leaderboards', '/app/leaderboards'], Icon: IconClassement },
];

export default function PillTabBar({ onOpenPlus, hidden = false }) {
    const { url } = usePage();
    const currentPath = url.split('?')[0];

    if (hidden) {
        return null;
    }

    return (
        <nav className="toy-bottombar" aria-label="Navigation principale">
            <div className="toy-bottombar-inner">
                {tabs.slice(0, 2).map(({ href, label, matches, Icon }) => {
                    const active = matches.some((match) => currentPath === match || currentPath.startsWith(`${match}/`));
                    return (
                        <Link key={href} href={href} className={`toy-nav-item ${active ? 'active' : ''}`}>
                            <Icon />
                            <span>{label}</span>
                        </Link>
                    );
                })}

                <button type="button" onClick={onOpenPlus} className="toy-nav-plus" aria-label="Ouvrir menu plus">
                    <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                </button>

                {tabs.slice(2).map(({ href, label, matches, Icon }) => {
                    const active = matches.some((match) => currentPath === match || currentPath.startsWith(`${match}/`));
                    return (
                        <Link key={href} href={href} className={`toy-nav-item ${active ? 'active' : ''}`}>
                            <Icon />
                            <span>{label}</span>
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}

