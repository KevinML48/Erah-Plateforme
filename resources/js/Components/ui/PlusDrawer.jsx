import { Link, router } from '@inertiajs/react';

import Tile from './Tile';
import PillButton from './PillButton';

const commonLinks = [
    { label: 'Paris', href: '/app/bets' },
    { label: 'Wallet', href: '/app/wallet' },
    { label: 'Duels', href: '/app/duels' },
    { label: 'Missions', href: '/app/missions' },
    { label: 'Cadeaux', href: '/app/gifts' },
    { label: 'Notifications', href: '/app/notifications' },
    { label: 'Settings', href: '/app/settings' },
];

const adminLinks = [
    { label: 'Admin Clips', href: '/app/admin/clips' },
    { label: 'Admin Matchs', href: '/app/admin/matches' },
    { label: 'Admin Wallets', href: '/app/admin/wallets' },
];

export default function PlusDrawer({ open, onClose, user }) {
    if (!open) {
        return null;
    }

    const links = user?.role === 'admin' ? [...commonLinks, ...adminLinks] : commonLinks;

    return (
        <div className="toy-drawer">
            <button type="button" className="absolute inset-0 h-full w-full cursor-default" onClick={onClose} aria-label="Fermer le menu plus" />

            <section className="toy-drawer-sheet">
                <header className="mb-3 flex items-center justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.14em] text-ui-muted">Quick access</p>
                        <h2 className="font-display text-xl font-bold">Menu plus</h2>
                    </div>
                    <PillButton variant="secondary" className="h-9 w-9 p-0" onClick={onClose}>
                        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </PillButton>
                </header>

                <div className="grid grid-cols-2 gap-2">
                    {links.map((link) => (
                        <Link key={link.href} href={link.href} onClick={onClose}>
                            <Tile size="s" className="px-3 py-3">
                                <p className="font-semibold">{link.label}</p>
                            </Tile>
                        </Link>
                    ))}
                </div>

                <div className="mt-3">
                    <PillButton
                        variant="secondary"
                        className="w-full"
                        onClick={() => {
                            onClose();
                            router.post('/logout');
                        }}
                    >
                        Logout
                    </PillButton>
                </div>
            </section>
        </div>
    );
}

