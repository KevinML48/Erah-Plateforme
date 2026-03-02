import EmptyTile from '../Components/ui/EmptyTile';
import HeroTile from '../Components/ui/HeroTile';
import MediaTile from '../Components/ui/MediaTile';
import PillButton from '../Components/ui/PillButton';
import PillTabs from '../Components/ui/PillTabs';
import StatPill from '../Components/ui/StatPill';
import Tile from '../Components/ui/Tile';
import GameLayout from '../Layouts/GameLayout';

export default function Styleboard() {
    return (
        <GameLayout title="Styleboard" subtitle="ToyCAD x ERAH visual board" topTabs={[{ label: 'Start', value: 'start' }, { label: 'My Recent', value: 'recent' }]} topTabsActive="start">
            <Tile title="Background variants" subtitle="Polygon layers">
                <div className="grid gap-3 md:grid-cols-3">
                    <div className="bg-geo-home h-28 overflow-hidden rounded-hud border border-ui-border/20" />
                    <div className="bg-geo-library h-28 overflow-hidden rounded-hud border border-ui-border/20" />
                    <div className="bg-geo-profile h-28 overflow-hidden rounded-hud border border-ui-border/20" />
                </div>
            </Tile>

            <div className="toy-grid toy-grid-hero">
                <HeroTile title="Get Started" description="Grande tuile hero comme reference ToyCAD." ctaLabel="Primary CTA" variant="light" />

                <Tile title="Pills" subtitle="Tabs / badges / buttons">
                    <div className="space-y-3">
                        <PillTabs items={[{ label: 'ToyCAD Library', value: 'a' }, { label: 'My Library', value: 'b' }]} active="a" />
                        <div className="flex flex-wrap gap-2">
                            <PillButton>Primary</PillButton>
                            <PillButton variant="secondary">Secondary</PillButton>
                            <PillButton variant="ghost">Ghost</PillButton>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <StatPill variant="new">NEW</StatPill>
                            <StatPill variant="warning">LIVE</StatPill>
                            <StatPill variant="dark">STATUS</StatPill>
                        </div>
                    </div>
                </Tile>
            </div>

            <div className="toy-grid toy-grid-3">
                <MediaTile title="Match-up card" meta="ERAH Falcons vs Midnight Owls" />
                <MediaTile title="Leaderboard row card" meta="#1 Maya Nova" />
                <Tile title="Empty tile">
                    <EmptyTile title="Aucune donnee" description="Etat vide standard de l'app." />
                </Tile>
            </div>
        </GameLayout>
    );
}
