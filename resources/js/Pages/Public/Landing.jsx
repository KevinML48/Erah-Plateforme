import { Link } from '@inertiajs/react';
import { useState } from 'react';

import HeroTile from '../../Components/ui/HeroTile';
import MediaTile from '../../Components/ui/MediaTile';
import PillButton from '../../Components/ui/PillButton';
import PillTabs from '../../Components/ui/PillTabs';
import StatPill from '../../Components/ui/StatPill';
import Tile from '../../Components/ui/Tile';
import GameLayout from '../../Layouts/GameLayout';

export default function Landing() {
    const [activeTab, setActiveTab] = useState('start');

    const tabItems = [
        { label: 'Start', value: 'start' },
        { label: 'My Recent', value: 'recent' },
    ];

    return (
        <GameLayout title="Arena ERAH" subtitle="Entrer dans l'interface communautaire" topTabs={tabItems} topTabsActive={activeTab} onTopTabsChange={setActiveTab}>
            <div className="toy-grid toy-grid-hero">
                <HeroTile
                    title="Entrer dans l'Arena ERAH"
                    description="Parie avant le lock, monte en ligue, reponds aux duels et fais vivre les clips communautaires."
                    ctaLabel="Ouvrir Dashboard"
                    ctaHref="/dashboard"
                    secondaryAction={
                        <Link href="/register">
                            <PillButton variant="secondary">Creer un compte</PillButton>
                        </Link>
                    }
                    variant="light"
                >
                    <div className="mt-2 flex flex-wrap gap-2">
                        <a href="/auth/google/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                            Google
                        </a>
                        <a href="/auth/discord/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                            Discord
                        </a>
                        <StatPill variant="new">ERAH LIVE</StatPill>
                    </div>
                </HeroTile>

                <Tile title="Pulse Arena" subtitle="Snapshot live" size="l">
                    <div className="space-y-3">
                        <div className="rounded-hud border border-ui-border/16 bg-ui-surface/92 p-3">
                            <p className="text-xs uppercase tracking-[0.1em] text-ui-muted">Clips publies</p>
                            <p className="mt-1 font-display text-5xl font-bold">6</p>
                            <p className="text-xs text-ui-muted">Highlights valides</p>
                        </div>
                        <div className="rounded-hud border border-ui-border/16 bg-ui-surface/92 p-3">
                            <p className="text-xs uppercase tracking-[0.1em] text-ui-muted">Duels pending</p>
                            <p className="mt-1 font-display text-5xl font-bold">1</p>
                            <p className="text-xs text-ui-muted">Defis en attente</p>
                        </div>
                    </div>
                </Tile>
            </div>

            <div className="toy-grid toy-grid-hero">
                <Tile title="Clips recents" subtitle="Feed public" size="m">
                    <div className="space-y-2">
                        <MediaTile title="Ace clutch en finale" meta="1 day ago" className="p-0" />
                        <MediaTile title="Retake au millimetre" meta="2 days ago" className="p-0" />
                        <MediaTile title="Rush mid lightning" meta="3 days ago" className="p-0" />
                    </div>
                </Tile>

                <Tile title="Top ligue" subtitle="Leaderboard rapide" size="m">
                    <div className="space-y-2">
                        <StatPill variant="warning">Bronze</StatPill>
                        <div className="rounded-hud border border-ui-border/16 bg-ui-surface/92 px-3 py-2">
                            <div className="flex items-center justify-between text-sm">
                                <span>#1 Maya Nova</span>
                                <span className="font-bold">80 pts</span>
                            </div>
                        </div>
                        <div className="rounded-hud border border-ui-border/16 bg-ui-surface/92 px-3 py-2">
                            <div className="flex items-center justify-between text-sm">
                                <span>#2 Kevin Molines</span>
                                <span className="font-bold">0 pts</span>
                            </div>
                        </div>
                    </div>
                </Tile>
            </div>
        </GameLayout>
    );
}

