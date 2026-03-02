import { Link } from '@inertiajs/react';

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

export default function Topbar({ title, user, hideActions = false }) {
    return (
        <header className="sticky top-0 z-40 border-b border-erah-border/10 bg-erah-bg/85 backdrop-blur">
            <div className="relative z-10 mx-auto flex h-16 w-full max-w-5xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div className="min-w-0">
                    <p className="font-display text-[0.65rem] uppercase tracking-[0.25em] text-muted">ERAH ARENA</p>
                    <h1 className="truncate font-display text-lg font-bold tracking-wide sm:text-xl">{title}</h1>
                </div>

                {!hideActions && (
                    <div className="flex items-center gap-2">
                        {user && (
                            <Link href="/app/notifications" className="hud-btn-secondary p-2.5" aria-label="Notifications">
                                <IconBell />
                            </Link>
                        )}
                        {user && (
                            <Link href="/profile" className="hud-btn-secondary gap-2 px-3 py-2 text-xs" aria-label="Profil">
                                <IconUser />
                                <span className="hidden sm:inline">{user.name}</span>
                            </Link>
                        )}
                    </div>
                )}
            </div>
        </header>
    );
}
