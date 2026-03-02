import IconCircleButton from './IconCircleButton';

function BellIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="1.9">
            <path d="M14.8 17H5.2a1 1 0 0 1-.8-1.6L5.6 14V10a6.4 6.4 0 1 1 12.8 0v4l1.2 1.4a1 1 0 0 1-.8 1.6h-4z" />
            <path d="M9.5 19a2.5 2.5 0 0 0 5 0" />
        </svg>
    );
}

function SearchIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="1.9">
            <circle cx="11" cy="11" r="6" />
            <path d="m20 20-3.3-3.3" />
        </svg>
    );
}

function UserIcon() {
    return (
        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="1.9">
            <circle cx="12" cy="8" r="3.2" />
            <path d="M5.2 18.5a6.8 6.8 0 0 1 13.6 0" />
        </svg>
    );
}

export default function BoardActions({ user }) {
    return (
        <div className="board-header-actions">
            <IconCircleButton href="/app/notifications" label="Notifications">
                <BellIcon />
            </IconCircleButton>
            <IconCircleButton href="/app/matches" label="Rechercher">
                <SearchIcon />
            </IconCircleButton>
            <IconCircleButton href={user ? '/profile' : '/login'} label="Profil">
                <UserIcon />
            </IconCircleButton>
        </div>
    );
}
